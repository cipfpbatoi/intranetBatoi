<?php

namespace Intranet\Http\Controllers;

use Intranet\Http\Controllers\Core\BaseController;

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
     * Mostra el llistat de cursos d'alumne amb autorització prèvia.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        $this->authorizeForUser(authUser(), 'viewAny', Curso::class);
        return parent::index();
    }

    /**
     * @return mixed
     */
    public function search(){
        $this->authorizeForUser(authUser(), 'viewAny', Curso::class);
        return Curso::where('activo', 1) ->get();
    }

    /**
     *
     */
    protected function iniBotones()
    {
        $this->authorizeForUser(authUser(), 'viewAny', Curso::class);
        $this->panel->setBothBoton('alumnocurso.register', ['class' => 'btn-success authorize'], true);
        $this->panel->setBothBoton('alumnocurso.unregister', ['class' => 'btn-danger unauthorize'], true);
    }

    
}
