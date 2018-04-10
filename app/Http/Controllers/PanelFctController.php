<?php

namespace Intranet\Http\Controllers;

use Intranet\Botones\BotonImg;
use Intranet\Botones\BotonIcon;
use Intranet\Entities\Fct;
use DB;

class PanelFctController extends BaseController
{
    
    protected $perfil = 'profesor';
    protected $model = 'Fct';
    protected $gridFields = ['Ciclo', 'Nombre', 'Centro', 'fin', 'tutor','xinstructor','id','qualificacio', 'projecte'];
    protected $profile = false;
    
    public function search()
    {
        return Fct::All();
        $repes = DB::table('fcts')
                    ->select('idAlumno')
                    ->selectRaw('count(`idAlumno`) as voltes')
                    ->groupBy('idAlumno')
                    ->having('voltes','>',1)
                    ->get();
        foreach ($repes as $repe){
            $a[] = $repe->idAlumno;
        }
                   
        return Fct::whereIn('idAlumno',$a)->get();
    }
     protected function iniBotones()
    {
        //$this->panel->setBotonera();
        $this->panel->setBoton('grid', new BotonImg('fct.delete'));
        $this->panel->setBoton('grid', new BotonImg('fct.edit'));
        $this->panel->setBoton('grid', new BotonImg('fct.show'));
    }
    
}
