<?php

namespace Intranet\Http\Controllers;

use Illuminate\Http\Request;
use Intranet\Entities\AlumnoResultado;
use Illuminate\Support\Facades\Auth;
use Intranet\Botones\BotonBasico;
use Intranet\Botones\BotonIcon;
use Intranet\Botones\BotonImg;
use Intranet\Entities\Grupo;
use Intranet\Entities\Curso;
use Intranet\Entities\Alumno;
use Intranet\Entities\Modulo_grupo;
use Intranet\Entities\Resultado;
use Illuminate\Support\Facades\Session;

class PanelSeguimientoAlumnosController extends IntranetController
{
    protected $perfil = 'profesor';
    protected $model = 'AlumnoResultado';
    protected $gridFields = ['nombre', 'nota', 'recomanacions'];





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


}
