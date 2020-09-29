<?php

namespace Intranet\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
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
    protected $gridFields = ['nombre', 'telef1',  'email','poblacion','subGrupo','posicion','telef2'];
    const FOL = 12;
    protected $modal = true;


    public function search(){
        $this->titulo = ['quien' => $this->search];
        return AlumnoGrupo::where('idGrupo',$this->search)->get();
    }

    /*
     * edit($id) return vista edit
     */
    public function edit($id)
    {
        $grupoTutoria = AuthUser()->grupoTutoria;
        $elemento = AlumnoGrupo::where('idAlumno',$id)->where('idGrupo',$grupoTutoria)->first();
        $default = $elemento->fillDefautOptions();
        $modelo = $this->model;
        return view($this->chooseView('edit'), compact('elemento', 'default', 'modelo'));
    }

    protected function redirect()
    {
        $grupoTutoria = AuthUser()->grupoTutoria;
        return redirect()->action('AlumnoGrupoController@indice',['grupo'=>$grupoTutoria]);
    }

    public function updateModal(Request $request, $grupo, $alumno)
    {
        $elemento = AlumnoGrupo::where('idAlumno',$alumno)->where('idGrupo',$grupo)->first(); //busca si hi ha
        $this->validateAll($request, $elemento);    // valida les dades

        $elemento->fillAll($request);        // ompli i guarda
        return $this->redirect();
    }

    protected function realStore(Request $request, $id = null)
    {
        $grupoTutoria = AuthUser()->grupoTutoria;
        $elemento = AlumnoGrupo::where('idAlumno',$id)->where('idGrupo',$grupoTutoria)->first(); //busca si hi ha
        $this->validateAll($request, $elemento);    // valida les dades

        return $elemento->fillAll($request);        // ompli i guarda
    }

    protected function iniBotones()
    {
        $grupoTutoria = AuthUser()->grupoTutoria;
        $this->panel->setBoton('grid', new BotonImg('alumno.muestra'));
        $this->panel->setBoton('grid', new BotonImg('alumno.edit', ['img' => 'fa-tags','roles' => config('roles.rol.direccion'), 'where' => ['idGrupo', '!=',  $grupoTutoria]]));
        $this->panel->setBoton('grid', new BotonImg('alumno.edit', ['img' => 'fa-tags','where' => ['idGrupo', '==',  $grupoTutoria]]));
        $this->panel->setBoton('grid', new BotonImg('alumno_grupo.edit', ['where' => ['idGrupo', '==',  $grupoTutoria]]));
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
