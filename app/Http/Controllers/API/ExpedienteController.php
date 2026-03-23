<?php

namespace Intranet\Http\Controllers\API;

use Illuminate\Http\Request;
use Intranet\Entities\Expediente;
use Intranet\Http\Requests;
use Intranet\Http\Controllers\Controller;
use Intranet\Http\Resources\ExpedienteEditResource;

/**
 * Controlador API per a expedients.
 */
class ExpedienteController extends ApiResourceController
{
    /**
     * Model Eloquent gestionat per l'endpoint.
     *
     * @var string
     */
    protected $model = 'Expediente';
    /**
     * Recurs específic per al payload d'edició.
     *
     * @var class-string<ExpedienteEditResource>
     */
    protected $editResource = ExpedienteEditResource::class;

}
