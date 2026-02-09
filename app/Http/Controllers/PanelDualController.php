<?php

namespace Intranet\Http\Controllers;

use Intranet\UI\Botones\BotonImg;
use Intranet\Entities\AlumnoFct;
use Intranet\Entities\Fct;
use Intranet\Entities\Grupo;
use Intranet\Entities\Ciclo;

class PanelDualController extends BaseController
{

    protected $perfil = 'profesor';
    protected $model = 'Grupo';
    protected $gridFields = [
        'nombre',
        'Matriculados',
        'EnDual',
        'XDual'
    ];
    
    protected function iniBotones()
    {
        $this->panel->setBoton('grid',new BotonImg(
                'fctcap.dual',
                [
                    'img' => 'fa-bullseye',
                    'roles' => config('roles.rol.jefe_practicas')
                ])
        );
        $this->panel->setBoton('grid',new BotonImg(
            'fctcap.show',
            [
                'img' => 'fa-eye',
                'roles' => config('roles.rol.jefe_practicas')
            ])
        );

        
    }
    protected function search(){
        $duals = Fct::esDual()->get();
        $grups = [];
        foreach ($duals as $dual){
            foreach ($dual->Alumnos as $alumne){
                $grupo = $alumne->Grupo->first()->codigo;
                $grups[$grupo] = $grupo;
            }
        }
        return Grupo::whereIn('codigo',$grups)
            ->get();
    }

    protected function show($id)
    {
        $grupo = Grupo::find($id);
        return redirect()->route('fct.linkQuality',['dni'=>$grupo->tutor]);
    }

}
