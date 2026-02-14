<?php

namespace Intranet\Http\Controllers\API;

use Intranet\Application\Comision\ComisionService;

class ComisionController extends ApiBaseController
{

    protected $model = 'Comision';
    protected $rules = [
        'kilometraje' => 'Integer',
        'profesor' => 'required',
        'servicio' => 'required',
        'entrada' => 'after:salida',
        'matricula' => 'required_with:marca'
    ];

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
