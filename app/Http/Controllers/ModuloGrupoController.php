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

        // Crida a la URL externa amb el token
        $response = Http::get("https://pcompetencies.cipfpbatoi.es/login/auth/{$token}");

        // Comprova la resposta
        if ($response->successful()) {
            // Tracta la resposta amb èxit
            return $response->body();
        }
            // Tracta l'error
        return response()->json(['error' => 'No s\'ha pogut realitzar la crida a la URL externa.'], $response->status());
    }

}
