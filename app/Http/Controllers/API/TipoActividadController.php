<?php

namespace Intranet\Http\Controllers\API;

use Intranet\Http\Resources\TipoActividadEditResource;

/**
 * Controlador API per a tipus d'activitat.
 */
class TipoActividadController extends ApiResourceController
{
    /**
     * Model Eloquent gestionat per l'endpoint.
     *
     * @var string
     */
    protected $model = 'TipoActividad';
    /**
     * Recurs específic per al payload d'edició.
     *
     * @var class-string<TipoActividadEditResource>
     */
    protected $editResource = TipoActividadEditResource::class;

}
