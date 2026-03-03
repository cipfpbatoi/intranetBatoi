<?php

namespace Intranet\Http\Controllers\API;

use Illuminate\Http\Request;
use Intranet\Entities\Reunion;
use Intranet\Exceptions\NotFoundDomainException;

/**
 * Controlador API per a l'assistència a reunions.
 */
/**
 * Controlador API per a assistència.
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
        $reunion = $this->findModelOrFail(Reunion::class, $request->idReunion, 'Reunió no trobada', ['reunion_id' => $request->idReunion]);
        $reunion->profesores()->updateExistingPivot($request->idProfesor, ['asiste' => $request->asiste]);
        return $this->sendResponse(['updated' => true], $reunion);
    }

}
