<?php

namespace Intranet\Http\Controllers;

use Intranet\Botones\BotonIcon;


class PanelComisionController extends BaseController
{
    use traitPanel;

    protected $gridFields = ['id', 'nombre','servicio', 'desde','total', 'situacion'];
    protected $perfil = 'profesor';
    protected $model = 'Comision';
    
    
    protected function iniBotones()
    {
        $this->panel->setBotonera([], ['delete', 'edit', 'notification']);
        $this->setAuthBotonera(['2' => 'pdf', '1' => 'autorizar', '4' => 'paid']);
        $this->panel->setBoton('profile', new BotonIcon("$this->model.unauthorize", ['text' => 'NO pagar' , 'class' => 'btn-danger unauthorize', 'where' => ['estado', '==', '4']], true));
        $this->panel->setBoton('profile', new BotonIcon("$this->model.unauthorize", ['text' => 'TORNAR pagar' ,'class' => 'btn-danger unauthorize', 'where' => ['estado', '==', '5']], true));
        $this->panel->setBothBoton('comision.gestor',['img' => 'fa-eye', 'where'=>['idDocumento','!=',null]]);
        
    }
}
