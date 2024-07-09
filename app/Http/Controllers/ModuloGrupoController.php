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
        $service = new JWTTokenService();
        $token = $service->createTokenProgramacio($id);

        // Construeix la URL amb el token
        $url = "https://pcompetencies.cipfpbatoi.es/login/auth/{$token}";
        // Redirigeix l'usuari a la URL externa amb el token
        return redirect()->away($url);
    }

}
