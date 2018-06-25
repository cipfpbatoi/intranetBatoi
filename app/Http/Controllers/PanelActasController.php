<?php

namespace Intranet\Http\Controllers;

use Intranet\Botones\BotonBasico;
use Intranet\Entities\Grupo;
use Intranet\Entities\Fct;

class PanelActasController extends BaseController
{

    protected $perfil = 'profesor';
    protected $model = 'Fct';
    protected $gridFields = ['Nombre', 'hasta', 'horas', 'qualificacio', 'projecte'];
    protected $vista = ['index' => 'intranet.list'] ;   
    
    protected function iniBotones()
    {
        if (Grupo::findOrFail($this->search)->acta_pendiente)
            $this->panel->setBoton('index', new BotonBasico("direccion.$this->search.finActa",['text'=>'acta']));
        
    }
    
    protected function search(){
        $grupo = Grupo::findOrFail($this->search);
        $this->titulo = ['quien' => $grupo->nombre ];
        if ($grupo->acta_pendiente)
            return Fct::Grupo($grupo)->Pendiente()->get();
        else 
            return Fct::Grupo($grupo)->get();
    }

}
