<?php

namespace Intranet\Http\Controllers\API;

use Intranet\Application\Comision\ComisionService;
use Intranet\Presentation\Crud\ComisionCrudSchema;

class ComisionController extends ApiResourceController
{

    protected $model = 'Comision';
    protected $rules = ComisionCrudSchema::API_RULES;

    private ComisionService $comisionService;

    public function __construct(ComisionService $comisionService)
    {
        parent::__construct();
        $this->comisionService = $comisionService;
    }

    public function autorizar()
    {
        $data = $this->comisionService->authorizationApiList();
        return $this->sendResponse($data, 'OK');
    }

    public function prePay($dni)
    {
        $data = $this->comisionService->prePayByProfesor((string) $dni);
        return $this->sendResponse($data, 'OK');
    }

}
