<?php

namespace Intranet\Http\Controllers;

use Intranet\Entities\Empresa;
use Intranet\UI\Botones\BotonBasico;
use Intranet\UI\Botones\BotonImg;


/**
 * Class PanelEmpresaSCController
 * @package Intranet\Http\Controllers
 */
class PanelErasmusController extends PanelEmpresaSCController
{
    /**
     * @return mixed
     */
    public function search()
    {
        return Empresa::where('europa', 1)->get();
    }

    /**
     *
     */
    protected function iniBotones()
    {
       $this->panel->setBoton(
           'grid',
           new BotonImg(
               'empresa.detalle',
               ['roles' => [config(self::ROLES_ROL_TUTOR),config(self::ROLES_ROL_DUAL)]]
           )
       );
       $this->panel->setBoton(
           'grid',
           new BotonImg(
               'empresa.delete',
               ['roles' => [config(self::ROLES_ROL_TUTOR),config(self::ROLES_ROL_DUAL)]]
           )
       );
    }
}
