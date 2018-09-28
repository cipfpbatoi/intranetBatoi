<?php

namespace Intranet\Http\Controllers;

use Intranet\Botones\BotonImg;
use Intranet\Botones\BotonBasico;

class Modulo_cicloController extends IntranetController
{
    
    protected $perfil = 'profesor';
    protected $model = 'Modulo_ciclo';
    protected $gridFields = ['id', 'Xmodulo','Xciclo','curso','enlace','Xdepartamento'];
    protected $vista;
    protected $modal = false;

    
    protected function iniBotones()
    {
        $this->panel->setBoton('index', new BotonBasico('modulo_ciclo.create', ['roles' => config('roles.rol.administrador')]));
        $this->panel->setBoton('grid', new BotonImg('modulo_ciclo.edit', ['roles' => config('roles.rol.administrador')]));
        $this->panel->setBoton('grid', new BotonImg('modulo_ciclo.delete', ['roles' => config('roles.rol.administrador')]));
    }

}
