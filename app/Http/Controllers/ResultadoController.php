<?php

namespace Intranet\Http\Controllers;

use Intranet\Application\Grupo\GrupoService;
use Intranet\Http\Controllers\Core\ModalController;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Intranet\Entities\Modulo_grupo;
use Intranet\Entities\Programacion;
use Intranet\Entities\Resultado;
use Intranet\Exceptions\NotFoundDomainException;
use Intranet\Http\Requests\ResultadoStoreRequest;
use Intranet\Http\Requests\ResultadoUpdateRequest;
use Intranet\Http\Traits\Core\Imprimir;
use Intranet\Services\School\ModuloGrupoService;
use Intranet\Services\UI\AppAlert as Alert;


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

    /**
     * @param int|string $idModulo
     * @throws NotFoundDomainException
     * @return \Illuminate\Http\RedirectResponse
     */
    private function rellenaPropuestasMejora($idModulo){
        $programacion = Programacion::where('idModuloCiclo', $idModulo)->first();
        if (!$programacion) {
            throw new NotFoundDomainException('Programació no trobada', ['modulo_ciclo_id' => $idModulo]);
        }
        return redirect()->route('programacion.seguimiento', ['programacion' => $programacion]);
    }

    public function store(ResultadoStoreRequest $request)
    {
        $this->authorize('create', Resultado::class);
        if ($modulogrupo = Modulo_grupo::find($request->idModuloGrupo)) {
            // Assegurem professor informant abans de guardar
            if (!$request->filled('idProfesor')) {
                $request->merge(['idProfesor' => AuthUser()->dni]);
            }
            $this->persist($request);
            return $this->redirect();
        }
        Alert::danger("Eixe mòdul no es dona en eixe grup");
        return $this->redirect();
    }

    /**
     * @param ResultadoUpdateRequest $request
     * @param int|string $id
     * @throws NotFoundDomainException
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(ResultadoUpdateRequest $request, $id)
    {
        try {
            $this->authorize('update', Resultado::findOrFail((int) $id));
        } catch (ModelNotFoundException $e) {
            throw new NotFoundDomainException('Resultat no trobat', ['resultado_id' => $id]);
        }
        $this->persist($request, $id);
        return $this->redirect();
    }

    /**
     * Elimina un resultat amb autorització explícita.
     *
     * @param int|string $id
     * @throws NotFoundDomainException
     */
    public function destroy($id)
    {
        try {
            $this->authorize('delete', Resultado::findOrFail((int) $id));
        } catch (ModelNotFoundException $e) {
            throw new NotFoundDomainException('Resultat no trobat', ['resultado_id' => $id]);
        }
        return parent::destroy($id);
    }

    /**
     * @return mixed
     */
    public function search()
    {
        $misModulos = app(ModuloGrupoService::class)->misModulos(AuthUser()->dni);
        return Resultado::whereIn('idModuloGrupo', hazArray($misModulos, 'id', 'id'))->get();
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
