<?php

namespace Intranet\Http\Controllers;

use Intranet\Http\Controllers\Core\ModalController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Intranet\Entities\AlumnoGrupo;
use Illuminate\Support\Facades\Auth;
use Intranet\UI\Botones\BotonBasico;
use Intranet\UI\Botones\BotonIcon;
use Intranet\UI\Botones\BotonImg;
use Intranet\Entities\Grupo;
use Intranet\Entities\Curso;
use Intranet\Entities\Alumno;
use Intranet\Services\UI\FormBuilder;

class AlumnoGrupoController extends ModalController
{
    protected $perfil = 'profesor';
    protected $model = 'AlumnoGrupo';
    protected $gridFields = ['nombre', 'telef1',  'email','poblacion','drets',
        'extraescolars','DA','subGrupo','posicion','telef2'];
    const FOL = 12;
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
        return redirect()->action('AlumnoGrupoController@indice',['grupo'=>$grupoTutoria]);
    }

    public function updateModal(Request $request, $grupo, $alumno)
    {
        $elemento = AlumnoGrupo::where('idAlumno', $alumno)->where('idGrupo', $grupo)->first(); //busca si hi ha
        $this->validateByModelRules($request, $elemento);
        $elemento->fillAll($request);        // ompli i guarda
        return $this->redirect();
    }

    protected function realStore(Request $request, $id = null)
    {
        $grupoTutoria = AuthUser()->grupoTutoria;
        $elemento = AlumnoGrupo::where('idAlumno', $id)->where('idGrupo', $grupoTutoria)->first(); //busca si hi ha
        $this->validateByModelRules($request, $elemento);

        return $elemento->fillAll($request);        // ompli i guarda
    }

    public function update(Request $request, $id)
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

    private function validateByModelRules(Request $request, $elemento): void
    {
        if (!$elemento) {
            abort(404);
        }

        $rules = method_exists($elemento, 'getRules') ? $elemento->getRules() : [];
        if (!empty($rules)) {
            $this->validate($request, $rules);
        }
    }

}
