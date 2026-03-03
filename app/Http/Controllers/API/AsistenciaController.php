<?php

namespace Intranet\Http\Controllers\API;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Intranet\Entities\Reunion;
use Intranet\Exceptions\NotFoundDomainException;

/**
 * Controlador API per a l'assistència a reunions.
 */
class AsistenciaController extends ApiResourceController
{

    protected $model = 'Asistencia';

    /**
     * @param Request $request
     * @throws NotFoundDomainException
     * @return \Illuminate\Http\JsonResponse
     */
    public function cambiar(Request $request)
    {
        try {
            $reunion = Reunion::findOrFail($request->idReunion);
        } catch (ModelNotFoundException $e) {
            throw new NotFoundDomainException('Reunió no trobada', ['reunion_id' => $request->idReunion]);
        }
        $reunion->profesores()->updateExistingPivot($request->idProfesor, ['asiste' => $request->asiste]);
        return $this->sendResponse(['updated' => true], $reunion);
    }

}
