<?php

namespace Intranet\Http\Controllers;

use Intranet\Botones\BotonIcon;


class PanelActividadController extends BaseController
{

    use traitPanel;
    
    protected $perfil = 'profesor';
    protected $model = 'Actividad';
    protected $gridFields = ['name', 'desde', 'hasta', 'situacion'];
    protected $parametresVista = ['before' => [] , 'modal' => ['explicacion']];
    
    
    protected function iniBotones()
    {
        $this->panel->setBotonera([], ['delete', 'notification']);
        $this->panel->setBothBoton('actividad.detalle');
        $this->panel->setBothBoton('actividad.edit');
        $this->panel->setBoton('profile', new BotonIcon("$this->model.unauthorize", ['class' => 'btn-danger unauthorize', 'where' => ['estado', '==', '3']], true));
        $this->panel->setBothBoton('actividad.gestor',['img' => 'fa-eye', 'where'=>['idDocumento','!=',null]]);
        $this->setAuthBotonera();
    }

}
