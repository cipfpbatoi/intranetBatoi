<?php

namespace Intranet\Http\Controllers;

use Intranet\Botones\BotonBasico;
use Intranet\Entities\Grupo;
use Intranet\Entities\AlumnoFctAval;

class PanelActasController extends BaseController
{

    protected $perfil = 'profesor';
    protected $model = 'AlumnoFctAval';
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
            return AlumnoFctAval::Grupo($grupo)->Pendiente()->get();
    }
    
    public function finActa($idGrupo){
        $grupo = Grupo::findOrFail($idGrupo);
        $fcts = AlumnoFctAval::Grupo($grupo)->Pendiente()->get();
        foreach ($fcts as $fct){
            $fct->actas = 2;
            $fct->save();
        }
        $grupo->acta_pendiente = 0;
        $grupo->save();
        avisa($grupo->tutor, "Ja pots passar a arreplegar l'acta del grup $grupo->nombre", "#");
        return back();
    }


}
