<?php

namespace Intranet\Http\Controllers;

use Intranet\Botones\BotonImg;
use Intranet\Entities\Solicitud;


/**
 * Class PanelSolicitudOrientacionController
 * @package Intranet\Http\Controllers
 */
class PanelSolicitudOrientacionController extends ModalController
{


    /**
     * @var array
     */
    protected $gridFields = ['id', 'nomAlum', 'fecha', 'motiu','Situacion'];
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
        $this->panel->setBotonera([], ['show','link']);
        $this->panel->setBoton('grid', new BotonImg('solicitud.active', ['where' => ['estado', '==', '1']]));
    }

    public function active($id)
    {
        $elemento = Solicitud::findOrFail($id);
        $elemento->estado = 2;
        $elemento->save();
        return back();
    }

    

}
