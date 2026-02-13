<?php

namespace Intranet\Http\Controllers;

use Intranet\Http\Controllers\Core\BaseController;

use Intranet\Entities\Expediente;
use Intranet\Entities\TipoExpediente;
use Intranet\UI\Botones\BotonImg;

/**
 * Class PanelExpedienteOrientacionController
 * @package Intranet\Http\Controllers
 */
class PanelProcedimientoAcompanyantController extends BaseController
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



    protected function iniPestanas($parametres = null){
        $this->panel->setPestana('profile', true, null, null, null, 'index', $this->parametresVista);
    }

    /**
     *
     */
    protected function iniBotones()
    {
        $this->panel->setBothBoton('expediente.show');
        $this->panel->setBothBoton('expediente.link');
        
    }

    /**
     * @return mixed
     */
    protected function search()
    {
        return Expediente::whereIn('tipo', hazArray(TipoExpediente::where('orientacion',2)->get(), 'id'))->where('estado',5)->where('idAcompanyant',AuthUser()->dni)->get();
    }

    

}
