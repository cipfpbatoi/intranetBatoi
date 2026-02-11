<?php

namespace Intranet\Http\Controllers;

use Intranet\Http\Controllers\Core\BaseController;

use Intranet\UI\Botones\BotonBasico;
use Intranet\UI\Botones\BotonIcon;
use Intranet\Http\Traits\Core\Panel;


/**
 * Class PanelComisionController
 * @package Intranet\Http\Controllers
 */
class PanelComisionController extends BaseController
{
    use Panel;

    /**
     * @var array
     */
    protected $gridFields = ['id', 'nombre','servicio', 'desde','total', 'situacion'];
    /**
     * @var string
     */
    protected $perfil = 'profesor';
    /**
     * @var string
     */
    protected $model = 'Comision';

    protected $parametresVista = ['before' => [] , 'modal' => ['explicacion']];


    /**
     *
     */
    protected function iniBotones()
    {
        $this->panel->setBotonera([], ['delete', 'edit', 'notification']);
        $this->setAuthBotonera();
        $this->panel->setBoton(
            'index',
            new BotonBasico(
                "comision.paid",
                ['id'=>'paid' ,'class'=>'paid'],
                true
            )
        );
        $this->panel->setBoton(
            'profile',
            new BotonIcon(
                "$this->model.unauthorize",
                ['text' => 'NO pagar' , 'class' => 'btn-danger unauthorize', 'where' => ['estado', '==', '4']],
                true
            ));
        $this->panel->setBoton(
            'profile',
            new BotonIcon(
                "$this->model.unauthorize",
                ['text' => 'TORNAR pagar' ,'class' => 'btn-danger unauthorize', 'where' => ['estado', '==', '5']],
                true
            ));
        $this->panel->setBothBoton('comision.gestor', ['img' => 'fa-archive', 'where'=>['idDocumento','!=',null]]);
        
    }
}
