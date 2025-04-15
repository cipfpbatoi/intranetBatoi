<?php

namespace Intranet\Http\Controllers\API;

use Illuminate\Http\Request;
use Intranet\Entities\Articulo;
use Intranet\Entities\Lote;
use Intranet\Http\Requests;
use Intranet\Http\Controllers\Controller;
use Intranet\Http\Controllers\API\ApiBaseController;
use Carbon\Carbon;

class ArticuloController extends ApiBaseController
{

    protected $model = 'Articulo';

    public function index()
    {
        $data = Articulo::orderBy('descripcion')->get();
        return $this->sendResponse($data, 'OK');
    }

}
