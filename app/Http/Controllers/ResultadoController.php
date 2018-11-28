<?php

namespace Intranet\Http\Controllers;

use Illuminate\Http\Request;
use Intranet\Entities\Grupo;
use Intranet\Entities\Modulo_grupo;
use Intranet\Entities\Programacion;
use Intranet\Entities\Resultado;
use Styde\Html\Facades\Alert;



class ResultadoController extends IntranetController
{

    use traitImprimir;
    
    protected $perfil = 'profesor';
    protected $model = 'Resultado';
    protected $gridFields = ['Modulo', 'XEvaluacion', 'XProfesor'];
    protected $modal = true;

    protected function iniBotones()
    {
        $this->panel->setBotonera(['create'], ['delete', 'edit']);
    }

    
    public function update(Request $request, $id)
    {
        $elemento = $this->class::findOrFail($id); //busca si hi ha
        $elemento->fillAll($request);        // ompli i guarda
        return $this->redirect();
    }
    
    public function search()
    {
        //dd(Modulo_Grupo::MisModulos());
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
        if ($modulogrupo = Modulo_grupo::find($request->idModuloGrupo)) {
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
        if ($grupo = Grupo::select('codigo', 'nombre')->QTutor()->first()) {
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
