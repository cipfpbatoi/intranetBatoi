<?php

namespace Intranet\Http\Controllers;

use Intranet\Application\FaltaItaca\FaltaItacaWorkflowService;
use Intranet\Application\Horario\HorarioService;
use Intranet\Http\Controllers\Core\IntranetController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Intranet\Entities\Hora;
use Intranet\Http\Traits\Autorizacion;
use Intranet\Http\Traits\Core\Imprimir;
use Intranet\Services\General\GestorService;
use Intranet\Services\General\StateService;

/**
 * Controlador legacy de justificació i autorització de birrets/ITACA.
 *
 * @deprecated Mantingut per compatibilitat mentre es retira el flux antic de birrets.
 */
class FaltaItacaController extends IntranetController
{
    use Autorizacion,Imprimir;

    private ?FaltaItacaWorkflowService $faltaItacaWorkflowService = null;
    private ?HorarioService $horarioService = null;
    
    protected $perfil = 'profesor';
    protected $model = 'Falta_itaca';

    private function horarios(): HorarioService
    {
        if ($this->horarioService === null) {
            $this->horarioService = app(HorarioService::class);
        }

        return $this->horarioService;
    }

    private function faltes(): FaltaItacaWorkflowService
    {
        if ($this->faltaItacaWorkflowService === null) {
            $this->faltaItacaWorkflowService = app(FaltaItacaWorkflowService::class);
        }

        return $this->faltaItacaWorkflowService;
    }
    /**
     * Pantalla legacy de professorat per a birrets.
     *
     * @deprecated Flux antic pendent de retirada.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        Session::forget('redirect');
        $profesor = AuthUser();
        $horarios = $this->horarios()->byProfesor((string) $profesor->dni);
        $horas = Hora::all();
        return view('falta.itaca', compact('profesor', 'horarios', 'horas'));
    }
    
    /**
     * Genera l'informe PDF legacy de birrets.
     *
     * @deprecated Mantingut mentre existix el flux de birrets.
     *
     * @param Request $request
     * @return mixed
     */
    public static function printReport($request)
    {
        $service = app(FaltaItacaWorkflowService::class);
        $elementos = $service->findElements($request->desde, $request->hasta);

        if ($request->mensual != 'on') {
            return self::hazPdf("pdf.comunicacioBirret", $elementos)->stream();
        }

        $nomComplet = $service->monthlyReportFileName($request->desde);
        $service->deletePreviousMonthlyReport($nomComplet);
        $gestor = new GestorService();

        $doc = $gestor->save(['fichero' => $nomComplet, 'tags' => "Birret listado llistat autorizacion autorizacio"]);
        StateService::makeLink($elementos, $doc);
        return self::hazPdf("pdf.comunicacioBirret", $elementos)
                        ->save(storage_path('/app/' . $nomComplet))
                        ->download($nomComplet);

    }

    /**
     * Resol una absència del flux legacy de birrets.
     *
     * @deprecated Mantingut mentre existix el flux de birrets.
     *
     * @param int|string $id
     * @return mixed
     */
    public function resolve($id)
    {
        $this->faltes()->resolveByAbsenceId((int) $id);
        return $this->follow(1, 1);
    }

    /**
     * Rebutja una absència del flux legacy de birrets.
     *
     * @deprecated Mantingut mentre existix el flux de birrets.
     *
     * @param int|string $id
     * @param Request $request
     * @return mixed
     */
    public function refuse($id, Request $request)
    {
        $this->faltes()->refuseByAbsenceId((int) $id, $request->explicacion);
        return $this->follow(2, 1);
    }
}
