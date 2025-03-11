<?php

namespace Intranet\Http\Controllers;

use Intranet\Entities\Grupo;
use Intranet\Entities\Modulo_grupo;
use Intranet\Entities\Programacion;
use Intranet\Entities\Resultado;
use Intranet\Http\Requests\ResultadoStoreRequest;
use Intranet\Http\Requests\ResultadoUpdateRequest;
use Intranet\Http\Traits\Imprimir;
use Styde\Html\Facades\Alert;


/**
 * Class ResultadoController
 * @package Intranet\Http\Controllers
 */
class ResultadoController extends ModalController
{

    use Imprimir;

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

    private function rellenaPropuestasMejora($idModulo){
        $programacion = Programacion::where('idModuloCiclo', $idModulo)->first()->id;
        return redirect("/programacion/$programacion/seguimiento");
    }

    public function store(ResultadoStoreRequest $request)
    {
        if ($modulogrupo = Modulo_grupo::find($request->idModuloGrupo)) {
            $newRes = new Resultado();
            $newRes->fillAll($request);
            if ($request->evaluacion == 3) {
                return $this->rellenaPropuestasMejora($modulogrupo->idModuloCiclo);
            }
            return $this->redirect();
        }
        Alert::danger("Eixe mòdul no es dona en eixe grup");
        return $this->redirect();
    }

    public function update(ResultadoUpdateRequest $request, $id)
    {
        Resultado::findOrFail($id)->fillAll($request);
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

    protected function createWithDefaultValues( $default=[]){
        return new Resultado(['idProfesor'=>AuthUser()->dni]);
    }
    
}
