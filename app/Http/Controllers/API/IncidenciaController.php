<?php

namespace Intranet\Http\Controllers\API;

use Illuminate\Http\Request;
use Intranet\Http\Requests;
use Intranet\Http\Controllers\Controller;
use Intranet\Http\Resources\IncidenciaEditResource;

/**
 * Controlador API per a incidències.
 */
class IncidenciaController extends ApiResourceController
{
    /**
     * Model Eloquent gestionat per l'endpoint.
     *
     * @var string
     */
    protected $model = 'Incidencia';
    /**
     * Recurs específic per al payload d'edició.
     *
     * @var class-string<IncidenciaEditResource>
     */
    protected $editResource = IncidenciaEditResource::class;

}
