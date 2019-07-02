<?php

namespace Intranet\Http\Controllers;

use Intranet\Entities\Empresa;
use Intranet\Botones\BotonBasico;
use Intranet\Botones\BotonImg;


/**
 * Class PanelEmpresaSCController
 * @package Intranet\Http\Controllers
 */
class PanelEmpresaSCController extends BaseController
{

    /**
     * @var string
     */
    protected $perfil = 'profesor';
    /**
     * @var string
     */
    protected $model = 'Empresa';
    /**
     * @var array
     */
    protected $gridFields = ['nombre', 'direccion', 'localidad', 'telefono', 'email', 'actividad','cicles'];
    /**
     * @var array
     */
    protected $vista = ['index' => 'empresa.indexSC'];


    /**
     * @return mixed
     */
    public function search(){
        return Empresa::whereNull('concierto')->get();
    }

    /**
     *
     */
    protected function iniBotones()
    {
       $this->panel->setBoton('index', new BotonBasico("empresa.create",['roles' => [config('roles.rol.practicas'),config('roles.rol.dual')]]));
       $this->panel->setBoton('grid', new BotonImg('empresa.detalle',['roles' => [config('roles.rol.practicas'),config('roles.rol.dual')]]));
       $this->panel->setBoton('grid', new BotonImg('empresa.delete',['roles' => [config('roles.rol.practicas'),config('roles.rol.dual')]]));
    }
}
