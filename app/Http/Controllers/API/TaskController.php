<?php

namespace Intranet\Http\Controllers\API;

use Illuminate\Http\Request;
use Intranet\Http\Requests;
use Intranet\Http\Controllers\Controller;
use Intranet\Http\Resources\TaskEditResource;

/**
 * Controlador API per a tasques.
 */
class TaskController extends ApiResourceController
{
    /**
     * Model Eloquent gestionat per l'endpoint.
     *
     * @var string
     */
    protected $model = 'Task';
    /**
     * Recurs específic per al payload d'edició.
     *
     * @var class-string<TaskEditResource>
     */
    protected $editResource = TaskEditResource::class;

}
