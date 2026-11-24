<?php

namespace Intranet\Services\School;

use Illuminate\Support\Facades\DB;
use Intranet\Entities\Actividad;
use Intranet\Services\Document\PdfService;
use Intranet\Services\General\StateService;
use RuntimeException;

/**
 * Genera el llistat de treball per a ITACA i marca les activitats incloses.
 */
class ActividadItacaPrintService
{
    /**
     * Injecta el generador de documents compartit.
     */
    public function __construct(private PdfService $pdfService)
    {
    }

    /**
     * Genera el PDF abans de marcar el lot com a ITACA, sense alterar l'autorització.
     *
     * @return mixed Resposta PDF o null si no hi ha activitats pendents.
     */
    public function imprimir()
    {
        return DB::transaction(function () {
            $actividades = Actividad::pendientesItaca()
                ->with(['profesores', 'grupos', 'tipoActividad'])
                ->orderBy('desde')->orderBy('id')
                ->lockForUpdate()->get();

            if ($actividades->isEmpty()) {
                return null;
            }

            // download() força la generació abans de persistir el canvi d'estat.
            $response = $this->pdfService
                ->hazPdf('pdf.actividadesItaca', $actividades, null, 'landscape')
                ->download('activitats-itaca.pdf');

            foreach ($actividades as $actividad) {
                if ((new StateService($actividad))->putEstado(5) === false) {
                    throw new RuntimeException('No s\'ha pogut marcar el lot d\'activitats per a ITACA.');
                }
            }

            return $response;
        });
    }
}
