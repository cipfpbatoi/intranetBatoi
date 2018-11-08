<?php

namespace Intranet\Http\Controllers;


use Intranet\Entities\Grupo;
use Illuminate\Support\Facades\Session;
use Intranet\Botones\BotonImg;
use Intranet\Entities\AlumnoFct;

class PanelPG0301Controller extends BaseController
{

    protected $perfil = 'profesor';
    protected $model = 'Fctcap';
    protected $gridFields = ['Nombre','Centro' ,'desde','hasta'];
    
    protected function iniBotones()
    {
         $this->panel->setBoton('grid', new BotonImg('alumnofct.pg0301', ['img' => 'fa-square-o',
            'where' => ['pg0301', '==', '0','asociacion','==',1]]));
        $this->panel->setBoton('grid', new BotonImg('alumnofct.pg0301', ['img' => 'fa-check-square-o', 
            'where' => ['pg0301', '==', '1','asociacion','==',1]]));
         Session::put('redirect', 'PanelPG0301Controller@indice');
    }
    
    protected function search(){
        $grupo = Grupo::findOrFail($this->search);
        $this->titulo = ['quien' => $grupo->nombre ];
        return AlumnoFct::Grupo($grupo)->esFct()->get();
        
    }
    
    

}
