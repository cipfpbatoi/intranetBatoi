<?php

namespace Intranet\Http\Controllers;

use Intranet\Http\Controllers\Core\ModalController;
use Intranet\Http\Requests\AlumnoGrupoUpdateRequest;

use Intranet\Entities\AlumnoGrupo;
use Intranet\UI\Botones\BotonIcon;
use Intranet\UI\Botones\BotonImg;
use Intranet\Entities\Curso;

class AlumnoGrupoController extends ModalController
{
    protected $perfil = 'profesor';
    protected $model = 'AlumnoGrupo';
    /**
     * Filtre del grup actual per al llistat modal.
     *
     * @var string|null
     */
    protected $search = null;
    protected $gridFields = ['nombre', 'telef1',  'email','poblacion','drets',
        'extraescolars','DA','subGrupo','posicion','telef2'];
    const FOL = 12;

    /**
     * Punt d'entrada legacy per a rutes que passen el grup en URL.
     *
     * @param string $grupo
     * @return \Illuminate\Contracts\View\View
     */
    public function indice($grupo)
    {
        $this->search = $grupo;
        return $this->index();
    }

    public function search()
    {
        $this->titulo = ['quien' => $this->search];
        return AlumnoGrupo::where('idGrupo', $this->search)->get();
    }

    /*
     * edit($id) return vista edit

    public function edit($id)
    {
        $grupoTutoria = AuthUser()->grupoTutoria;
        $elemento = AlumnoGrupo::where('idAlumno',$id)->where('idGrupo',$grupoTutoria)->first();
        dd($grupoTutoria);
        $formulario = new FormBuilder($elemento);
        $modelo = $this->model;
        return view($this->chooseView('edit'), compact('formulario', 'modelo'));

    }
    */

    protected function redirect()
    {
        $grupoTutoria = AuthUser()->grupoTutoria;
        return redirect()->route('alumnogrupo.index', ['grupo' => $grupoTutoria]);
    }

    public function updateModal(AlumnoGrupoUpdateRequest $request, $grupo, $alumno)
    {
        $elemento = AlumnoGrupo::where('idAlumno', $alumno)->where('idGrupo', $grupo)->first(); //busca si hi ha
        if (!$elemento) {
            abort(404);
        }
        $elemento->fillAll($request);        // ompli i guarda
        return $this->redirect();
    }

    protected function realStore(AlumnoGrupoUpdateRequest $request, $id = null)
    {
        $grupoTutoria = AuthUser()->grupoTutoria;
        $elemento = AlumnoGrupo::where('idAlumno', $id)->where('idGrupo', $grupoTutoria)->first(); //busca si hi ha
        if (!$elemento) {
            abort(404);
        }

        return $elemento->fillAll($request);        // ompli i guarda
    }

    public function update(AlumnoGrupoUpdateRequest $request, $id)
    {
        $this->realStore($request, $id);
        return $this->redirect();
    }

    protected function iniBotones()
    {
        $grupoTutoria = AuthUser()->grupoTutoria;
        $this->panel->setBoton('grid', new BotonImg('alumno.edit', ['img' => 'fa-tags','roles' => config('roles.rol.direccion'), 'where' => ['idGrupo', '!=',  $grupoTutoria]]));
        $this->panel->setBoton('grid', new BotonImg('alumno.edit', ['img' => 'fa-tags','where' => ['idGrupo', '==',  $grupoTutoria]]));
        $this->panel->setBoton('grid', new BotonImg('alumno_grupo.edit', ['where' => ['idGrupo', '==',  $grupoTutoria]]));
        $this->panel->setBoton('grid', new BotonImg('alumno.carnet', ['roles' => config('roles.rol.direccion'), 'where' => ['idGrupo', '!=',  $grupoTutoria]]));
        $this->panel->setBoton('profile', new BotonIcon('alumno.carnet', ['roles' => config('roles.rol.direccion'), 'where' => ['idGrupo', '!=',  $grupoTutoria]]));
        $this->panel->setBoton('grid', new BotonImg('alumno.carnet', ['where' => ['idGrupo', '==',  $grupoTutoria]]));
        $this->panel->setBoton('profile', new BotonIcon('alumno.carnet', ['where' => ['idGrupo', '==',  $grupoTutoria]]));
        $this->panel->setBoton('grid', new BotonImg('direccion.aFol', ['img' => 'fa-file-word-o','roles' => config('roles.rol.direccion')]));
        if (AuthUser()->departamento == self::FOL && date('Y-m-d')>config('variables.certificatFol')) {
            $this->panel->setBoton('grid', new BotonImg('alumno.checkFol', ['img' => 'fa-square-o', 'where' => ['fol', '==', 0]]));
            $this->panel->setBoton('grid', new BotonImg('alumno.checkFol', ['img' => 'fa-check', 'where' => ['fol', '==', 1]]));
        }
        $cursos = Curso::Activo()->get();
        foreach ($cursos as $curso) {
            if (($curso->aforo == 0) || ($curso->NAlumnos < $curso->aforo * config('variables.reservaAforo'))) {
                $this->panel->setBoton('grid', new BotonImg('alumnocurso.registerAlumno/' . $curso->id, ['text' => trans('messages.generic.register') . $curso->titulo, 'img' => 'fa-institution']));
            }
        }
        $this->panel->setBoton(
            'grid',
            new BotonImg(
                'alumno.days',
                [
                    'img' => 'fa-calendar',
                    'roles' => config('roles.rol.tutor'),
                    'text'=>'Modificar Calendari'
                ]
            )
        );
        
    }

}
