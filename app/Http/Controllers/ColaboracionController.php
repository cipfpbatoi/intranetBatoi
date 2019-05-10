<?php

namespace Intranet\Http\Controllers;

use Illuminate\Http\Request;
use Intranet\Entities\Activity;
use Intranet\Entities\Fct;
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

/**
 * Class ColaboracionController
 * @package Intranet\Http\Controllers
 */
class ColaboracionController extends IntranetController
{
    use traitAutorizar;
    /**
     * @var string
     */
    protected $perfil = 'profesor';
    /**
     * @var string
     */
    protected $model = 'Colaboracion';
    /**
     * @var array
     */
    protected $gridFields = ['empresa','localidad','contacto','email','telefono','Xciclo','puestos','dni'];
    /**
     * @var array
     */
    protected $titulo = [];
    protected $vista = ['show'=>'colaboracion'];


    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
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
        if ($validator->fails())
            return Redirect::back()->withInput()->withErrors($validator);

        $copia->save();
        return back();

    }

    /**
     * @param Request $request
     * @param null $id
     * @return mixed
     */
    protected function realStore(Request $request, $id = null)
    {
        $elemento = $id ? Colaboracion::findOrFail($id) : new Colaboracion(); //busca si hi ha
        if ($id) $elemento->setRule('idCentro',$elemento->getRule('idCentro').','.$id);
        $this->validateAll($request, $elemento);    // valida les dades
        return $elemento->fillAll($request);        // ompli i guarda
    }

    /**
     *
     */
    public function iniBotones()
    {
        $this->panel->setBoton('grid', new BotonImg('colaboracion.show',['roles' => [config('roles.rol.practicas'),config('roles.rol.dual')]]));
        
    }

    /**
     * @return mixed
     */
    public function search(){
        $this->titulo = ['quien' => AuthUser()->Departamento->literal ];
        $ciclos = Ciclo::select('id')->where('departamento', AuthUser()->departamento)->get()->toArray();
        $colaboraciones = Colaboracion::whereIn('idCiclo',$ciclos)->get();
        return $colaboraciones->filter(function ($colaboracion){
            return $colaboracion->Centro->Empresa->concierto;
        });
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        parent::update($request, $id);
        $empresa = Centro::find($request->idCentro)->idEmpresa;
        Session::put('pestana',1);
        return redirect()->action('EmpresaController@show', ['id' => $empresa]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        parent::store($request);
        $empresa = Centro::find($request->idCentro)->idEmpresa;
        Session::put('pestana',1);
        return redirect()->action('EmpresaController@show', ['id' => $empresa]);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $empresa = Colaboracion::find($id)->Centro->Empresa;
        parent::destroy($id);
        Session::put('pestana',1);
        return redirect()->action('EmpresaController@show', ['id' => $empresa]);
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View

    public function show($id){
        $empresa = Colaboracion::find($id)->Centro->idEmpresa;
        Session::put('pestana',1);
        return redirect()->action('EmpresaController@show', ['id' => $empresa]);
    }
     */

    /*
     * show($id) return vista
     * busca en model de dades i el mostra amb vista show
     */

    public function show($id)
    {
        $elemento = Colaboracion::findOrFail($id);
        $contactCol = Activity::where('model_class','Intranet\Entities\Colaboracion')->where('action','email')->
            where('model_id',$id)->get();
        $fcts = Fct::where('idColaboracion',$id)->where('asociacion',1)->get();
        $alumnos = [];
        foreach ($fcts as $fct)
            $alumnos = array_merge($alumnos,hazArray($fct->Alumnos,'nia','nia'));
        $contactAl = Activity::where('model_class','Intranet\Entities\Alumno')->where('action','email')->
                    whereIn('model_id',$alumnos)->get();
        //dd($contactosAl);
        return view($this->chooseView('show'), compact('elemento', 'contactCol','contactAl'));
    }

}
