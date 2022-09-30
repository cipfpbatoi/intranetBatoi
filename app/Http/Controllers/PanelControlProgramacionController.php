<?php

namespace Intranet\Http\Controllers;

use Intranet\Entities\Modulo_ciclo;
use Intranet\Botones\BotonIcon;
use Intranet\Botones\BotonPost;


/**
 * Class PanelControlProgramacionController
 * @package Intranet\Http\Controllers
 */
class PanelControlProgramacionController extends BaseController
{


    /**
     * @var string
     */
    protected $model = 'Modulo_ciclo';
    /**
     * @var array
     */
    protected $gridFields = ['id','Xciclo', 'Xmodulo', 'estado', 'situacion'];


    /**
     * @return \Illuminate\Database\Eloquent\Collection|Modulo_ciclo[]|mixed
     */
    protected function search()
    {
        if (UserisAllow(config('roles.rol.direccion'))) {
            return Modulo_ciclo::with('Ciclo')
                ->with('Modulo')
                ->with('Programacion')
                ->get();
        }
        return Modulo_ciclo::with('Ciclo')
            ->with('Modulo')
            ->with('Programacion')
            ->where('idDepartamento', AuthUser()->departamento)
            ->get();
    }

    protected function iniBotones()
    {
        $this->panel->setBothBoton('programacion.advise',['img' => 'fa-bell', 'where'=>['estado','==',0]]);
    }

}
