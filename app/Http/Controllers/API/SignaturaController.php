<?php

namespace Intranet\Http\Controllers\API;

use DB;
use Intranet\Entities\Signatura;
use Intranet\Services\DigitalSignatureService;

class SignaturaController extends ApiBaseController
{
    protected $model = 'Signatura';
    
    /**
     * @param $cadena
     * @param bool $send
     * @return array
     */

    public function show($cadena, $send = true)
    {
        $signatura = Signatura::findOrFail($cadena);

        $data = DigitalSignatureService::validateUserSign($signatura->routeFile);

        if (is_null($data)) {
            // Document no signat o signatura incompatible
            return [
                'signed' => false,
                'message' => 'El document no està signat digitalment amb un format compatible.',
            ];
        }

        // Document signat correctament
        return [
            'signed' => true,
            'data' => $data,
        ];
    }
}
