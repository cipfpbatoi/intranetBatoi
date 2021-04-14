<?php

namespace Intranet\Http\Controllers;

use Illuminate\Http\Request;
use Intranet\Entities\Centro;
use Response;
use Illuminate\Support\Facades\Session;

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
        $empresa = Centro::find($id)->idEmpresa;
        parent::destroy($id);
        Session::put('pestana',2);
        return $this->showEmpresa($empresa);
    }
    

}
