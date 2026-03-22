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

/**
 * Class CalendariFctController
 *
 * Controla el llistat d'alumnat per a gestionar el calendari FCT.
 */
class CalendariFctController extends ModalController
{
    protected $perfil = 'profesor';
    protected $model = 'Alumno';
    /**
     * Filtre addicional per al títol del llistat, si escau.
     *
     * @var string|null
     */
    protected ?string $search = null;
    protected $gridFields = ['fullName'  ];

    /**
     * Recupera els alumnes del professor autenticat.
     *
     * @return \Illuminate\Support\Collection
     */
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
    /**
     * Mostra el calendari FCT d'un alumne.
     *
     * @param int|string $id
     * @return \Illuminate\Contracts\View\View
     */
    public function days($id)
    {
        $alumno  = Alumno::find($id);
         return view('fct.days',compact('alumno'));
    }


}
