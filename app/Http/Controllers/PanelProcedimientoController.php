<?php

namespace Intranet\Http\Controllers;

use Intranet\Botones\BotonIcon;
use Intranet\Botones\Panel;
use Intranet\Entities\Expediente;
use Intranet\Entities\TipoExpediente;
use Intranet\Botones\BotonImg;


/**
 * Class PanelExpedienteOrientacionController
 * @package Intranet\Http\Controllers
 */
class PanelProcedimientoController extends BaseController
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

    protected $parametresVista = ['before' => [] , 'modal' => ['select']];

/*
    protected function iniPestanas($parametres = null){
        $this->panel->setPestana('profile', true, null, null, null, 'index', $this->parametresVista);
    }
    /**
     *
     */
    protected function iniBotones()
    {
        $this->panel->setBothBoton('expediente.link');
        if (esRol(AuthUser()->rol,config('roles.rol.direccion'))) {
            $this->panel->setBoton('grid', new BotonImg('expediente.delete'));
            $this->panel->setBoton('profile',new BotonIcon('expediente.user', ['text' => 'Assignar Acompanyant', 'img' => 'fa-user', 'class' => 'btn-primary user', 'where' => ['estado', '==', 4]]));
        }
        $this->panel->setBothBoton('expediente.show');
    }

    /**
     * @return mixed
     */
    protected function search()
    {
        return Expediente::whereIn('tipo', hazArray(TipoExpediente::where('orientacion',2)->get(), 'id'))->where('estado','>=',4)->get();
    }

    

}
