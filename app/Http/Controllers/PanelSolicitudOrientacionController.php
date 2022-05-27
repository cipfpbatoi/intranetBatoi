<?php

namespace Intranet\Http\Controllers;

use Intranet\Entities\Expediente;
use Intranet\Entities\TipoExpediente;
use Intranet\Botones\BotonImg;


/**
 * Class PanelSolicitudOrientacionController
 * @package Intranet\Http\Controllers
 */
class PanelSolicitudOrientacionController extends BaseController
{

    /**
     * @var array
     */
    protected $gridFields = ['id', 'nomAlum', 'fecha', 'text1','Situacion'];
    /**
     * @var string
     */
    protected $perfil = 'profesor';
    /**
     * @var string
     */
    protected $model = 'Solicitud';
    /**
     * @var string
     */
    protected $orden = 'fecha';


    /**
     *
     */
    protected function iniBotones()
    {
        $this->panel->setBotonera([], ['show']);
        $this->panel->setBoton('grid', new BotonImg('solicitud.active', ['where' => ['estado', '==', '1']]));
    }



    

}
