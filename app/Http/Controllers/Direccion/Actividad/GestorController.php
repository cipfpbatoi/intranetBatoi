<?php

namespace Intranet\Http\Controllers\Direccion\Actividad;

use Intranet\Entities\Actividad;
use Intranet\Exceptions\NotFoundDomainException;
use Intranet\Http\Controllers\Controller;
use Intranet\Services\General\GestorService;

/**
 * Accés al gestor documental d'una activitat des del panell de Direcció.
 */
class GestorController extends Controller
{
    /**
     * Retorna el document o la redirecció associada a l'activitat.
     *
     * @param int|string $id
     * @throws NotFoundDomainException
     * @return mixed
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

        if ($actividad->idDocumento) {
            return redirect('/documento/' . $actividad->idDocumento . '/show');
        }

        return (new GestorService($actividad))->render();
    }
}
