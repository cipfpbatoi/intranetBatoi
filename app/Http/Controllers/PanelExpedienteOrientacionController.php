<?php

namespace Intranet\Http\Controllers;

use Intranet\Entities\Expediente;
use Intranet\Entities\TipoExpediente;
use Intranet\Botones\BotonImg;

/**
 * Class PanelExpedienteOrientacionController
 * @package Intranet\Http\Controllers
 */
class PanelExpedienteOrientacionController extends BaseController
{
    //use traitPanel;

    /**
     * @var array
     */
    protected $gridFields = ['id', 'nomAlum', 'fecha', 'Xtipo', 'Short','Situacion'];
    /**
     * @var string
     */
    protected $perfil = 'profesor';
    /**
     * @var string
     */
    protected $model = 'Expediente';
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
        $this->panel->setBoton('grid', new BotonImg('expediente.active', ['where' => ['estado', '==', '4']]));

        
    }

    /**
     * @return mixed
     */
    protected function search()
    {
        return Expediente::whereIn('tipo', hazArray(TipoExpediente::where('orientacion',1)->get(), 'id'))->get();
    }

    

}
