<?php

namespace Intranet\Http\Controllers;

use Intranet\UI\Botones\BotonIcon;
use Intranet\UI\Botones\BotonBasico;
use Intranet\UI\Botones\BotonPost;
use Intranet\Entities\Incidencia;

class PanelOrdenTrabajoController extends BaseController
{
    
    protected $perfil = 'profesor';
    protected $model = 'Incidencia';
    protected $parametresVista = ['modal' => ['explicacion','aviso']];
    
    
    public function search()
    {
        return Incidencia::where('orden',$this->search)->get(); 
    }
    
    public function iniBotones()
    {
        $this->panel->setBoton('index', new BotonBasico("$this->search.pdf", ['where'=>['estado','==',0]],"mantenimiento/ordentrabajo" ));
        $this->panel->setBoton('index', new BotonBasico("ordentrabajo.", ['text'=>trans('messages.buttons.verorden')],"mantenimiento" ));
        $this->panel->setBoton('profile', new BotonIcon("incidencia.remove", ['class' => 'btn-danger unauthorize','where'=>['estado','<',3]],'mantenimiento'));
        $this->panel->setBoton('profile', new BotonPost("incidencia.resolve", ['class' => 'resolve btn-danger unauthorize','where'=>['estado','==',2]],'mantenimiento'));
        
    }
    
    public function iniPestanas($parametres = null)
    {
        $this->panel->setPestana(trans('validation.attributes.orden').' '.$this->search, true, 'profile.incidencia',null,null,1,$this->parametresVista);
    }
    
    
}
