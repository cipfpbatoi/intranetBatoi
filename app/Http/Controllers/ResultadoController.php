<?php

namespace Intranet\Http\Controllers;

use Intranet\Application\Grupo\GrupoService;
use Intranet\Http\Controllers\Core\ModalController;

use Intranet\Entities\Modulo_grupo;
use Intranet\Entities\Programacion;
use Intranet\Entities\Resultado;
use Intranet\Http\Requests\ResultadoStoreRequest;
use Intranet\Http\Requests\ResultadoUpdateRequest;
use Intranet\Http\Traits\Core\Imprimir;
use Styde\Html\Facades\Alert;


/**
 * Class ResultadoController
 * @package Intranet\Http\Controllers
 */
class ResultadoController extends ModalController
{

    use Imprimir;

    private ?GrupoService $grupoService = null;

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

    public function __construct(?GrupoService $grupoService = null)
    {
        parent::__construct();
        $this->grupoService = $grupoService;
    }

    private function grupos(): GrupoService
    {
        if ($this->grupoService === null) {
            $this->grupoService = app(GrupoService::class);
        }

        return $this->grupoService;
    }

    private function rellenaPropuestasMejora($idModulo){
        $programacion = Programacion::where('idModuloCiclo', $idModulo)->first()->id;
        return redirect("/programacion/$programacion/seguimiento");
    }

    public function store(ResultadoStoreRequest $request)
    {
        if ($modulogrupo = Modulo_grupo::find($request->idModuloGrupo)) {
            $newRes = new Resultado();
            // Assegurem professor informant abans de guardar
            if (!$request->filled('idProfesor')) {
                $request->merge(['idProfesor' => AuthUser()->dni]);
            }
            $newRes->fillAll($request);
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
        if ($grupo = $this->grupos()->largestByTutor(AuthUser()->dni)) {
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
