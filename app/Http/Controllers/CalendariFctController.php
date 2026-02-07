<?php

namespace Intranet\Http\Controllers;

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
use Intranet\Services\FormBuilder;

class CalendariFctController extends IntranetController
{
    protected $perfil = 'profesor';
    protected $model = 'Alumno';
    protected $gridFields = ['fullName'  ];
    protected $modal = true;


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
