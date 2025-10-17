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

        if (!$data = DigitalSignatureService::validateUserSign($signatura->routeFile)) {
            return response()->view('errors.signatura', [
                'missatge' => 'Has d’estar autenticat per validar la signatura.'
            ], 403);
        }

        return ['data'=> $data];
    }
}
