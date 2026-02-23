<?php

namespace Intranet\Http\Controllers;

use Intranet\Http\Controllers\Core\IntranetController;

use Intranet\UI\Botones\BotonImg;
use Intranet\Services\Auth\JWTTokenService;
use Intranet\Services\School\ModuloGrupoService;

/**
 * Class Modulo_cicloController
 * @package Intranet\Http\Controllers
 */
class ModuloGrupoController extends IntranetController
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
     * @var bool
     */
    protected $modal = true;


    
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
