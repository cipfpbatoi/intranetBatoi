<?php

namespace Intranet\Http\Controllers;

use Intranet\Botones\BotonImg;
use Intranet\Botones\BotonBasico;
use Intranet\Entities\Task;
use Intranet\Http\Requests\TaskRequest;

/**
 * Class CicloController
 * @package Intranet\Http\Controllers
 */
class PanelTaskController extends ModalController
{
    const ADMINISTRADOR = 'roles.rol.administrador';

    /**
     * @var string
     */
    protected $model = 'Task';
    /**
     * @var array
     */
    protected $gridFields = [ 'id','descripcion','vencimiento','destino','activa'];


    protected function iniBotones()
    {
        $this->panel->setBoton('index', new BotonBasico('task.create', ['roles' => config(self::ADMINISTRADOR)]));
        $this->panel->setBoton('grid', new BotonImg('task.show', ['roles' => config(self::ADMINISTRADOR)]));
        $this->panel->setBoton('grid', new BotonImg('task.edit', ['roles' => config(self::ADMINISTRADOR)]));
        $this->panel->setBoton('grid', new BotonImg('task.delete', ['roles' => config(self::ADMINISTRADOR)]));
    }

    public function store(TaskRequest $request)
    {
        $new = new Task();
        $new->fillAll($request);
        return $this->redirect();
    }

    public function update(TaskRequest $request, $id)
    {
        Task::findOrFail($id)->fillAll($request);
        return $this->redirect();
    }

}
