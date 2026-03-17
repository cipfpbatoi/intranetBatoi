<?php

namespace Intranet\Http\Controllers\Direccion\Actividad;

use Intranet\Entities\Actividad;
use Intranet\Exceptions\NotFoundDomainException;
use Intranet\Http\Controllers\Controller;
use Intranet\Services\Document\PdfService;

/**
 * PDF de valoració d'una activitat des del panell de Direcció.
 */
class ValuePdfController extends Controller
{
    /**
     * Genera el PDF de valoració d'una activitat.
     *
     * @param int|string $id
     * @throws NotFoundDomainException
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function __invoke($id)
    {
        $actividad = Actividad::find($id);
        if (!$actividad) {
            throw new NotFoundDomainException('Activitat no trobada', [
                'actividad_id' => $id,
            ]);
        }

        $this->authorize('view', $actividad);

        return app(PdfService::class)->hazPdf('pdf.valoracionActividad', $actividad, null)->stream();
    }
}
