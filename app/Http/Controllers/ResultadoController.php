<?php

namespace Intranet\Http\Controllers;

use Illuminate\Http\Request;
use Intranet\Entities\Grupo;
use Intranet\Entities\Modulo_grupo;
use Intranet\Entities\Programacion;
use Intranet\Entities\Resultado;
use Styde\Html\Facades\Alert;


/**
 * Class ResultadoController
 * @package Intranet\Http\Controllers
 */
class ResultadoController extends IntranetController
{

    use traitImprimir;

    /**
     * @var string
     */
    protected $perfil = 'profesor';
    /**
     * @var string
     */
    protected $model = 'Resultado';
    /**
     * @var array
     */
    protected $gridFields = ['Modulo', 'XEvaluacion', 'XProfesor'];
    /**
     * @var bool
     */
    protected $modal = true;

    /**
     *
     */
    protected function iniBotones()
    {
        $this->panel->setBotonera(['create'], ['delete', 'edit']);
    }


    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $elemento = $this->class::findOrFail($id); //busca si hi ha
        $elemento->fillAll($request);        // ompli i guarda
        return $this->redirect();
    }

    /**
     * @return mixed
     */
    public function search()
    {
        return Resultado::whereIn('idModuloGrupo', hazArray(Modulo_Grupo::MisModulos(), 'id', 'id'))->get();
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Request $request)
    {
        if ($modulogrupo = Modulo_grupo::find($request->idModuloGrupo)) {
            $this->realStore($request);
            if ($request->evaluacion == 3) {
                $programacion = Programacion::where('idModuloCiclo', $modulogrupo->idModuloCiclo)->where('curso', Curso())->first()->id;
                return redirect("/programacion/$programacion/seguimiento");
            }
            return $this->redirect();
        }
        Alert::danger("Eixe mòdul no es dona en eixe grup");
        return $this->redirect();

    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function listado()
    {
        if ($grupo = Grupo::select('codigo', 'nombre')->QTutor()->first()) {
            $resultados = Resultado::QGrupo($grupo->codigo)->orderBy('idModuloGrupo')
                            ->orderBy('evaluacion')->get();
            $datosInforme = $grupo->nombre;
            return $this->hazPdf('pdf.resultado', $resultados, $datosInforme)->stream();
        }
        Alert::danger(trans("messages.generic.nogroup"));
        return back();
    }

    protected function createWithDefaultValues(){
        return new Resultado(['idProfesor'=>AuthUser()->dni]);
    }



}
