<?php
namespace Intranet\Http\Controllers;

use Illuminate\Support\Facades\Session;
use Intranet\Entities\Lote;
use Illuminate\Support\Facades\Auth;
use Intranet\Botones\BotonImg;
use Intranet\Botones\BotonBasico;
use Intranet\Entities\Material;
use Intranet\Entities\Incidencia;
use Intranet\Entities\TipoIncidencia;

/**
 * Class MaterialController
 * @package Intranet\Http\Controllers
 */
class MaterialBajaController extends IntranetController
{

    /**
     * @var string
     */
    protected $model = 'MaterialBaja';

    /**
     * @var array
     */
    protected $gridFields = ['id', 'descripcion', 'espacio','fechabaja',];
    /**
     * @var array
     */



    public function search()
    {
        return Material::where('inventariable', 1)->where('estado', 3)->get();
    }
}