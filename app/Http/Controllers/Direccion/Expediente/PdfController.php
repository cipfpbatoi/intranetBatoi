<?php

namespace Intranet\Http\Controllers\Direccion\Expediente;

use Intranet\Application\Expediente\ExpedienteService;
use Intranet\Http\Controllers\Controller;
use Intranet\Http\Traits\Core\Imprimir;

/**
 * Genera el PDF individual d'un expedient des del panell de Direcció.
 */
class PdfController extends Controller
{
    use Imprimir;

    /**
     * Retorna el PDF de l'expedient seleccionat.
     */
    public function __invoke(int|string $id, ExpedienteService $expedienteService)
    {
        $expediente = $expedienteService->findOrFail($id);
        $dades = [$expediente];
        $vista = $expediente->TipoExpediente->vista;

        return self::hazPdf("pdf.expediente.$vista", $dades)->stream();
    }
}
