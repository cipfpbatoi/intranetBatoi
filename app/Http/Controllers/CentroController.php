<?php

namespace Intranet\Http\Controllers;

use Illuminate\Http\Request;
use Intranet\Entities\Centro;
use Response;
use Illuminate\Support\Facades\Redirect;
use Mapper;
use Illuminate\Support\Facades\Session;

class CentroController extends IntranetController
{
    
    
    protected $perfil = 'profesor';
    protected $model = 'Centro';
    

    public function update(Request $request, $id)
    {
        parent::update($request, $id);
        Session::put('pestana',2);
        return redirect()->action('EmpresaController@show', ['id' => $request->idEmpresa]);
    }

    public function store(Request $request)
    {
        parent::store($request);
        Session::put('pestana',2);
        return redirect()->action('EmpresaController@show', ['id' => $request->idEmpresa]);
    }
    
    public function mapa($id)
    {
        $centro = Centro::find($id);
        Mapper::location($centro->localidad.",".$centro->direccion)->map(['zoom'=>'15']);
        return view('empresa.mapa');
    }

    public function destroy($id)
    {
        $empresa = Centro::find($id)->idEmpresa;
        parent::destroy($id);
        Session::put('pestana',2);
        return redirect()->action('EmpresaController@show', ['id' => $empresa]);
    }
    

}
