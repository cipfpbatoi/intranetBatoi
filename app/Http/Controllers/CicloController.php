<?php

namespace Intranet\Http\Controllers;

use Intranet\Botones\BotonImg;
use Intranet\Botones\BotonBasico;

/**
 * Class CicloController
 * @package Intranet\Http\Controllers
 */
class CicloController extends IntranetController
{

    /**
     * @var string
     */
    protected $perfil = 'profesor';
    /**
     * @var string
     */
    protected $model = 'Ciclo';
    /**
     * @var array
     */
    protected $gridFields = [ 'ciclo','literal','Xdepartamento','Xtipo','normativa','titol','rd','rd2'];
    /**
     * @var
     */
    protected $vista;
    /**
     * @var bool
     */
    protected $modal = true;

    /**
     *
     */
    protected function iniBotones()
    {
        $this->panel->setBoton('index', new BotonBasico('ciclo.create', ['roles' => config('roles.rol.administrador')]));
        $this->panel->setBoton('grid', new BotonImg('ciclo.edit', ['roles' => config('roles.rol.administrador')]));
        $this->panel->setBoton('grid', new BotonImg('ciclo.delete', ['roles' => config('roles.rol.administrador')]));
    }

}
