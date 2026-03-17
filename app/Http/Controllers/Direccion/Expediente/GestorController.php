<?php

namespace Intranet\Http\Controllers\Direccion\Expediente;

use Intranet\Application\Expediente\ExpedienteService;
use Intranet\Http\Controllers\Controller;
use Intranet\Services\General\GestorService;

/**
 * Resol l'accés al gestor documental d'un expedient des de Direcció.
 */
class GestorController extends Controller
{
    /**
     * Redirigix al document associat o delega en el gestor documental.
     */
    public function __invoke(int|string $id, ExpedienteService $expedienteService)
    {
        $expediente = $expedienteService->findOrFail($id);

        if ($expediente->idDocumento) {
            return redirect('/documento/' . $expediente->idDocumento . '/show');
        }

        return (new GestorService($expediente))->render();
    }
}
