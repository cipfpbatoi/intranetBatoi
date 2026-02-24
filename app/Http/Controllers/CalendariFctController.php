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

class CalendariFctController extends ModalController
{
    protected $perfil = 'profesor';
    protected $model = 'Alumno';
    protected $gridFields = ['fullName'  ];
    public function search()
    {
        $this->titulo = ['quien' => $this->search];
        return Alumno::misAlumnos()->get();
    }



    protected function iniBotones()
    {
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
    public function days($id)
    {
        $alumno  = Alumno::find($id);
         return view('fct.days',compact('alumno'));
    }


}
