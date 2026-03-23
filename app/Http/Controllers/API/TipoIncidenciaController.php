<?php

namespace Intranet\Http\Controllers\API;

use Intranet\Http\Resources\TipoIncidenciaEditResource;

/**
 * Controlador API per a tipus d'incidència.
 */
class TipoIncidenciaController extends ApiResourceController
{
    /**
     * Model Eloquent gestionat per l'endpoint.
     *
     * @var string
     */
    protected $model = 'TipoIncidencia';
    /**
     * Recurs específic per al payload d'edició.
     *
     * @var class-string<TipoIncidenciaEditResource>
     */
    protected $editResource = TipoIncidenciaEditResource::class;

}
