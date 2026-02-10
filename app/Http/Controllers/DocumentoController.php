<?php

namespace Intranet\Http\Controllers;


use Illuminate\Http\Request;
use Intranet\Entities\Adjunto;
use Intranet\Entities\Documento;
use Intranet\Entities\Profesor;
use Intranet\Services\Document\TipoDocumentoService;
use Intranet\Entities\Grupo;
use Intranet\Entities\AlumnoFct;
use Intranet\Services\UI\FormBuilder;
use Intranet\Services\General\GestorService;
use Intranet\Services\Document\CreateOrUpdateDocumentAction;
use Illuminate\Support\Facades\Session;
use Styde\Html\Facades\Alert;


class DocumentoController extends IntranetController
{

    protected $model = 'Documento';
    protected $formFields = ['tipoDocumento' => ['type' => 'select'],
        'rol' => ['type' => 'hidden'],
        'propietario' => ['disabled' => 'disabled'],
        'grupo' => ['type' => 'select'],
        'supervisor' => ['type' => 'hidden'],
        'ciclo' => ['type' => 'hidden'],
        'detalle' => ['type' => 'textarea'],
        'curso' => ['disabled'=> 'disabled'],
        'descripcion' => ['type' => 'text'],
        'enlace' => ['type' => 'text'],
        'fichero' => ['type' => 'file'],
        'activo' => ['type' => 'checkbox'],
        'tags' => ['type' => 'tag', 'params' => ['class' => 'tags']],
    ];


    protected function redirect()
    {
        if (Session::get('redirect')) {
            return redirect()->action(Session::get('redirect'));
        }

        return redirect()->route('documento.index');
    }


 

    public function store(Request $request, $fct = null)
    {
       
        if ($request->has('nota') && $this->validate($request, ['nota' => 'numeric|min:1|max:11'])) {
            $this->saveNota($request->nota, $fct);
            if ($request->nota < 5) {
                return $this->redirect();
            }
        }

        $except = ['nota'];
        $rol = TipoDocumentoService::rol($request->input('tipoDocumento'));
        $cursoRequest = $request->input('curso')??curso();
        $cleanRequest = $request->duplicate(
            $request->except($except),
            $request->files->all()
        );

        (new CreateOrUpdateDocumentAction())->fromRequest(
            $cleanRequest,
            [
                'rol' => $rol,
                'curso' => $cursoRequest,
            ]
        );

        return $this->redirect();
    }



    private function saveNota($nota, $fct)
    {
        $fctAl = AlumnoFct::findOrFail($fct);
        $fctAl->calProyecto = $nota;
        if ($fctAl->calificacion < 1) {
            $fctAl->calificacion = 1;
        }
        $fctAl->save();
    }

    protected function createWithDefaultValues($default=[])
    {
        return (new CreateOrUpdateDocumentAction())->build(
            array_merge(['curso'=>Curso(),'propietario'=>config('contacto.titulo'),'activo'=>true], $default)
        );
     }

    public function project($idFct)
    {
        if ($fct = AlumnoFct::findOrFail($idFct)) {

            $proyecto = $fct->Alumno->Projecte ?? null;
            $descripcion = $proyecto->titol ?? '';
            $detalle = $proyecto->descripcio ?? '';
            $elemento = (new CreateOrUpdateDocumentAction())->build([
                'curso' => Curso(),
                'propietario' => $fct->Alumno->FullName,
                'supervisor' => AuthUser()->FullName,
                'activo' => true,
                'tipoDocumento' => 'Proyecto',
                'idDocumento' => '',
                'ciclo' => Grupo::QTutor(AuthUser()->dni)->first()->Ciclo->ciclo,
                'descripcion' => $descripcion,
                'detalle' => $detalle,
            ]);
            $formulario = new FormBuilder(
                $elemento,
                [
                    'tipoDocumento' => ['disabled' => 'disabled'],
                    'propietario' => ['disabled' => 'disabled'],
                    'curso' => ['disabled'=> 'disabled'],
                    'supervisor' => ['type' => 'hidden'],
                    'ciclo' => ['type' => 'hidden'],
                    'descripcion' => ['type' => 'text'],
                    'detalle' => ['type' => 'textarea'],
                    'nota' => ['type' => 'text'],
                    'fichero' => ['type' => 'file'],
                    'tags' => ['type' => 'tag', 'params' => ['class' => 'tags']]
                ]
            );
            $modelo = $this->model;
            Session::put('redirect', 'PanelFctAvalController@index');
            return view($this->chooseView('create'), compact('formulario', 'modelo'));
        }
        return back();

    }

