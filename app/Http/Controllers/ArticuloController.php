<?php
namespace Intranet\Http\Controllers;

use Intranet\Entities\Articulo;
use Illuminate\Support\Facades\Auth;
use Intranet\Botones\BotonImg;
use Intranet\Botones\BotonBasico;

/**
 * Class MaterialController
 * @package Intranet\Http\Controllers
 */
class ArticuloController extends IntranetController
{

    /**
     * @var string
     */
    protected $perfil = 'profesor';
    /**
     * @var string
     */
    protected $model = 'Articulo';
    /**
     * @var array
     */
    //protected $vista = ['index' => 'Material'];
    /**
     * @var array
     */

    protected $gridFields = [ 'identificacion','descripcion', 'estat', 'Espacio', 'unidades'];
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

    public function search(){
        return Articulo::where('lote_id',$this->search)->get();

    }
}
