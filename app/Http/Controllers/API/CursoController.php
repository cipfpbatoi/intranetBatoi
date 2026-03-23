<?php

namespace Intranet\Http\Controllers\API;

use Illuminate\Http\Request;
use Intranet\Http\Requests;
use Intranet\Http\Controllers\Controller;
use Intranet\Http\Resources\CursoEditResource;

/**
 * Controlador API per a cursos.
 */
class CursoController extends ApiResourceController
{
    /**
     * Model Eloquent gestionat per l'endpoint.
     *
     * @var string
     */
    protected $model = 'Curso';
    /**
     * Recurs específic per al payload d'edició.
     *
     * @var class-string<CursoEditResource>
     */
    protected $editResource = CursoEditResource::class;

}
