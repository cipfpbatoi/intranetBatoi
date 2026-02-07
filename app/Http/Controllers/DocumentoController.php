<?php

namespace Intranet\Http\Controllers;


use Illuminate\Http\Request;
use DB;
use Intranet\UI\Botones\BotonImg;
use Intranet\Entities\Adjunto;
use Intranet\Entities\Documento;
use Intranet\Entities\Profesor;
use Intranet\Entities\TipoDocumento;
use Intranet\Entities\Grupo;
use Intranet\Entities\AlumnoFct;
use Intranet\Services\FormBuilder;
use Intranet\Services\GestorService;
use Intranet\Services\Document\CreateOrUpdateDocumentAction;
use Illuminate\Support\Facades\Session;
use Styde\Html\Facades\Alert;
use function Symfony\Component\String\s;


class DocumentoController extends IntranetController
{

    protected $gridFields = ['tipoDocumento', 'descripcion', 'curso', 'idDocumento', 'propietario', 'created_at',
        'grupo', 'tags', 'ciclo', 'modulo','detalle','fichero'
    //    ,'situacion'
    ];
    protected $model = 'Documento';
    protected $directorio = '/Ficheros/';
    protected $panel;
    protected $modal = false;
    protected $profile = false;
    protected int $perPage = 50;
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


    public function index(){
        ini_set('memory_limit', '512M');
        // Opcions de filtres (tipus i cursos disponibles)
        $configTipos = TipoDocumento::allPestana();
        $bdTipos = Documento::select('tipoDocumento')
            ->distinct()
            ->orderBy('tipoDocumento')
            ->pluck('tipoDocumento')
            ->filter();

        $tipoOptions = [];
        foreach ($configTipos as $key => $label) {
            $tipoOptions[$key] = $label;
        }
        foreach ($bdTipos as $tipo) {
            $tipoOptions[$tipo] = $configTipos[$tipo] ?? $tipo;
        }

        $this->panel->filterTipoOptions = $tipoOptions;
        $this->panel->filterCursoOptions = Documento::select('curso')
            ->distinct()
            ->orderBy('curso', 'desc')
            ->limit(8)
            ->pluck('curso');
        $this->panel->filterPropietario = true;
        $this->panel->filterTags = true;
        return parent::index();
    }
    public function search()
    {
        $query = Documento::query()
            ->select([
                'id',
                'tipoDocumento',
                'descripcion',
                'curso',
                'idDocumento',
                'propietario',
                'created_at',
                'grupo',
                'tags',
                'ciclo',
                'modulo',
                'detalle',
                'fichero',
                'rol',
                'activo',
            ])
            ->orderBy('curso', 'desc');
        $search = request('search');
        $filterTipo = request('tipoDocumento');
        $filterCurso = request('curso');
        $filterPropietario = request('propietario');
        $filterTags = request('tags');

        if (Session::get('completa')) {
            $query->whereIn('rol', RolesUser(AuthUser()->rol));
        } else {
            // Quan no es mostra la llista completa, acotem per curs o per propietari
            $query->where(function ($q) {
                $q->where(function ($sub) {
                    $sub->where('curso', Curso())
                        ->whereIn('rol', RolesUser(AuthUser()->rol));
                })->orWhere('propietario', AuthUser()->fullName);
            });
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('descripcion', 'like', "%{$search}%")
                    ->orWhere('tags', 'like', "%{$search}%")
                    ->orWhere('propietario', 'like', "%{$search}%")
                    ->orWhere('tipoDocumento', 'like', "%{$search}%");
            });
        }

        if ($filterTipo) {
            $query->where('tipoDocumento', $filterTipo);
        }

        if ($filterCurso) {
            $query->where('curso', $filterCurso);
        }

        if ($filterPropietario) {
            $query->where('propietario', 'like', "%{$filterPropietario}%");
        }

        if ($filterTags) {
            $query->where('tags', 'like', "%{$filterTags}%");
        }

        return $query->paginate($this->perPage)->appends(request()->query());

    }

    protected function iniBotones()
    {
        $this->panel->setBothBoton(
            'documento.show',
            ['where' => ['rol', 'in', RolesUser(AuthUser()->rol),'link','==',1]]
        );
        $this->panel->setBoton('grid', new BotonImg('documento.delete', ['roles' => config('roles.rol.direccion')]));
        $this->panel->setBoton('grid', new BotonImg('documento.edit', ['roles' => config('roles.rol.direccion')]));
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
        $rol = TipoDocumento::rol($request->input('tipoDocumento'));
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
