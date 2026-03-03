<?php

namespace Intranet\Http\Controllers\API;


use Illuminate\Database\Eloquent\ModelNotFoundException;
use Intranet\Entities\MaterialBaja;
use Intranet\Exceptions\NotFoundDomainException;
use Intranet\Http\Resources\MaterialBajaResource;

/**
 * Controlador API per a consultes de baixes de material.
 */
class MaterialBajaController extends ApiResourceController
{
    protected $model = 'MaterialBaja';

    /**
     * @param int|string $id
     * @throws NotFoundDomainException
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            $registro = MaterialBaja::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            throw new NotFoundDomainException('Registre de baixa no trobat', ['material_baja_id' => $id]);
        }
        return $this->sendResponse(new MaterialBajaResource($registro), 'OK');
    }
}
