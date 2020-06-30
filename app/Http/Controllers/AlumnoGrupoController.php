<?php

namespace Intranet\Http\Controllers;

use Illuminate\Http\Request;
use Intranet\Entities\AlumnoGrupo;
use Illuminate\Support\Facades\Auth;
use Intranet\Botones\BotonBasico;
use Intranet\Botones\BotonIcon;
use Intranet\Botones\BotonImg;
use Intranet\Entities\Grupo;
use Intranet\Entities\Curso;
use Intranet\Entities\Alumno;

class AlumnoGrupoController extends IntranetController
{
    protected $perfil = 'profesor';
    protected $model = 'AlumnoGrupo';
    protected $gridFields = ['nameFull', 'telef1', 'telef2', 'email','poblacion'];
    const FOL = 12;

    public function search(){
        $this->titulo = ['quien' => $this->search];
        return Alumno::QGrupo($this->search)->get();
    }

    protected function iniBotones()
    {
        $grupoTutoria = AuthUser()->grupoTutoria;
        $this->panel->setBoton('grid', new BotonImg('alumno.muestra'));
        $this->panel->setBoton('grid', new BotonImg('alumno.edit', ['roles' => config('roles.rol.direccion'), 'where' => ['idGrupo', '!=',  $grupoTutoria]]));
        $this->panel->setBoton('grid', new BotonImg('alumno.edit', ['where' => ['idGrupo', '==',  $grupoTutoria]]));
        $this->panel->setBoton('grid', new BotonImg('alumno.carnet', ['roles' => config('roles.rol.direccion'), 'where' => ['idGrupo', '!=',  $grupoTutoria]]));
        $this->panel->setBoton('profile', new BotonIcon('alumno.carnet', ['roles' => config('roles.rol.direccion'), 'where' => ['idGrupo', '!=',  $grupoTutoria]]));
        $this->panel->setBoton('grid', new BotonImg('alumno.carnet', ['where' => ['idGrupo', '==',  $grupoTutoria]]));
        $this->panel->setBoton('profile', new BotonIcon('alumno.carnet', ['where' => ['idGrupo', '==',  $grupoTutoria]]));
        //$this->panel->setBoton('grid', new BotonImg('alumno.baja', ['where' => ['idGrupo', '==', $miGrupo]]));
        //$this->panel->setBoton('profile', new BotonIcon('alumno.baja', ['where' => ['idGrupo', '==', $miGrupo]]));
        $this->panel->setBoton('grid', new BotonImg('direccion.aFol', ['img' => 'fa-file-word-o','roles' => config('roles.rol.direccion')]));
        if (AuthUser()->departamento == self::FOL && date('Y-m-d')>config('curso.certificatFol')) {
            $this->panel->setBoton('grid', new BotonImg('alumno.checkFol', ['img' => 'fa-square-o', 'where' => ['fol', '==', 0]]));
            $this->panel->setBoton('grid', new BotonImg('alumno.checkFol', ['img' => 'fa-check', 'where' => ['fol', '==', 1]]));
        }

        $cursos = Curso::Activo()->get();
        foreach ($cursos as $curso) {
            if (($curso->aforo == 0) || ($curso->NAlumnos < $curso->aforo * config('variables.reservaAforo')))
                $this->panel->setBoton('grid', new BotonImg('alumnocurso.registerAlumno/' . $curso->id, ['text' => trans('messages.generic.register') . $curso->titulo, 'img' => 'fa-institution']));
        }
        
    }

}
