<?php

namespace Intranet\Http\Controllers;

use Intranet\Botones\BotonImg;
use Intranet\Botones\BotonBasico;

/**
 * Class Modulo_cicloController
 * @package Intranet\Http\Controllers
 */
class Modulo_cicloController extends IntranetController
{
    const ROLES_ROL_ADMINISTRADOR = 'roles.rol.administrador';

    /**
     * @var string
     */
    protected $perfil = 'profesor';
    /**
     * @var string
     */
    protected $model = 'Modulo_ciclo';
    /**
     * @var array
     */
    protected $gridFields = ['id', 'Xmodulo','Xciclo','curso','enlace','Xdepartamento'];
    /**
     * @var
     */
    protected $vista;
    /**
     * @var bool
     */
    protected $modal = false;


    
    /**
     *
     */
    protected function iniBotones()
    {
        $this->panel->setBoton('index', new BotonBasico('modulo_ciclo.create', ['roles' => config(self::ROLES_ROL_ADMINISTRADOR)]));
        $this->panel->setBoton('grid', new BotonImg('modulo_ciclo.edit', ['roles' => config(self::ROLES_ROL_ADMINISTRADOR)]));
        $this->panel->setBoton('grid', new BotonImg('modulo_ciclo.delete', ['roles' => config(self::ROLES_ROL_ADMINISTRADOR)]));
    }

}
