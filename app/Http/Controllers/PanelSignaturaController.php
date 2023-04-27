<?php

namespace Intranet\Http\Controllers;

use Intranet\Botones\BotonBasico;
use Intranet\Entities\Signatura;
use Intranet\Botones\BotonImg;
use Intranet\Entities\Expediente;
use Intranet\Entities\TipoExpediente;

/**
 * Class PanelExpedienteController
 * @package Intranet\Http\Controllers
 */
class PanelSignaturaController extends BaseController
{

    /**
     * @var array
     */
    protected $gridFields = [ 'tipus', 'profesor', 'alumne','created_at'];
    /**
     * @var string
     */
    protected $perfil = 'profesor';
    /**
     * @var string
     */
    protected $model = 'Signatura';

    /**
     * @var array
     */
    protected $parametresVista = [ 'modal' => ['signatura']];


    /**
     *
     */
    protected function iniBotones()
    {
        if (authUser()->dni === config('avisos.director') || authUser()->dni === config('avisos.errores')) {
            $this->panel->setBotonera([], ['delete']);
            $this->panel->setBoton(
                'index',
                new BotonBasico(
                    "signatura.post",
                    ['class' => 'btn-success signatura']
                )
            );
        }
    }

    /**
     * @return mixed
     */
    protected function search()
    {
        return Signatura::where('signed', 0)->get();
    }
    

}
