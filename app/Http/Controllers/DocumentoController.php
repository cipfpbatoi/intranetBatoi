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
use Intranet\Entities\Fct;
use Styde\Html\Facades\Alert;
use Intranet\Botones\Panel;
use Illuminate\Support\Facades\Session;
use Intranet\Entities\Ciclo;
use Illuminate\Support\Facades\Route;

class DocumentoController extends IntranetController
{

    protected $gridFields = ['tipoDocumento', 'descripcion', 'curso', 'idDocumento', 'propietario', 'created_at',
        'grupo', 'tags', 'ciclo', 'modulo','fichero','situacion'];
    protected $model = 'Documento';
    protected $directorio = '/Ficheros/';
    protected $panel;
    

    public function index()
    {
        Session::forget('redirect'); //buida variable de sessió redirect ja que sols se utiliza en cas de direccio
        $this->iniBotones();
        return $this->grid($this->search());
    }

    public function search()
    {
        if (Session::get('completa'))
            return Documento::whereIn('rol', RolesUser(AuthUser()->rol))->get();
        else
            return Documento::where('curso',Curso())->whereIn('rol', RolesUser(AuthUser()->rol))->get();
    }

    public function grupo($grupo)
    {
        $this->panel = new Panel($this->model, null, null, false);
        foreach (TipoDocumento::allRol($grupo) as $key => $role) {
            if (UserisAllow($role))
                $this->panel->setPestana($key, true, 'profile.documento', ['tipoDocumento', $key]);
        }
        $todos = Documento::whereIn('rol', RolesUser(AuthUser()->rol))->get();

        //$this->panel->setTitulo($this->titulo);
        $this->panel->setElementos($todos);
        if ($this->panel->countPestana())
            return view('documento.grupo', ['panel' => $this->panel]);
        else
            return redirect()->route('home');
    }

    public function proyecto()
    {

        $this->panel = new Panel($this->model, ['curso', 'descripcion', 'tags', 'ciclo'], 'grid.standard');
        $roles = RolesUser(AuthUser()->rol);
        $todos = Documento::whereIn('rol', $roles)
                ->where('tipoDocumento', 'Proyecto')
                ->orderBy('curso')
                ->get();
        $grupos = Ciclo::select('ciclo')
                ->where('departamento', AuthUser()->departamento)
                ->distinct()
                ->get();
        $this->panel->setBothBoton('documento.show', ['where' => ['rol', 'in', RolesUser(AuthUser()->rol)]]);
        foreach ($grupos as $grupo) {
            $this->panel->setPestana(str_replace([' ', '(', ')'], '', $grupo->ciclo), true, 'profile.documento', ['ciclo', $grupo->ciclo]);
        }
        //$this->panel->setTitulo($this->titulo);
        $this->panel->setElementos($todos);
        if ($grupos->count()) {
            return view('documento.grupo', ['panel' => $this->panel]);
        } else {
            Alert::danger(trans("messages.generic.noproyecto"));
            return redirect()->route('home');
        }
    }

    public function acta($grupo)
    {
        $this->panel = new Panel($this->model, null, null, false);
        $roles = RolesUser(AuthUser()->rol);
        $profe = Profesor::find(AuthUser()->dni);
        $todos = Documento::whereIn('rol', $roles)
                ->whereIn('tipoDocumento', TipoDocumento::all($grupo))
                ->whereIn('grupo', $profe->grupos())
                ->get();
        $grupos = Documento::select('grupo')
                ->whereIn('rol', $roles)
                ->whereIn('tipoDocumento', TipoDocumento::all($grupo))
                ->whereIn('grupo', $profe->grupos())
                ->distinct()
                ->get();
        foreach ($grupos as $grupo) {
            $this->panel->setPestana($grupo->grupo, true, 'profile.documento', ['grupo', $grupo->grupo]);
        }
        //$this->panel->setTitulo($this->titulo);
        $this->panel->setElementos($todos);
        if ($grupos->count())
            return view('documento.grupo', ['panel' => $this->panel]);
        else {
            Alert::danger(trans("messages.generic.noacta"));
            return redirect()->route('home');
        }
    }

