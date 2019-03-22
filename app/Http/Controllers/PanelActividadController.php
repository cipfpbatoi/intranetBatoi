<?php

namespace Intranet\Http\Controllers;

use Intranet\Botones\BotonIcon;


/**
 * Class PanelActividadController
 * @package Intranet\Http\Controllers
 */
class PanelActividadController extends BaseController
{

    use traitPanel;

    /**
     * @var string
     */
    protected $perfil = 'profesor';
    /**
     * @var string
     */
    protected $model = 'Actividad';
    /**
     * @var array
     */
    protected $gridFields = ['name', 'desde', 'hasta', 'situacion'];
    /**
     * @var array
     */
    protected $parametresVista = ['before' => [] , 'modal' => ['explicacion']];


    /**
     *
     */
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
