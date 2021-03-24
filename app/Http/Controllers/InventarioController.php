<?php
namespace Intranet\Http\Controllers;

use Intranet\Entities\Lote;
use Illuminate\Support\Facades\Auth;
use Intranet\Botones\BotonImg;
use Intranet\Botones\BotonBasico;
use Intranet\Entities\Material;

/**
 * Class MaterialController
 * @package Intranet\Http\Controllers
 */
class InventarioController extends IntranetController
{

    /**
     * @var string
     */
    protected $perfil = 'profesor';
    /**
     * @var string
     */
    protected $model = 'Inventario';
    /**
     * @var array
     */
    protected $vista = ['index' => 'Material'];
    /**
     * @var array
     */
    protected $gridFields = ['id', 'descripcion', 'Estado', 'espacio'];
    /**
     * @var array
     */

    /**
     * MaterialController constructor.
     */
    public function __construct()
    {
        $this->middleware($this->perfil);
        parent::__construct();
    }

    public function search(){

    }


}
