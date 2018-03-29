<?php

namespace Intranet\Http\Controllers;

use Illuminate\Http\Request;
use Intranet\Entities\Alumno_grupo;
use Illuminate\Support\Facades\Auth;
use DB;
use Intranet\Botones\BotonBasico;
use Intranet\Botones\BotonIcon;
use Intranet\Botones\BotonImg;
use Intranet\Entities\Grupo;
use Intranet\Entities\Curso;
use Intranet\Entities\AlumnoCurso;

class Alumno_grupoController extends IntranetController
{
    protected $perfil = 'profesor';
    protected $model = 'Alumno_grupo';
    protected $gridFields = ['name', 'telef1', 'telef2', 'email'];
    protected $miGrupo;

  

    public function muestra($grupo)
    {
        $todos = Alumno_grupo::join('alumnos', 'idAlumno', '=', 'nia')
                ->select('alumnos.*', 'idGrupo', 'idAlumno', DB::raw('CONCAT(apellido1," ",apellido2,",",nombre) AS name'))
                ->where('idGrupo', '=', $grupo)
                ->orderBy('name', 'asc')
                ->get();
        $this->titulo = ['quien' => $grupo];
        $this->iniBotones();
        return parent::grid($todos);
    }

    protected function iniBotones()
    {
        $miGrupo = Grupo::where('tutor', '=', AuthUser()->dni)->get();
        $miGrupo = isset($miGrupo->first()->codigo) ? $miGrupo->first()->codigo : '';
        $this->panel->setBoton('grid', new BotonImg('alumno.muestra'));
        $this->panel->setBoton('grid', new BotonImg('alumno.edit', ['roles' => config('constants.rol.direccion'), 'where' => ['idGrupo', '!=', $miGrupo]]));
        $this->panel->setBoton('grid', new BotonImg('alumno.edit', ['where' => ['idGrupo', '==', $miGrupo]]));
        $this->panel->setBoton('grid', new BotonImg('alumno.carnet', ['roles' => config('constants.rol.direccion'), 'where' => ['idGrupo', '!=', $miGrupo]]));
        $this->panel->setBoton('profile', new BotonIcon('alumno.carnet', ['roles' => config('constants.rol.direccion'), 'where' => ['idGrupo', '!=', $miGrupo]]));
        $this->panel->setBoton('grid', new BotonImg('alumno.carnet', ['where' => ['idGrupo', '==', $miGrupo]]));
        $this->panel->setBoton('profile', new BotonIcon('alumno.carnet', ['where' => ['idGrupo', '==', $miGrupo]]));
        $this->panel->setBoton('grid', new BotonImg('alumno.baja', ['where' => ['idGrupo', '==', $miGrupo]]));
        $this->panel->setBoton('profile', new BotonIcon('alumno.baja', ['where' => ['idGrupo', '==', $miGrupo]]));
        $this->panel->setBoton('grid', new BotonImg('direccion.aFol', ['img' => 'fa-file-word-o','roles' => config('constants.rol.direccion')]));
        
        
        //$this->panel->setBoton('grid',new BotonImg('fct.asigna',['img'=>'fa-birthday-cake', 'roles'=>config('constants.rol.tutor'),'where' => ['idGrupo', '==', $miGrupo]]));
        $cursos = Curso::Activo()->get();
        foreach ($cursos as $curso) {
            if (($curso->aforo == 0) || ($curso->Cuantos() < $curso->aforo * config('constants.reservaAforo')))
                $this->panel->setBoton('grid', new BotonImg('alumnocurso.registerAlumno/' . $curso->id, ['text' => trans('messages.generic.register') . $curso->titulo, 'img' => 'fa-institution']));
        }
        $this->panel->setPestana('profile', false, 'profile.alumno_grupo');
    }

}
