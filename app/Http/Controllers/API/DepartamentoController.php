<?php

namespace Intranet\Http\Controllers\API;


use Intranet\Http\Controllers\Controller;
use Intranet\Http\Controllers\API\ApiBaseController;
use Intranet\Http\Resources\JDepartamentoResource;

class DepartamentoController extends ApiBaseController
{

    protected $model = 'Departamento';

    public function index()
    {
        $data = $this->class::where('didactico', 1)->whereNotNull('idProfesor')->get();
        return $this->sendResponse(['data' => JDepartamentoResource::collection($data)], 'OK');
    }

}
