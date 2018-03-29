<?php

namespace Intranet\Http\Controllers;

use Intranet\Entities\Curso;

class PanelCursoController extends BaseController
{

    protected $model = 'Curso';
    protected $gridFields = ['id', 'titulo', 'estado', 'fecha_inicio','Alumno'];

    protected function search(){
        return Curso::where('activo', 1)->get();
    }

    protected function iniBotones()
    {
        $this->panel->setBothBoton('alumnocurso.register', ['class' => 'btn-success authorize'], true);
        $this->panel->setBothBoton('alumnocurso.unregister', ['class' => 'btn-danger unauthorize'], true);
    }

    
}
