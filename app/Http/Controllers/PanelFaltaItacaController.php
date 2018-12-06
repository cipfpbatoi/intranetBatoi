<?php

namespace Intranet\Http\Controllers;

use Intranet\Botones\BotonIcon;

class PanelFaltaItacaController extends BaseController
{
    use traitPanel;
    
    protected $perfil = 'profesor';
    protected $model = 'Falta_itaca';
    protected $orden = 'dia';
    protected $notFollow = true;    
    protected $gridFields = ['nombre','dia','horas','justificacion','fichaje','Xestado'];
    protected $parametresVista = ['modal' => ['explicacion']];
    
    protected function iniBotones()
    {
        $this->panel->setBoton('profile', new BotonIcon("$this->model.resolve", ['class' => 'btn-success authorize', 'where' => ['estado', '!=', '2']], true));
        $this->panel->setBoton('profile', new BotonIcon("$this->model.refuse", ['class' => 'btn-danger refuse', 'where' => ['estado', '>', '0','estado','<','3']], true));
        $this->panel->setBothBoton('itaca.gestor',['img' => 'fa-eye', 'where'=>['idDocumento','!=',null]]);
        
    }
    
    
}
