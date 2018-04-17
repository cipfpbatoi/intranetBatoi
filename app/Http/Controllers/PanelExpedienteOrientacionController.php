<?php

namespace Intranet\Http\Controllers;

use Intranet\Entities\Expediente;


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
        
    }
    protected function search()
    {
        return Expediente::where('tipo',4)->get();
    }

    

}
