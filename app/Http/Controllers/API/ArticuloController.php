<?php

namespace Intranet\Http\Controllers\API;

use Illuminate\Http\Request;
use Intranet\Entities\Articulo;
use Intranet\Entities\Lote;
use Intranet\Http\Requests;
use Intranet\Http\Controllers\Controller;
use Illuminate\Support\Carbon;

class ArticuloController extends ApiResourceController
{

    protected $model = 'Articulo';

    public function index()
    {
        $data = Articulo::orderBy('descripcion')->get();
        return $this->sendResponse($data, 'OK');
    }

}
