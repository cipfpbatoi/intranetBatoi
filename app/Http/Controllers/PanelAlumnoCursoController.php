<?php

namespace Intranet\Http\Controllers;

use Intranet\Http\Controllers\Core\BaseController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Intranet\Entities\Curso;


/**
 * Class PanelAlumnoCursoController
 * @package Intranet\Http\Controllers
 */
class PanelAlumnoCursoController extends BaseController
{
    /**
     * @var string
     */
    protected $model = 'Curso';
    /**
     * @var array
     */
    protected $gridFields = ['id', 'titulo', 'estado', 'fecha_inicio','NAlumnos'];


    /**
     * @return mixed
     */
    public function search(){
        Gate::authorize('viewAny', Curso::class);
        return Curso::where('activo', 1) ->get();
    }

    /**
     *
     */
    protected function iniBotones()
    {
        Gate::authorize('viewAny', Curso::class);
        $this->panel->setBothBoton('alumnocurso.register', ['class' => 'btn-success authorize'], true);
        $this->panel->setBothBoton('alumnocurso.unregister', ['class' => 'btn-danger unauthorize'], true);
    }

    
}
