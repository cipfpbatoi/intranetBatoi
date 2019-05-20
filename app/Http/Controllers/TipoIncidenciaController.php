<?php

namespace Intranet\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use DB;
use Intranet\Botones\BotonImg;
use Intranet\Botones\BotonIcon;
use Intranet\Botones\BotonBasico;
use Jenssegers\Date\Date;
use \PDF;
use Intranet\Entities\Comision;
use Intranet\Http\Controllers\BaseController;
use Intranet\Botones\Panel;

/**
 * Class ComisionController
 * @package Intranet\Http\Controllers
 */
class TipoIncidenciaController extends IntranetController
{


    /**
     * @var array
     */
    protected $gridFields = ['id', 'nombre', 'nom','profesor','tipo'];
    /**
     * @var string
     */
    protected $perfil = 'profesor';
    /**
     * @var string
     */
    protected $model = 'TipoIncidencia';
    /**
     * @var bool
     */
    protected $modal = true;

    /**
     *
     */
    protected function iniBotones()
     {
         $this->panel->setBotonera(['create'],['delete','edit']);
     }

    protected function search(){
        return $this->class::all();
    }

}
