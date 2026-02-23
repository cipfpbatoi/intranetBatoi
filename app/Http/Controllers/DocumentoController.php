<?php

namespace Intranet\Http\Controllers;

use Intranet\Application\AlumnoFct\AlumnoFctService;
use Intranet\Application\Documento\DocumentoFormService;
use Intranet\Application\Documento\DocumentoLifecycleService;
use Intranet\Application\Grupo\GrupoService;
use Intranet\Application\Profesor\ProfesorService;
use Intranet\Http\Controllers\Core\IntranetController;


use Illuminate\Http\Request;
use Intranet\Entities\Adjunto;
use Intranet\Entities\Documento;
use Intranet\Presentation\Crud\DocumentoCrudSchema;
use Intranet\Services\Document\TipoDocumentoService;
use Intranet\Services\UI\FormBuilder;
use Intranet\Services\General\GestorService;
use Intranet\Services\Document\CreateOrUpdateDocumentAction;
use Illuminate\Support\Facades\Session;
use Styde\Html\Facades\Alert;


class DocumentoController extends IntranetController
{
    private ?DocumentoFormService $documentoFormService = null;
    private ?GrupoService $grupoService = null;
    private ?AlumnoFctService $alumnoFctService = null;
    private ?DocumentoLifecycleService $documentoLifecycleService = null;

    protected $model = 'Documento';
    protected $formFields = DocumentoCrudSchema::FORM_FIELDS;

    public function __construct(
        ?GrupoService $grupoService = null,
        ?AlumnoFctService $alumnoFctService = null,
        ?DocumentoLifecycleService $documentoLifecycleService = null
    )
    {
        parent::__construct();
        $this->grupoService = $grupoService;
        $this->alumnoFctService = $alumnoFctService;
        $this->documentoLifecycleService = $documentoLifecycleService;
    }

    private function grupos(): GrupoService
    {
        if ($this->grupoService === null) {
            $this->grupoService = app(GrupoService::class);
        }

        return $this->grupoService;
    }

    private function alumnoFcts(): AlumnoFctService
    {
        if ($this->alumnoFctService === null) {
            $this->alumnoFctService = app(AlumnoFctService::class);
        }

        return $this->alumnoFctService;
    }

    private function documentos(): DocumentoLifecycleService
    {
        if ($this->documentoLifecycleService === null) {
            $this->documentoLifecycleService = app(DocumentoLifecycleService::class);
        }

        return $this->documentoLifecycleService;
    }

    private function forms(): DocumentoFormService
    {
        if ($this->documentoFormService === null) {
            $this->documentoFormService = app(DocumentoFormService::class);
        }

        return $this->documentoFormService;
    }


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
            $this->forms()->updateNota($this->alumnoFcts(), (int) $fct, $request->nota);
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
    protected function createWithDefaultValues($default=[])
    {
        return (new CreateOrUpdateDocumentAction())->build(
            array_merge(['curso'=>Curso(),'propietario'=>config('contacto.titulo'),'activo'=>true], $default)
        );
     }

    public function project($idFct)
    {
        if ($fct = $this->alumnoFcts()->findOrFail((int) $idFct)) {
            $grupoTutor = $this->grupos()->firstByTutor(AuthUser()->dni);
            $ciclo = $grupoTutor?->Ciclo?->ciclo ?? '';
            $elemento = (new CreateOrUpdateDocumentAction())->build(
                $this->forms()->projectDefaults($fct, $ciclo, AuthUser()->FullName)
            );
            $formulario = new FormBuilder(
                $elemento,
                DocumentoCrudSchema::projectFormFields()
            );
            $modelo = $this->model;
            Session::put('redirect', 'PanelFctAvalController@index');
            return view($this->chooseView('create'), compact('formulario', 'modelo'));
        }
        return back();

    }

    public function qualitatUpload($id)
    {
        $profesor = app(ProfesorService::class)->findOrFail((string) $id);

        $documents = Adjunto::where('route', "profesor/$id")->get();
        $grupo = $this->grupos()->firstByTutor((string) $id);
        if (!$grupo) {
            Alert::danger('No hi ha grup de tutoria assignat');
            return back();
        }
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
        $grupo = $this->grupos()->firstByTutor(AuthUser()->dni);
        if (!$grupo) {
            Alert::danger('No hi ha grup de tutoria assignat');
            return back();
        }
        $elemento = (new CreateOrUpdateDocumentAction())->build([
            ...$this->forms()->qualitatDefaults($grupo, AuthUser()->FullName),
        ]);
        $formulario = new FormBuilder(
            $elemento,
            DocumentoCrudSchema::qualitatFormFields()
        );
        $modelo = $this->model;
        Session::put('redirect', 'FctController@index');
        return view($this->chooseView('create'), compact('formulario', 'modelo'));
    }

    public function edit($id = null)
    {
        $elemento = Documento::findOrFail($id);
        $formulario = new FormBuilder($elemento, DocumentoCrudSchema::editFormFields((bool) $elemento->enlace));
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
        $this->documentos()->delete($borrar);
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
