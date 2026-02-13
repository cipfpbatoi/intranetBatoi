<?php

namespace Intranet\Http\Controllers;

use Intranet\Http\Controllers\Core\BaseController;

use Intranet\Entities\Curso;

/**
 * Class PanelCursoController
 * @package Intranet\Http\Controllers
 */
class PanelCursoController extends BaseController
{

    /**
     * @var string
     */
    protected $model = 'Curso';
    /**
     * @var array
     */
    protected $gridFields = ['id', 'titulo', 'estado', 'fecha_inicio','Alumno'];

    /**
     * @return mixed
     */
    protected function search(){
        return Curso::where('activo', 1)->get();
    }

    /**
     *
     */
    protected function iniBotones()
    {
        $this->panel->setBothBoton('alumnocurso.register', ['class' => 'btn-success authorize'], true);
        $this->panel->setBothBoton('alumnocurso.unregister', ['class' => 'btn-danger unauthorize'], true);
    }

    
}
