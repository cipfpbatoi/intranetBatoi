<?php

namespace Intranet\Http\Controllers\Direccion\Actividad;

use Intranet\Entities\Actividad;
use Intranet\Http\Controllers\Controller;
use Intranet\Services\School\ActividadItacaPrintService;

/**
 * Descarrega el llistat d'activitats valorades i finalitzades per a ITACA.
 */
class ItacaPrintController extends Controller
{
    /**
     * Genera el llistat o informa que no hi ha activitats disponibles.
     *
     * @return mixed
     */
    public function __invoke(ActividadItacaPrintService $service)
    {
        $this->authorize('create', Actividad::class);

        return $service->imprimir() ?? back()->with(
            'info',
            __('models.Actividad.itaca_empty')
        );
    }
}
