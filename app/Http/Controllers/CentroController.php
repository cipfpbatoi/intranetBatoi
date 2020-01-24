<?php

namespace Intranet\Http\Controllers;

use Illuminate\Http\Request;
use Intranet\Entities\Centro;
use Response;
use Mapper;
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
        return redirect()->action('EmpresaController@show', ['id' => $request->idEmpresa]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        parent::store($request);
        Session::put('pestana',2);
        return redirect()->action('EmpresaController@show', ['id' => $request->idEmpresa]);
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function mapa($id)
    {
        $centro = Centro::find($id);
        Mapper::location($centro->localidad.",".$centro->direccion)->map(['zoom'=>'15']);
        return view('empresa.mapa');
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
        return redirect()->action('EmpresaController@show', ['id' => $empresa]);
    }
    

}
