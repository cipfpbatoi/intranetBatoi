<?php

declare(strict_types=1);

namespace Intranet\Application\Reunion;

use Illuminate\Support\Carbon;
use Intranet\Entities\OrdenReunion;
use Intranet\Entities\Reunion;
use Intranet\Services\Document\PdfService;
use Intranet\Services\Document\TipoReunionService;

/**
 * Genera el PDF definitiu d'una acta de reunió.
 */
class ReunionArchivePdfService
{
    /**
     * @param PdfService $pdfService
     */
    public function __construct(private PdfService $pdfService)
    {
    }

    /**
     * Genera i guarda el PDF de l'acta en la ruta indicada.
     */
    public function save(Reunion $reunion, string $absolutePath): void
    {
        $meetingDate = new Carbon($reunion->fecha);
        $reunion->dia = FechaString($meetingDate);
        $reunion->hora = $meetingDate->format('H:i');

        $updatedAt = new Carbon($reunion->updated_at);
        $reunion->hoy = haVencido($reunion->fecha)
            ? $reunion->dia
            : FechaString($updatedAt);

        $this->pdfService->hazPdf(
            $this->view($reunion),
            OrdenReunion::query()->where('idReunion', $reunion->id)->get(),
            $reunion,
            'portrait',
            'a4'
        )->save($absolutePath);
    }

    /**
     * Resol la plantilla d'acta o convocatòria segons la data de la reunió.
     */
    private function view(Reunion $reunion): string
    {
        $meetingType = TipoReunionService::find($reunion->tipo);
        $template = haVencido($reunion->fecha)
            ? $meetingType->acta
            : $meetingType->convocatoria;

        return 'pdf.reunion.' . $template;
    }
}
