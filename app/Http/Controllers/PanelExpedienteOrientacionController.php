<?php

namespace Intranet\Http\Controllers;

use Intranet\Entities\Expediente;
use Intranet\Entities\TipoExpediente;
use Intranet\Botones\BotonImg;

class PanelExpedienteOrientacionController extends BaseController
{
    //use traitPanel;

    protected $gridFields = ['id', 'nomAlum', 'fecha', 'Xtipo', 'Short','Situacion'];
    protected $perfil = 'profesor';
    protected $model = 'Expediente';
    protected $orden = 'fecha';

    
    protected function iniBotones()
    {
        $this->panel->setBotonera([], ['show']);
        $this->panel->setBoton('grid', new BotonImg('expediente.active', ['where' => ['estado', '==', '4']]));

        
    }
    protected function search()
    {
        return Expediente::whereIn('tipo', hazArray(TipoExpediente::where('orientacion',1)->get(), 'id'))->get();
    }

    

}
