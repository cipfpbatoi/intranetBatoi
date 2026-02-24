<?php

namespace Intranet\Http\Controllers\API;

use Intranet\Entities\Signatura;
use Intranet\Services\Signature\DigitalSignatureService;

class SignaturaController extends ApiResourceController
{
    protected $model = 'Signatura';
    
    /**
     * @param $cadena
     * @param bool $send
     * @return array
     */

    public function show($id)
    {
        $cadena = (string) $id;
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
