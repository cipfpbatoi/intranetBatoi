<?php

namespace Intranet\Http\Controllers;

use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Intranet\Entities\Centro;
use Intranet\Entities\Colaboracion;
use Response;
use Illuminate\Support\Facades\Session;
use Styde\Html\Facades\Alert;

/**
 * Class CentroController
 * @package Intranet\Http\Controllers
 */
class CentroController extends IntranetController
{


    /**
     * @var string
     */
    protected $perfil = 'profesor';
    /**
     * @var string
     */
    protected $model = 'Centro';


    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        parent::update($request, $id);
        Session::put('pestana',2);
        return $this->showEmpresa($request->idEmpresa);
    }

    private function showEmpresa($id){
        return redirect()->action('EmpresaController@show', ['empresa' => $id]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        parent::store($request);
        Session::put('pestana',2);
        return $this->showEmpresa($request->idEmpresa);
    }



    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $centro = Centro::find($id);
        $empresa = Centro::find($id)->idEmpresa;
        $misColaboraciones =Colaboracion::Micolaboracion($empresa)->count();
        if ($centro->Colaboraciones->count() == $misColaboraciones){
            try {
                parent::destroy($id);
            } catch (QueryException $exception){
                Alert::danger("No es pot esborrar perquè hi ha valoracions fetes per a eixe centre d'anys anteriors.");
            }
        } else {
            Alert::danger("Eixe centre te col·laboracions d'altres cicles. Esborra la col·laboració del teu cicle");
        }
        Session::put('pestana',2);
        return $this->showEmpresa($empresa);
    }
    

}
