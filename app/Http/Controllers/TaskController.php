<?php

namespace Intranet\Http\Controllers;

use Intranet\Http\Controllers\Core\ModalController;


use Intranet\UI\Botones\BotonBasico;
use Intranet\UI\Botones\BotonImg;
use Intranet\Entities\Task;
use Intranet\Presentation\Crud\TaskCrudSchema;
use Intranet\Services\School\TaskValidationService;

use Intranet\Http\Requests\TaskRequest;


/**
 * Controlador de manteniment i validació de tasques.
 */
class TaskController extends ModalController
{
    private $tarea;
    const ADMINISTRADOR = 'roles.rol.administrador';
    private ?TaskValidationService $taskValidationService = null;

    /**
     * @var string
     */
    protected $model = 'Task';
    /**
     * @var array
     */
    protected $gridFields = TaskCrudSchema::GRID_FIELDS;

    protected $formFields = TaskCrudSchema::FORM_FIELDS;

    public function __construct(?TaskValidationService $taskValidationService = null)
    {
        parent::__construct();
        $this->taskValidationService = $taskValidationService;
    }

    private function validationService(): TaskValidationService
    {
        if ($this->taskValidationService === null) {
            $this->taskValidationService = app(TaskValidationService::class);
        }

        return $this->taskValidationService;
    }

    protected function iniBotones()
    {
        $this->panel->setBoton('index', new BotonBasico('task.create', ['roles' => config(self::ADMINISTRADOR)]));
        $this->panel->setBoton('grid', new BotonImg('task.show', ['roles' => config(self::ADMINISTRADOR)]));
        $this->panel->setBoton('grid', new BotonImg('task.edit', ['roles' => config(self::ADMINISTRADOR)]));
        $this->panel->setBoton('grid', new BotonImg('task.delete', ['roles' => config(self::ADMINISTRADOR)]));
    }



    public function store(TaskRequest $request)
    {
        $this->authorize('create', Task::class);
        $this->persist($request);
        return $this->redirect();
    }

    public function update(TaskRequest $request, $id)
    {
        $this->authorize('update', Task::findOrFail($id));
        $this->persist($request, $id);
        return $this->redirect();
    }

    public function check($id)
    {
        $this->tarea = Task::findOrFail($id);
        $this->authorize('check', $this->tarea);
        $taskTeacher = $this->tarea->myDetails;
        if ($taskTeacher) {
            $this->tarea->Profesores()->detach(AuthUser()->dni);
        } else {
            $valid = $this->validationService()->resolve($this->tarea->action, AuthUser()->dni);
            $this->tarea->Profesores()->attach(AuthUser()->dni, ['check'=>1,'valid'=>$valid]);
        }
       return back();
    }

}
