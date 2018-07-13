<?php

namespace Intranet\Http\Controllers;

use Illuminate\Http\Request;
use Intranet\Entities\Curso;


class PanelAlumnoCursoController extends BaseController
{
    protected $model = 'Curso';
    protected $gridFields = ['id', 'titulo', 'estado', 'fecha_inicio','NAlumnos'];

    
    public function search(){
        return Curso::where('activo', 1) ->get();
    }

    protected function iniBotones()
    {
        $this->panel->setBothBoton('alumnocurso.register', ['class' => 'btn-success authorize'], true);
        $this->panel->setBothBoton('alumnocurso.unregister', ['class' => 'btn-danger unauthorize'], true);
    }

    
}
