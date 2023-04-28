<?php

namespace Intranet\Http\Controllers\API;


use Intranet\Entities\MaterialBaja;
use Intranet\Http\Resources\MaterialBajaResource;


class MaterialBajaController extends ApiBaseController
{
    protected $model = 'MaterialBaja';

    public function show($id, $send = true)
    {
        $registro = MaterialBaja::findOrFail($id);
        return $this->sendResponse(new MaterialBajaResource($registro), 'OK');
    }
}
