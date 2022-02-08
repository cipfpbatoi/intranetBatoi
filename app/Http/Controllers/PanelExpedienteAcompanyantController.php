<?php

namespace Intranet\Http\Controllers;

use Intranet\Entities\Expediente;
use Intranet\Entities\TipoExpediente;
use Intranet\Botones\BotonImg;

/**
 * Class PanelExpedienteOrientacionController
 * @package Intranet\Http\Controllers
 */
class PanelExpedienteAcompanyantController extends BaseController
{

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
        $this->panel->setBothBoton('expediente.link');
        
    }

    /**
     * @return mixed
     */
    protected function search()
    {
        return Expediente::whereIn('tipo', hazArray(TipoExpediente::where('orientacion',2)->get(), 'id'))->get();
    }

    

}
