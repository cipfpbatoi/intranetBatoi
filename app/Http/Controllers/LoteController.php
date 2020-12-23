<?php

namespace Intranet\Http\Controllers;

use Intranet\Entities\Material;
use Illuminate\Support\Facades\Auth;
use Intranet\Entities\Incidencia;
use Intranet\Botones\BotonImg;
use Intranet\Botones\BotonBasico;

/**
 * Class MaterialController
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
    //protected $vista = ['index' => 'Material'];
    /**
     * @var array
     */

    protected $gridFields = ['id', 'descripcion', 'proveedor', 'unidades','inventariable' ,'registre'];
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
        $this->panel->setBotonera(['create'], ['delete', 'edit', 'detalle', 'copy']);
    }

    public function detalle($id){
        return redirect(route('articulo.lote',$id));
    }

}
