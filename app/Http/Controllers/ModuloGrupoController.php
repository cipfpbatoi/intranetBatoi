<?php

namespace Intranet\Http\Controllers;

use Intranet\Http\Controllers\Core\ModalController;

use Intranet\UI\Botones\BotonImg;
use Intranet\Services\Auth\JWTTokenService;
use Intranet\Services\School\ModuloGrupoService;

/**
 * Class Modulo_cicloController
 * @package Intranet\Http\Controllers
 */
class ModuloGrupoController extends ModalController
{

    /**
     * @var string
     */
    protected $perfil = 'profesor';
    /**
     * @var string
     */
    protected $model = 'Modulo_grupo';
    /**
     * @var array
     */
    protected $gridFields = ['id', 'Xmodulo','Xciclo','Xdepartamento'];
    /**
     * @var
     */
    protected $vista;
    /**
     *
     */
    protected function iniBotones()
    {
        $this->panel->setBoton('grid', new BotonImg('modulo_grupo.link'));
    }

    protected function search()
    {
        return app(ModuloGrupoService::class)->misModulos(AuthUser()->dni);
    }

    protected function link($id)
    {
         return redirect()->away(JWTTokenService::getTokenLink($id));
    }

}
