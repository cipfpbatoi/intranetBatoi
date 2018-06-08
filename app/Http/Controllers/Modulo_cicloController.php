<?php

namespace Intranet\Http\Controllers;

use Intranet\Botones\BotonImg;
use Intranet\Botones\BotonBasico;

class Modulo_cicloController extends IntranetController
{
    
    protected $perfil = 'profesor';
    protected $model = 'Modulo_ciclo';
    protected $gridFields = ['id', 'Xmodulo','Xciclo','curso','enlace'];
    protected $vista;
    protected $modal = true;

    
    protected function iniBotones()
    {
        $this->panel->setBoton('index', new BotonBasico('modulo_ciclo.create', ['roles' => config('constants.rol.administrador')]));
        $this->panel->setBoton('grid', new BotonImg('modulo_ciclo.edit', ['roles' => config('constants.rol.administrador')]));
        $this->panel->setBoton('grid', new BotonImg('modulo_ciclo.delete', ['roles' => config('constants.rol.administrador')]));
    }

}