    public function qualitatUpload($id)
    {
        $profesor = Profesor::findOrFail($id);

        $documents = Adjunto::where('route', "profesor/$id")->get();
        $grupo = Grupo::QTutor($id)->first();
        $elemento = (new CreateOrUpdateDocumentAction())->fromArray([
            'curso' => Curso(),
            'propietario' => $profesor->FullName,
            'supervisor' => $profesor->FullName,
            'activo' => true,
            'tipoDocumento' => 'FCT',
            'idDocumento' => null,
            'ciclo' => $grupo->Ciclo->ciclo,
            'grupo' => $grupo->nombre,
            'tags' => 'Fct,Entrevista,Alumnat,Instructor',
            'descripcion' => "Documentació FCT Cicle " . $grupo->Ciclo->ciclo,
        ]);

        $zip = new \ZipArchive();
        $path = "gestor/" . curso() . "/FCT/";
        $zipFile = $path . $elemento->id . "_FCT.zip";
        $elemento->fichero = $zipFile;

        // Comprovar si el directori existeix, si no, crear-lo
        $storagePath = storage_path('app/' . $path);
        if (!file_exists($storagePath)) {
            if (!mkdir($storagePath, 0777, true) && !is_dir($storagePath)) {
                throw new \RuntimeException(sprintf('Directory "%s" was not created', $storagePath));
            }
        }

        $zip->open($storagePath . $elemento->id . "_FCT.zip", \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
        $problem = false;
        $esborrar = [];
        foreach ($documents as $document) {
            $file = public_path("storage/adjuntos/{$document->route}/{$document->title}.{$document->extension}");
            if (file_exists($file)) {
                $zip->addFile($file, $document->name);
                $esborrar[$document->id] = $file;
            } else {
                $problem = true;
                Alert::danger("Problemes per a guardar el fitxer: $file");
            }
        }
        if (!$problem) {
            $zip->close();
            $elemento->save();
            foreach ($esborrar as $adjunto => $file) {
                Adjunto::destroy($adjunto);
                unlink($file);
            }
            return redirect('/alumnofct');
        }
        $elemento->delete();
        return back();
    }


    public function qualitat()
    {
        $grupo = Grupo::QTutor(AuthUser()->dni)->first();
        $elemento = (new CreateOrUpdateDocumentAction())->build([
            'curso' => Curso(),
            'propietario' => AuthUser()->FullName,
            'supervisor' => AuthUser()->FullName,
            'activo' => true,
            'tipoDocumento' => 'FCT',
            'idDocumento' => '',
            'ciclo' => $grupo->Ciclo->ciclo,
            'grupo' => $grupo->nombre,
            'tags' => 'Fct,Entrevista,Alumnat,Instructor',
            'instrucciones' => 'Pujar en un sols document comprimit: Entrevista Alumnat i Entrevista Instructor',
        ]);
        $formulario = new FormBuilder(
            $elemento,
            [
                'tipoDocumento' => ['disabled' => 'disabled'],
                'instrucciones' => ['disabled' => 'disabled'],
                'curso' => ['disabled'=> 'disabled'],
                'rol' => ['type' => 'hidden'],
                'propietario' => ['disabled' => 'disabled'],
                'grupo' => ['disabled' => 'disabled'],
                'supervisor' => ['type' => 'hidden'],
                'ciclo' => ['type' => 'hidden'],
                'detalle' => ['type' => 'textarea'],
                'descripcion' => ['type' => 'text'],
                'fichero' => ['type' => 'file'],
                'tags' => ['type' => 'tag', 'params' => ['class' => 'tags']]
            ]
        );
        $modelo = $this->model;
        Session::put('redirect', 'FctController@index');
        return view($this->chooseView('create'), compact('formulario', 'modelo'));
    }

    public function edit($id = null)
    {
        $elemento = Documento::findOrFail($id);
        $formulario = $elemento->enlace?
            new FormBuilder(
                $elemento,
                [
                    'tipoDocumento' => ['type' => 'select'],
                    'propietario' => ['disabled' => 'disabled'],
                    'supervisor' => ['type' => 'hidden'],
                    'rol' => ['type' => 'hidden'],
                    'ciclo' => ['type' => 'hidden'],
                    'detalle' => ['type' => 'textarea'],
                    'curso' => ['disabled' => 'disabled'],
                    'grupo' => ['disabled' => 'disabled'],
                    'descripcion' => ['type' => 'text'],
                    'enlace' => ['type' => 'text'],
                    'activo' => ['type' => 'checkbox'],
                    'tags' => ['type' => 'tag', 'params' => ['class' => 'tags']]
                ]
            ):
            new FormBuilder(
                $elemento,
                [
                    'tipoDocumento' => ['type' => 'select'],
                    'propietario' => ['disabled' => 'disabled'],
                    'rol' => ['type' => 'hidden'],
                    'supervisor' => ['type' => 'hidden'],
                    'detalle' => ['type' => 'textarea'],
                    'curso' => ['disabled' => 'disabled'],
                    'grupo' => ['disabled' => 'disabled'],
                    'ciclo' => ['type' => 'hidden'],
                    'descripcion' => ['type' => 'text'],
                    'fichero' => ['type' => 'file'],
                    'activo' => ['type' => 'checkbox'],
                    'tags' => ['type' => 'tag', 'params' => ['class' => 'tags']]
                ]
            );
        $modelo = $this->model;
        return view($this->chooseView('edit'), compact('formulario', 'modelo'));
    }
    
    public function show($id)
    {
        $gestor = new GestorService(null, Documento::find($id));
        return $gestor->render();
    }

    public function destroy($id)
    {
        $borrar = Documento::findOrFail($id);
        if ($borrar) {
            if ($borrar->link && !$borrar->exist) {
                unlink(storage_path('app/' . $borrar->fichero));
            }
            $borrar->delete();
        }
        return $this->redirect();
    }

    public function readFile($name)
    {
        $adjunto = Adjunto::where('name', $name)->first();
        if ($adjunto) {
            return redirect("/storage/adjuntos/".$adjunto->route."/".$adjunto->title.".".$adjunto->extension);
        } else {
            return back();
        }
    }

    

}
