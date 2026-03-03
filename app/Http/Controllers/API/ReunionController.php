<?php

namespace Intranet\Http\Controllers\API;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Intranet\Entities\Reunion;
use Illuminate\Http\Request;
use Intranet\Exceptions\NotFoundDomainException;

/**
 * Controlador API per a reunions.
 */
class ReunionController extends ApiResourceController
{

    protected $model = 'Reunion';
    
    /**
     * @param int|string $idReunion
     * @param int|string $idAlumno
     * @param Request $request
     * @throws NotFoundDomainException
     * @return \Illuminate\Http\JsonResponse
     */
    protected function putAlumno($idReunion,$idAlumno, Request $request){
        try {
            $reunion = Reunion::findOrFail($idReunion);
        } catch (ModelNotFoundException $e) {
            throw new NotFoundDomainException('Reunió no trobada', ['reunion_id' => $idReunion]);
        }
        $reunion->alumnos()->updateExistingPivot($idAlumno,['capacitats'=> $request->capacitats]);
        return $this->sendResponse($request->capacitats, 'OK');
    }

}
