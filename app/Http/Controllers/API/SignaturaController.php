<?php

namespace Intranet\Http\Controllers\API;

use DB;
use Intranet\Entities\Signatura;
use Intranet\Services\DigitalSignatureService;

class SignaturaController extends ApiBaseController
{
    protected $model = 'Signatura';

    public function show($cadena,$send=true)
    {
        $signatura = Signatura::findOrFail($cadena);

        $data = DigitalSignatureService::validateUserSign($signatura->routeFile);
        return ['data'=> $data];
    }
}
