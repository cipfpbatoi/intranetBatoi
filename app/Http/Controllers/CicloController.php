<?php

namespace Intranet\Http\Controllers;

use Intranet\Botones\BotonImg;
use Intranet\Botones\BotonBasico;

class CicloController extends IntranetController
{
    
    protected $perfil = 'profesor';
    protected $model = 'Ciclo';
    protected $gridFields = [ 'ciclo','literal','Xdepartamento','Xtipo','normativa','titol','rd','rd2'];
    protected $vista;
    protected $modal = true;

    protected function iniBotones()
    {
        $this->panel->setBoton('index', new BotonBasico('ciclo.create', ['roles' => config('roles.rol.administrador')]));
        $this->panel->setBoton('grid', new BotonImg('ciclo.edit', ['roles' => config('roles.rol.administrador')]));
        $this->panel->setBoton('grid', new BotonImg('ciclo.delete', ['roles' => config('roles.rol.administrador')]));
    }

}
