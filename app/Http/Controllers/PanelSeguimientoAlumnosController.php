<?php

namespace Intranet\Http\Controllers;

use Intranet\Http\Controllers\Core\IntranetController;

use Illuminate\Http\Request;
use Intranet\Entities\AlumnoResultado;
use Intranet\Entities\Resultado;

class PanelSeguimientoAlumnosController extends IntranetController
{
    protected $perfil = 'profesor';
    protected $model = 'AlumnoResultado';
    protected $redirect = 'PanelSeguimientoAlumnosController@indice';

    public function indice($search)
    {
        $resultado = Resultado::find($search);
        $elemento = $resultado->moduloGrupo;
        $resultados = AlumnoResultado::where('idModuloGrupo',$resultado->idModuloGrupo)->get();
        $alumnes = $this->createWithDefaultValues(['idModuloGrupo'=>$resultado->idModuloGrupo])->getidAlumnoOptions();
        return view('seguimiento.index', compact('elemento',  'alumnes', 'resultados'));
    }




    /*
     * store (Request) return redirect
     * guarda els valors del formulari
     */
    public function store(Request $request)
    {
        $this->realStore($request);
        return back();
    }

    public function destroy($id){
        $this->search = 1;
        parent::destroy($id);
        return back();
    }
}
