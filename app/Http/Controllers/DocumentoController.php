<?php

namespace Intranet\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use DB;
use Intranet\Botones\BotonImg;
use Intranet\Botones\BotonBasico;
use Intranet\Entities\Documento;
use Intranet\Entities\TipoDocumento;
use Intranet\Entities\Grupo;
use Intranet\Entities\Profesor;
use Intranet\Entities\Programacion;
use Intranet\Entities\Alumno;
use Intranet\Entities\AlumnoFct;
use Intranet\Entities\Fct;
use Styde\Html\Facades\Alert;
use Intranet\Botones\Panel;
use Illuminate\Support\Facades\Session;
use Intranet\Entities\Ciclo;
use Illuminate\Support\Facades\Route;

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
        if ($request->has('nota')) {
            $this->validate($request,['nota' => 'numeric|min:1|max:10']);
            $fct = AlumnoFct::findOrFail($fct);
            $fct->calProyecto = $request->nota;
            if ($fct->calificacion < 1) {
                $fct->calificacion = 1;
            }
            $fct->save();
        }
        if ($request->enlace) {
            $except = ['nota','fichero'];
        } else {
            $except = ['nota'];
        }
        return parent::store(subsRequest($request->duplicate(null, $request->except($except)), ['rol' => TipoDocumento::rol($request->tipoDocumento)]));
    }

    protected function createWithDefaultValues($default=[]){
        return new Documento(['curso'=>Curso(),'propietario'=>config('contacto.titulo')]);
     }

    public function project($idFct)
    {   
        if ($fct = AlumnoFct::findOrFail($idFct)) {
            
            $elemento = $this->createWithDefaultValues();
            $elemento->addFillable('nota');
            $elemento->tipoDocumento = 'Proyecto';
            $elemento->idDocumento = '';
            $elemento->ciclo = Grupo::QTutor(AuthUser()->dni)->first()->Ciclo->ciclo;
            $elemento->supervisor = AuthUser()->FullName;
            $elemento->propietario = $fct->Alumno->FullName;
            $elemento->setInputType('tipoDocumento', ['disabled' => 'disabled']);
            $elemento->setInputType('grupo', ['type' => 'hidden']);
            $elemento->setInputType('enlace', ['type' => 'hidden']);
            $elemento->setRule('nota','required');
            $default = $elemento->fillDefautOptions();
            $modelo = $this->model;
            Session::put('redirect', 'PanelFctAvalController@index');
            return view($this->chooseView('create'), compact('elemento', 'default', 'modelo'));
        } else {
            return back();
        }
    }
    
    public function qualitat()
    {
        $elemento = $this->createWithDefaultValues();
        $elemento->tipoDocumento = 'Qualitat';
        $elemento->idDocumento = '';
        $grupo = Grupo::QTutor(AuthUser()->dni)->first();
        $elemento->ciclo = $grupo->Ciclo->ciclo;
        $elemento->grupo = $grupo->nombre;
        $elemento->supervisor = AuthUser()->FullName;
        $elemento->propietario = $elemento->supervisor;
        $elemento->tags = 'Fct,Entrevista,Alumnat,Instructor,PR04-01,PR04-02';
        $elemento->addFillable('instrucciones',true);
        $elemento->instrucciones = 'Pujar en un sols document comprimit: Entrevista Alumnat i Entrevista Instructor';
        $elemento->setInputType('instrucciones', ['disabled' => 'disabled']);
        $elemento->setInputType('tipoDocumento', ['disabled' => 'disabled']);
        $elemento->setInputType('grupo', ['disabled' => 'disabled']);
        $elemento->setInputType('enlace', ['type' => 'hidden']);
        $default = $elemento->fillDefautOptions();
        $modelo = $this->model;
        Session::put('redirect', 'FctController@index');
        return view($this->chooseView('create'), compact('elemento', 'default', 'modelo'));
    }

    public function edit($id)
    {
        $elemento = Documento::findOrFail($id);
        $elemento->setInputType('tipoDocumento', ['disabled' => 'disabled']);
        $elemento->setInputType('grupo', ['type' => 'hidden']);
        if ($elemento->enlace){
            $elemento->setInputType('fichero', ['disableAll' => 'on']);
        } else {
            $elemento->setInputType('enlace', ['type' => 'hidden']);
        }
        $default = $elemento->fillDefautOptions();
        $modelo = $this->model;
        return view($this->chooseView('edit'), compact('elemento', 'default', 'modelo'));
    }
    
    public function show($id)
    {
        $doc = Documento::find($id);
        if (in_array($doc->rol, RolesUser(AuthUser()->rol))) {
            if ($doc->enlace != '') {
                return redirect($doc->enlace);
            } else {
                return parent::document($id);
            }
        }
        return back();
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
