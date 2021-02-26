<?php

namespace Intranet\Http\Controllers\API;

use Illuminate\Http\Request;
use Intranet\Entities\Articulo;
use Intranet\Entities\Lote;
use Intranet\Http\Requests;
use Intranet\Http\Controllers\Controller;
use Intranet\Http\Controllers\API\ApiBaseController;
use Jenssegers\Date\Date;

class ArticuloController extends ApiBaseController
{

    protected $model = 'Articulo';

    function getMateriales($articulo){
        $lote = Articulo::find($articulo);
        if (count($lote->Materiales))
            return response()->json(['data' => $lote->Materiales]);
        else response()->json([],404);
    }



}
