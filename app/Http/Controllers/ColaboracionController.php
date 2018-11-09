<?php

namespace Intranet\Http\Controllers;

use Illuminate\Http\Request;
use Intranet\Entities\Empresa;
use Intranet\Entities\Centro;
use Intranet\Entities\Colaboracion;
use Intranet\Entities\Grupo;
use Intranet\Entities\Ciclo;
use Response;
use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Intranet\Botones\BotonBasico;
use Intranet\Botones\BotonIcon;
use Intranet\Botones\BotonImg;
use Styde\Html\Facades\Alert;
use Illuminate\Support\Facades\Session;

class ColaboracionController extends IntranetController
{
    protected $perfil = 'profesor';
    protected $model = 'Colaboracion';
    protected $gridFields = ['empresa','localidad','contacto','email','telefono','Xciclo','puestos','dni'];
    protected $titulo = [];

  
    public function copy($id)
    {
        $profesor = AuthUser()->dni;
        $elemento = Colaboracion::find($id);
        Session::put('pestana',1);
        $copia = New Colaboracion();
        $copia->fill($elemento->toArray());
        $copia->idCiclo = Grupo::QTutor($profesor)->get()->count() > 0 ? Grupo::QTutor($profesor)->first()->idCiclo : Grupo::QTutor($profesor,true)->first()->idCiclo;
       
          
        
        $copia->tutor = AuthUser()->FullName;
        
            // para no generar más de uno por ciclo
        $validator = Validator::make($copia->toArray(),$copia->getRules());
        if ($validator->fails()){
            return Redirect::back()->withInput()->withErrors($validator);
        }
        else{
            $copia->save();
            return back();
        }
    }
    
    protected function realStore(Request $request, $id = null)
    {
        $elemento = $id ? Colaboracion::findOrFail($id) : new Colaboracion(); //busca si hi ha
        if ($id) $elemento->setRule('idCentro',$elemento->getRule('idCentro').','.$id);
        $this->validateAll($request, $elemento);    // valida les dades
        return $elemento->fillAll($request);        // ompli i guarda
    }

    public function iniBotones()
    {
        $this->panel->setBoton('grid', new BotonImg('colaboracion.show',['roles' => [config('roles.rol.practicas'),config('roles.rol.dual')]]));
        
    }
    public function search(){
        $this->titulo = ['quien' => AuthUser()->Departamento->literal ];
        $ciclos = Ciclo::select('id')->where('departamento', AuthUser()->departamento)->get()->toArray();
        $colaboraciones = Colaboracion::whereIn('idCiclo',$ciclos)->get();
        return $colaboraciones->filter(function ($colaboracion){
            return $colaboracion->Centro->Empresa->concierto;
        });
    }
    public function update(Request $request, $id)
    {
        parent::update($request, $id);
        $empresa = Centro::find($request->idCentro)->idEmpresa;
        Session::put('pestana',1);
        return redirect()->action('EmpresaController@show', ['id' => $empresa]);
    }

    public function store(Request $request)
    {
        parent::store($request);
        $empresa = Centro::find($request->idCentro)->idEmpresa;
        Session::put('pestana',1);
        return redirect()->action('EmpresaController@show', ['id' => $empresa]);
    }

    public function destroy($id)
    {
        $empresa = Colaboracion::find($id)->Centro->Empresa;
        parent::destroy($id);
        Session::put('pestana',1);
        return redirect()->action('EmpresaController@show', ['id' => $empresa]);
    }
    public function show($id){
        $empresa = Colaboracion::find($id)->Centro->idEmpresa;
        Session::put('pestana',1);
        return redirect()->action('EmpresaController@show', ['id' => $empresa]);
    }

}
