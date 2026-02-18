<?php

namespace Intranet\Http\Controllers\API;

use Illuminate\Http\Request;
use Intranet\Entities\Expediente;
use Intranet\Http\Requests;
use Intranet\Http\Controllers\Controller;

class ExpedienteController extends ApiResourceController
{

    protected $model = 'Expediente';

}
