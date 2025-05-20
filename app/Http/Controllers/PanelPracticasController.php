<?php

namespace Intranet\Http\Controllers;

use Intranet\Botones\BotonImg;
use Intranet\Entities\Grupo;
use Intranet\Entities\Ciclo;

class PanelPracticasController extends BaseController
{

    protected $perfil = 'profesor';
    protected $model = 'Grupo';
    protected $gridFields = [
        'nombre',
        'Matriculados',
        'Resfct',
        'Exentos',
        'Respro',
        'Resempresa',
        'Acta',
        'Calidad',
        'Xtutor'
    ];
    
    protected function iniBotones()
    {
        $this->panel->setBoton('grid',new BotonImg(
            'direccion.acta',
            [
                'img' => 'fa-file-word-o',
                'roles' => config('roles.rol.direccion'),
                'where' => ['acta_pendiente','==','1']
            ])
        );
        $this->panel->setBoton('grid',new BotonImg(
            'fctcap.check',
            [
                'img' => 'fa-check',
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
        return Grupo::whereIn('codigo', function($query) {
            $query->select('idGrupo')
                ->from('alumno_grupo')
                ->groupBy('idGrupo');
        })->get();
        /*$ciclos = hazArray(Ciclo::where('tipo',3)->get(),'id','id');
        return Grupo::where('curso',2)
            ->orWhereIn('idCiclo',$ciclos)
            ->get();*/
    }


    protected function show($id)
    {
        $grupo = Grupo::find($id);
        return redirect()->route('fct.linkQuality',['dni'=>$grupo->tutor]);
    }

}
