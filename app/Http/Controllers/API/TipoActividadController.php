<?php

namespace Intranet\Http\Controllers\API;

use Illuminate\Http\Request;
use Intranet\Entities\Lote;
use Intranet\Entities\Material;
use Intranet\Http\Controllers\Controller;
use Intranet\Http\Resources\LoteResource;
use Intranet\Http\Resources\ArticuloLoteResource;


class TipoActividadController extends ApiResourceController
{

    protected $model = 'TipoActividad';


}
