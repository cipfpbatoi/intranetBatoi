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
    /**
     * @var array
     */
    //protected $modal = true;

    protected $gridFields = [ 'registre', 'proveedor','procedencia', 'estado','fechaAlta'];
    /**
     * @var array
     */
    //protected $parametresVista = ['modal' => ['explicacion']];

    /**
     * MaterialController constructor.
     */
    public function __construct()
    {
        $this->middleware($this->perfil);
        parent::__construct();
    }

    /**
     *
     */

    protected function iniBotones()
    {
        $this->panel->setBoton('index', new BotonBasico('lote.create', ['roles' => [config('roles.rol.direccion'), config('roles.rol.mantenimiento')]]));
    }

    public function detalle($id){
        return redirect(route('articulo.lote',$id));
    }

}
