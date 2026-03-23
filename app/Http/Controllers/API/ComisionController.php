<?php

namespace Intranet\Http\Controllers\API;

use Intranet\Application\Comision\ComisionService;
use Intranet\Http\Resources\ComisionEditResource;
use Intranet\Presentation\Crud\ComisionCrudSchema;

/**
 * Controlador API per a comissions.
 */
class ComisionController extends ApiResourceController
{
    /**
     * Model Eloquent gestionat per l'endpoint.
     *
     * @var string
     */
    protected $model = 'Comision';
    /**
     * Recurs específic per al payload d'edició.
     *
     * @var class-string<ComisionEditResource>
     */
    protected $editResource = ComisionEditResource::class;
    protected $rules = ComisionCrudSchema::API_RULES;

    private ComisionService $comisionService;

    public function __construct(ComisionService $comisionService)
    {
        parent::__construct();
        $this->comisionService = $comisionService;
    }

    /**
     * Retorna les comissions pendents d'autorització.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function autorizar()
    {
        $data = $this->comisionService->authorizationApiList();
        return $this->sendResponse($data, 'OK');
    }

    /**
     * Retorna llistat per al càlcul previ de pagaments.
     *
     * @param string $dni
     * @return \Illuminate\Http\JsonResponse
     */
    public function prePay($dni)
    {
        $data = $this->comisionService->prePayByProfesor((string) $dni);
        return $this->sendResponse($data, 'OK');
    }

}
