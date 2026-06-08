<?php

namespace Intranet\Http\Controllers;

use Intranet\Http\Requests\DesdeHastaRequest;
use Intranet\Http\Traits\Core\Imprimir;
use Intranet\Services\General\GestorService;
use Intranet\Services\General\StateService;
use Intranet\Services\School\FaltaReportService;
use Illuminate\Support\Carbon;

/**
 * Controlador per generar informes d'absencies en PDF.
 */
class MensualController extends Controller
{
    use Imprimir;

    /**
     * Mostra el formulari d'impressio d'absencies.
     */
    public function vistaImpresion()
    {
        return view('falta.imprime');
    }

    /**
     * Genera el PDF d'absencies segons el rang sol.licitat.
     */
    public function imprimir(DesdeHastaRequest $request, FaltaReportService $faltaReportService)
    {
        return $this->printFaltaReport($request, $faltaReportService);
    }

    /**
     * Construeix i retorna el PDF mensual o la comunicacio d'absencia.
     */
    private function printFaltaReport(DesdeHastaRequest $request, FaltaReportService $faltaReportService)
    {
        $desde = new Carbon($request->desde);
        $hasta = new Carbon($request->hasta);
        if ($request->mensual !== 'on') {
            return self::hazPdf(
                "pdf.comunicacioAbsencia",
                $faltaReportService->getComunicacioElements($desde, $hasta)
            )->stream();
        }

        $nomComplet = $faltaReportService->nameFile();
        $gestor = new GestorService();
        $doc = $gestor->save([
            'fichero' => $nomComplet,
            'tags' => "Ausència Ausencia Llistat listado Professorado Profesorat Mensual"
        ]);

        $elementos = $faltaReportService->getMensualElements($desde, $hasta);
        $faltaReportService->markPrinted($hasta);
        StateService::makeLink($elementos, $doc);

        return self::hazPdf("pdf.faltas", $elementos)
            ->save(storage_path('/app/' . $nomComplet))
            ->download($nomComplet);
    }

}
