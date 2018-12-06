<?php

namespace Intranet\Http\Controllers;
use Intranet\Entities\Expediente;
use Intranet\Entities\TipoExpediente;

class PanelExpedienteController extends BaseController
{
    use traitPanel;

    protected $gridFields = ['id', 'nomAlum', 'fecha', 'Xtipo', 'Xmodulo', 'situacion'];
    protected $perfil = 'profesor';
    protected $model = 'Expediente';
    protected $orden = 'fecha';
    protected $parametresVista = ['before' => [] , 'modal' => ['explicacion']];
    

    
    protected function iniBotones()
    {
        $this->panel->setBotonera([], ['delete', 'edit']);
        $this->panel->setBothBoton('expediente.gestor',['img' => 'fa-eye', 'where'=>['idDocumento','!=',null]]);
        $this->setAuthBotonera();
    }

    protected function search()
    {
        return Expediente::whereIn('tipo', hazArray(TipoExpediente::where('orientacion',0)->get(), 'id'))->get();
    }
    

}
