<?php

namespace Intranet\Http\Controllers;

use Intranet\Http\Controllers\Core\IntranetController;
use Intranet\Http\Requests\AlumnoResultadoStoreRequest;

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
        $resultado = Resultado::findOrFail((int) $search);
        $this->authorize('view', $resultado);
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
        $this->validate($request, (new AlumnoResultadoStoreRequest())->rules());
        $resultado = Resultado::where('idModuloGrupo', $request->idModuloGrupo)->firstOrFail();
        $this->authorize('update', $resultado);
        $this->realStore($request);
        return back();
    }

    public function destroy($id){
        $alumnoResultado = AlumnoResultado::findOrFail((int) $id);
        $resultado = Resultado::where('idModuloGrupo', $alumnoResultado->idModuloGrupo)->firstOrFail();
        $this->authorize('update', $resultado);
        $this->search = (int) $resultado->id;
        parent::destroy($id);
        return back();
    }
}
