<?php

namespace Intranet\Http\Controllers;


use Illuminate\Http\Request;
use DB;
use Intranet\Botones\BotonImg;
use Intranet\Entities\Documento;
use Intranet\Entities\TipoDocumento;
use Intranet\Entities\Grupo;
use Intranet\Entities\AlumnoFct;
use Intranet\Services\FormBuilder;
use Intranet\Services\Gestor;
use Illuminate\Support\Facades\Session;

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
        'tags' => ['type' => 'tag', 'params' => ['class' => 'tags']],
    ];



    public function search()
    {
        if (Session::get('completa')) {
            return Documento::whereIn('rol', RolesUser(AuthUser()->rol))->orderBy('curso', 'desc')->get();
        } else {
            return Documento::where('curso',Curso())->whereIn('rol', RolesUser(AuthUser()->rol))->orWhere('propietario',AuthUser()->fullName)
                ->orderBy('curso','desc')->get();
        }
    }

    protected function iniBotones()
    {
        $this->panel->setBothBoton('documento.show', ['where' => ['rol', 'in', RolesUser(AuthUser()->rol),'link','==',1]]);
        $this->panel->setBoton('grid', new BotonImg('documento.delete', ['roles' => config('roles.rol.direccion')]));
        $this->panel->setBoton('grid', new BotonImg('documento.edit', ['roles' => config('roles.rol.direccion')]));
    }



    public function store(Request $request, $fct = null)
    {
        $except = ['nota'];
        if ($request->has('nota') && $this->validate($request,['nota' => 'numeric|min:1|max:10'])) {
            $this->saveNota($request->nota,$fct);
            if ($request->nota < 5){
                return $this->redirect();
            }
        }
        return parent::store(subsRequest($request->duplicate(null,$request->except($except)), ['rol' => TipoDocumento::rol($request->tipoDocumento)]));
    }

    private function saveNota($nota,$fct){
        $fctAl = AlumnoFct::findOrFail($fct);
        $fctAl->calProyecto = $nota;
        if ($fctAl->calificacion < 1) {
            $fctAl->calificacion = 1;
        }
        $fctAl->save();
    }

    protected function createWithDefaultValues($default=[]){
        return new Documento(['curso'=>Curso(),'propietario'=>config('contacto.titulo')]);
     }

    public function project($idFct)
    {   
        if ($fct = AlumnoFct::findOrFail($idFct)) {
            
            $elemento = $this->createWithDefaultValues();
            $elemento->supervisor = AuthUser()->FullName;
            $elemento->propietario = $fct->Alumno->FullName;
            $elemento->tipoDocumento = 'Proyecto';
            $elemento->idDocumento = '';
            $elemento->ciclo = Grupo::QTutor(AuthUser()->dni)->first()->Ciclo->ciclo;
            $formulario = new FormBuilder($elemento,['tipoDocumento' => ['disabled' => 'disabled'],
                'propietario' => ['disabled' => 'disabled'],
                'curso' => ['disabled'=> 'disabled'],
                'supervisor' => ['type' => 'hidden'],
                'ciclo' => ['type' => 'hidden'],
                'descripcion' => ['type' => 'text'],
                'detalle' => ['type' => 'textarea'],
                'nota' => ['type' => 'text'],
                'fichero' => ['type' => 'file'],
                'tags' => ['type' => 'tag', 'params' => ['class' => 'tags']],
            ]);
            $modelo = $this->model;
            Session::put('redirect', 'PanelFctAvalController@index');
            return view($this->chooseView('create'), compact('formulario', 'modelo'));
        } else {
            return back();
        }
    }
    
    public function qualitat()
    {
        $elemento = $this->createWithDefaultValues();
        $elemento->tipoDocumento = 'FCT';
        $elemento->idDocumento = '';
        $grupo = Grupo::QTutor(AuthUser()->dni)->first();
        $elemento->ciclo = $grupo->Ciclo->ciclo;
        $elemento->grupo = $grupo->nombre;
        $elemento->supervisor = AuthUser()->FullName;
        $elemento->propietario = $elemento->supervisor;
        $elemento->tags = 'Fct,Entrevista,Alumnat,Instructor';
        $elemento->instrucciones = 'Pujar en un sols document comprimit: Entrevista Alumnat i Entrevista Instructor';
        $formulario = new FormBuilder($elemento,['tipoDocumento' => ['disabled' => 'disabled'],
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
            'tags' => ['type' => 'tag', 'params' => ['class' => 'tags']],
        ]);
        $modelo = $this->model;
        Session::put('redirect', 'FctController@index');
        return view($this->chooseView('create'), compact('formulario', 'modelo'));
    }

    public function edit($id)
    {
        $elemento = Documento::findOrFail($id);
        $formulario = $elemento->enlace?
            new FormBuilder($elemento, ['tipoDocumento' => ['disabled' => 'disabled'],
                'propietario' => ['disabled' => 'disabled'],
                'rol' => ['type' => 'hidden'],
                'detalle' => ['type' => 'textarea'],
                'curso' => ['disabled' => 'disabled'],
                'grupo' => ['disabled' => 'disabled'],
                'descripcion' => ['type' => 'text'],
                'enlace' => ['type' => 'text'],
                'tags' => ['type' => 'tag', 'params' => ['class' => 'tags']]]):
            new FormBuilder($elemento, ['tipoDocumento' => ['disabled' => 'disabled'],
                'propietario' => ['disabled' => 'disabled'],
                'rol' => ['type' => 'hidden'],
                'detalle' => ['type' => 'textarea'],
                'curso' => ['disabled' => 'disabled'],
                'grupo' => ['disabled' => 'disabled'],
                'descripcion' => ['type' => 'text'],
                'fichero' => ['type' => 'file'],
                'tags' => ['type' => 'tag', 'params' => ['class' => 'tags']]]);
        $modelo = $this->model;
        return view($this->chooseView('edit'), compact('formulario', 'modelo'));
    }
    
    public function show($id)
    {
        $gestor = new Gestor(null,Documento::find($id));
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

    

}
