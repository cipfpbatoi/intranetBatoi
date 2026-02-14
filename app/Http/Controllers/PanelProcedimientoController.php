<?php

namespace Intranet\Http\Controllers;

use Intranet\Http\Controllers\Core\BaseController;

use Illuminate\Support\Facades\Session;
use Intranet\UI\Botones\BotonIcon;
use Intranet\UI\Panels\Panel;
use Intranet\Entities\Expediente;
use Intranet\Entities\TipoExpediente;
use Intranet\UI\Botones\BotonImg;


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

    public function index()
    {
        $todos = $this->search();

        $this->panel->setPestana("Per_assignar",  true , 'profile.expediente',
            ['estado',4],null,'index',$this->parametresVista);
        $this->panel->setPestana("Assignades",  false , 'profile.expediente',
            ['estado',5],null,null,$this->parametresVista);
        $this->iniBotones();
        return $this->grid($todos);
    }


    /**
     *
     */
    protected function iniBotones()
    {

        if (esRol(AuthUser()->rol,config('roles.rol.direccion'))) {
            $this->panel->setBoton('profile',new BotonImg('expediente.user', ['text' => 'Assignar Acompanyant', 'img' => 'fa-user', 'class' => 'user', 'where' => ['estado', '==', 4]]));
        }
        $this->panel->setBoton('profile', new BotonImg('expediente.link'));
        $this->panel->setBoton('profile', new BotonImg('expediente.show'));
        if (esRol(AuthUser()->rol,config('roles.rol.direccion'))) {
            $this->panel->setBoton('profile', new BotonImg('expediente.delete'));
        }
    }

    /**
     * @return mixed
     */
    protected function search()
    {
        return Expediente::whereIn('tipo', hazArray(TipoExpediente::where('orientacion',2)->get(), 'id'))->where('estado','>=',4)->get();
    }

    

}
