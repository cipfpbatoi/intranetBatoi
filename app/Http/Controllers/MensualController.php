<?php

namespace Intranet\Http\Controllers;

use Intranet\Http\Requests\DesdeHastaRequest;
use Intranet\Http\Traits\Imprimir;
use Intranet\Services\School\FaltaReportService;
use Intranet\Services\General\GestorService;
use Intranet\Services\General\StateService;
use Jenssegers\Date\Date;


class MensualController extends Controller
{
    use Imprimir;

    public function vistaImpresion()
    {
        return view('falta.imprime');
    }

    public function imprimir(DesdeHastaRequest $request, FaltaReportService $faltaReportService)
    {
        if ($request->llistat === 'faltas') {
            return $this->printFaltaReport($request, $faltaReportService);
        }
        return FaltaItacaController::printReport($request);
    }

    private function printFaltaReport(DesdeHastaRequest $request, FaltaReportService $faltaReportService)
    {
        $desde = new Date($request->desde);
        $hasta = new Date($request->hasta);
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