    protected function iniBotones()
    {
        $this->panel->setBothBoton('documento.show', ['where' => ['rol', 'in', RolesUser(AuthUser()->rol),'link','==',1]]);
        $this->panel->setBoton('grid', new BotonImg('documento.delete', ['roles' => config('constants.rol.direccion')]));
        $this->panel->setBoton('grid', new BotonImg('documento.edit', ['roles' => config('constants.rol.direccion')]));
    }

    public function store(Request $request, $fct = null)
    {
        if ($request->has('nota')) {
            $fct = Fct::findOrFail($fct);
            $fct->calProyecto = $request->nota;
            $fct->calificacion = 1;
            $fct->save();
        }
        if ($request->enlace) $except = ['nota','fichero'];
        else $except = ['nota'];
        return parent::store(subsRequest($request->duplicate(null, $request->except($except)), ['rol' => TipoDocumento::rol($request->tipoDocumento)]));
    }
    

    public function project($idFct)
    {   if ($fct = Fct::findOrFail($idFct)) {
            $elemento = new Documento;
            $elemento->addFillable('nota');
            $elemento->tipoDocumento = 'Proyecto';
            $elemento->idDocumento = '';
            $elemento->ciclo = Grupo::where('tutor', '=', AuthUser()->dni)->first()->Ciclo->ciclo;
            $elemento->supervisor = AuthUser()->FullName;
            $elemento->propietario = $fct->Alumno->FullName;
            $elemento->setInputType('tipoDocumento', ['disabled' => 'disabled']);
            $elemento->setInputType('grupo', ['type' => 'hidden']);
            $elemento->setInputType('enlace', ['type' => 'hidden']);
            $default = $elemento->fillDefautOptions();
            $modelo = $this->model;
            Session::put('redirect', 'PanelAvalFctController@index');
            return view($this->chooseView('create'), compact('elemento', 'default', 'modelo'));
        } else {
            return back();
        }
    }
    
    public function qualitat()
    {   
        $elemento = new Documento;
        $elemento->tipoDocumento = 'Qualitat';
        $elemento->idDocumento = '';
        $elemento->ciclo = Grupo::where('tutor', '=', AuthUser()->dni)->first()->Ciclo->ciclo;
        $elemento->grupo = Grupo::where('tutor', '=', AuthUser()->dni)->first()->nombre;
        $elemento->supervisor = AuthUser()->FullName;
        $elemento->propietario = AuthUser()->FullName;
        $elemento->tags = 'Fct,PR03-01,PR04-01,PR04-02,PR06-01';
        $elemento->addFillable('instrucciones',true);
        $elemento->instrucciones = 'Pujar en un sols document comprimit: PR03-01,PR04-01,PR04-02,PR06-01';
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
            //$elemento->setRule('fichero','mimes:pdf,zip');
            $elemento->setInputType('fichero', ['disableAll' => 'on']);
        }  
        else 
            $elemento->setInputType('enlace', ['type' => 'hidden']);
        $default = $elemento->fillDefautOptions();
        $modelo = $this->model;
        return view($this->chooseView('edit'), compact('elemento', 'default', 'modelo'));
    }
    
    public function show($id)
    {
        $doc = Documento::find($id);
        if (in_array($doc->rol, RolesUser(AuthUser()->rol))) {
            if ($doc->enlace != '')
                return redirect($doc->enlace);
            else {
                return parent::document($id);
            }
        } else
            return back();
    }

    public function destroy($id)
    {
        $borrar = Documento::findOrFail($id);
        if ($borrar) {
            if ($borrar->link && !$borrar->exist) unlink(storage_path('app/' . $borrar->fichero));
            $borrar->delete();
        }
        return $this->redirect();
    }

    public function actas()
    {
        $this->gridFields = ['curso', 'descripcion', 'grupo', 'created_at'];
        $this->panel = new Panel($this->model, $this->gridFields, 'grid.standard');
        $roles = RolesUser(AuthUser()->rol);
        $todos = Documento::whereIn('rol', $roles)
                ->where('tags', 'like', 'acta claustro')
                ->get();
        return $this->grid($todos);
    }
    

}
