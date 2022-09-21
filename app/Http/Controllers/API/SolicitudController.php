<?php

namespace Intranet\Http\Controllers\API;

use Intranet\Http\Controllers\Controller;


class SolicitudController extends ApiResourceController
{
    protected $model = 'Solicitud';
    protected $resource = 'Intranet\Http\Resources\SolicitudResource';

}
