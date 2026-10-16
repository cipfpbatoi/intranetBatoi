<?php

namespace Intranet\Http\Controllers\API;

use Illuminate\Http\Request;
use Intranet\Entities\Asistencia;
use Intranet\Entities\Reunion;

/**
 * Controlador API per a assistència.
 */
class AsistenciaController extends ApiResourceController
{

    protected $model = 'Asistencia';

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function cambiar(Request $request)
    {
        $validated = $request->validate([
            'idReunion' => 'required|integer',
            'idProfesor' => 'required|string',
            'asiste' => 'required|boolean',
        ]);
        $reunion = $this->findModelOrFail(
            Reunion::class,
            $validated['idReunion'],
            'Reunió no trobada',
            ['reunion_id' => $validated['idReunion']]
        );
        $this->authorize('manageParticipants', $reunion);

        $attendance = Asistencia::query()
            ->where('idReunion', $reunion->id)
            ->where('idProfesor', (string) $validated['idProfesor'])
            ->first();

        if ($attendance === null) {
            return $this->sendNotFound('El professor no pertany a la reunió indicada.');
        }

        $reunion->profesores()->updateExistingPivot($validated['idProfesor'], [
            'asiste' => (bool) $validated['asiste'],
        ]);

        return $this->sendResponse(['updated' => true], $reunion);
    }

}
