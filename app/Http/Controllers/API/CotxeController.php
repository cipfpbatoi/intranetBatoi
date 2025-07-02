<?php

namespace Intranet\Http\Controllers\API;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Intranet\Entities\Espacio;
use Intranet\Entities\Profesor;
use Intranet\Entities\Reserva;

class CotxeController extends ApiResourceController
{

    protected $model = 'Cotxe';

    public function eventPorta(Request $eventPorta)
    {
         //Log::info($eventPorta->all());
         Log::info($eventPorta->getContent());
    }

    private function getJson($dispositivo)
    {
        $user = config('variables.domotica.user');
        $pass =  config('variables.domotica.pass');
        $link = 'http://172.16.10.74/api/devices/'.$dispositivo;
        $response = Http::withBasicAuth($user, $pass)
            ->accept('application/json')
            ->get($link);
        return $response->json();
    }

}
