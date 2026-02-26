<?php

namespace Intranet\Http\Controllers;

use Intranet\UI\Botones\BotonImg;
use Illuminate\Support\Facades\Gate;
use Intranet\Entities\Empresa;


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
        Gate::authorize('viewAny', Empresa::class);
        return $this->empreses()->erasmusList();
    }

    /**
     *
     */
    protected function iniBotones()
    {
       Gate::authorize('viewAny', Empresa::class);
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
