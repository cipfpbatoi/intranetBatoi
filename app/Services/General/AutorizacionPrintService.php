<?php

namespace Intranet\Services\General;

use Intranet\Services\Document\PdfService;
use Illuminate\Support\Carbon;

/**
 * Servei d'impressió en lot per a fluxos d'autorització.
 *
 * S'encarrega de:
 * - filtrar elements per estat inicial,
 * - generar el PDF,
 * - persistir el document en gestor,
 * - aplicar la transició d'estat massiva,
 * - enllaçar (opcionalment) elements i document.
 */
class AutorizacionPrintService
{
    private PdfService $pdfService;

    public function __construct(PdfService $pdfService)
    {
        $this->pdfService = $pdfService;
    }

    /**
     * Executa la generació de document i canvi d'estat en lot.
     *
     * @param string $class FQCN de l'entitat.
     * @param string $model Nom curt del model per a config i nom de fitxer.
     * @param string|null $modelo Vista PDF sense prefix `pdf.`.
     * @param int|null $inicial Estat inicial per al filtre.
     * @param int|string|null $final Acció final de `StateService` o estat explícit.
     * @param string $orientacion Orientació del document.
     * @param bool $link Enllaçar elements al document generat.
     *
     * @return mixed Resposta de descàrrega del PDF o `null` si no hi ha elements.
     */
    public function imprimir(
        string $class,
        string $model,
        ?string $modelo = null,
        ?int $inicial = null,
        int|string|null $final = null,
        string $orientacion = 'portrait',
        bool $link = true
    ) {
        $modelo = ($modelo === null || $modelo === '') ? strtolower($model) . 's' : $modelo;
        $final = $final ?? '_print';
        $inicial = $inicial ?? (config('modelos.' . getClass($class) . '.print') - 1);

        $todos = $class::where('estado', '=', $inicial)->get();

        if ($todos->isEmpty()) {
            return null;
        }

        $pdf = $this->pdfService->hazPdf("pdf.$modelo", $todos, null, $orientacion);
        $nom = $model . new Carbon() . '.pdf';
        $nomComplet = 'gestor/' . Curso() . '/informes/' . $nom;
        $tags = config("modelos.$model.documento");

        $doc = GestorService::saveDocument($nomComplet, $tags);

        StateService::makeAll($todos, $final);

        if ($link && $doc) {
            StateService::makeLink($todos, $doc->id);
        }

        return $pdf->save(storage_path('/app/' . $nomComplet))->download($nom);
    }
}
