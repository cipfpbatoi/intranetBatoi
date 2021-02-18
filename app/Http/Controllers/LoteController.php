<?php

namespace Intranet\Http\Controllers;

use Intranet\Botones\BotonBasico;

/**
 * Class LoteController
 * @package Intranet\Http\Controllers
 */
class LoteController extends IntranetController
{

    /**
     * @var string
     */
    protected $perfil = 'profesor';
    /**
     * @var string
     */
    protected $model = 'Lote';
    /**
     * @var array
     */
    protected $vista = ['index' => 'Lote'];


    protected $gridFields = [ 'registre', 'proveedor','procedencia', 'estado','fechaAlta'];



    protected function iniBotones()
    {
        $this->panel->setBoton('index', new BotonBasico('direccion.lote.create', ['text'=>'Nova Factura','roles' => config('roles.rol.direccion')]));
    }

}
