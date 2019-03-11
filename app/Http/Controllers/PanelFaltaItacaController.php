<?php

namespace Intranet\Http\Controllers;

use Intranet\Botones\BotonIcon;

/**
 * Class PanelFaltaItacaController
 * @package Intranet\Http\Controllers
 */
class PanelFaltaItacaController extends BaseController
{
    use traitPanel;

    /**
     * @var string
     */
    protected $perfil = 'profesor';
    /**
     * @var string
     */
    protected $model = 'Falta_itaca';
    /**
     * @var string
     */
    protected $orden = 'dia';
    /**
     * @var bool
     */
    protected $notFollow = true;
    /**
     * @var array
     */
    protected $gridFields = ['nombre','dia','horas','justificacion','fichaje','Xestado'];
    /**
     * @var array
     */
    protected $parametresVista = ['modal' => ['explicacion']];

    /**
     *
     */
    protected function iniBotones()
    {
        $this->panel->setBoton('profile', new BotonIcon("$this->model.resolve", ['class' => 'btn-success authorize', 'where' => ['estado', '!=', '2']], true));
        $this->panel->setBoton('profile', new BotonIcon("$this->model.refuse", ['class' => 'btn-danger refuse', 'where' => ['estado', '>', '0','estado','<','3']], true));
        $this->panel->setBothBoton('itaca.gestor',['img' => 'fa-eye', 'where'=>['idDocumento','!=',null]]);
        
    }
    
    
}
