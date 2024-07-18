<?php

namespace Intranet\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Intranet\Botones\BotonImg;
use Intranet\Botones\BotonBasico;
use Intranet\Entities\Modulo_grupo;
use Intranet\Services\JWTTokenService;

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
        return Modulo_grupo::MisModulos();
    }

    protected function link($id)
    {
        $modulo = Modulo_grupo::find($id);
        return redirect()->away($modulo->TokenLink);
    }

}
