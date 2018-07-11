<?php

namespace Intranet\Http\Controllers;

use Illuminate\Http\Request;
use Intranet\Entities\Curso;
use Intranet\Entities\Curso_alumno;
use Intranet\Botones\BotonImg;
use Intranet\Entities\Modulo;
use Intranet\Entities\Modulo_ciclo;
use Intranet\Entities\Modulo_grupo;
use Intranet\Entities\Departamento;
use Intranet\Entities\Grupo;
use Intranet\Entities\Resultado;
use Styde\Html\Facades\Alert;
use Illuminate\Support\Facades\Session;
use Intranet\Entities\Horario;
use Intranet\Entities\Profesor;
use Intranet\Entities\Reunion;
use Intranet\Entities\Programacion;


class ResultadoController extends IntranetController
{

    protected $perfil = 'profesor';
    protected $model = 'Resultado';
    protected $gridFields = ['Modulo', 'XEvaluacion', 'XProfesor'];
    protected $modal = true;

    protected function iniBotones()
    {
        $this->panel->setBotonera(['create'], ['delete', 'edit']);
    }

    public function search()
    {
        $misModulos = hazArray(Modulo_Grupo::MisModulos(), 'id', 'id');
        return Resultado::whereIn('idModuloGrupo', $misModulos)
                        ->get();
    }

    /*
     * store (Request) return redirect
     * guarda els valors del formulari
     */

    public function store(Request $request)
    {
        $modulogrupo = Modulo_grupo::find($request->idModuloGrupo);
        if ($modulogrupo) {
            $this->realStore($request);
            if ($request->evaluacion == 3) {
                $programacion = Programacion::where('idModuloCiclo', $modulogrupo->idModuloCiclo)->where('curso', Curso())->first()->id;
                return redirect("/programacion/$programacion/seguimiento");
            } else
                return $this->redirect();
        }
        else {
            Alert::danger("Eixe mòdul no es dona en eixe grup");
            return $this->redirect();
        }
    }

    public function listado()
    {
        $grupo = Grupo::select('codigo', 'nombre')->QTutor()->first();
        if ($grupo) {
            $resultados = Resultado::QGrupo($grupo->codigo)->orderBy('idModuloGrupo')
                            ->orderBy('evaluacion')->get();
            $datosInforme = $grupo->nombre;
            return $this->hazPdf('pdf.resultado', $resultados, $datosInforme)->stream();
        } else {
            Alert::danger(trans("messages.generic.nogroup"));
            return back();
        }
    }

}
