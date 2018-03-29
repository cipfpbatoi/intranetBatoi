<?php

namespace Intranet\Http\Controllers;


class PanelExpedienteController extends BaseController
{
    use traitPanel;

    protected $gridFields = ['id', 'nomAlum', 'fecha', 'Xtipo', 'Xmodulo', 'situacion'];
    protected $perfil = 'profesor';
    protected $model = 'Expediente';
    protected $orden = 'fecha';

    
    protected function iniBotones()
    {
        $this->panel->setBotonera([], ['delete', 'edit']);
        $this->setAuthBotonera();
    }

    

}
