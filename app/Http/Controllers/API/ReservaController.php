<?php

namespace Intranet\Http\Controllers\API;

use Intranet\Entities\Espacio;
use Intranet\Entities\Reserva;
use Intranet\Entities\Profesor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class   ReservaController extends ApiBaseController
{

    protected $model = 'Reserva';
    
    public function show($cadena, $send=true)
    {
        $data = parent::show($cadena, false);
        foreach ($data as $uno) {
            if (isset($uno->Profesor->nombre)) {
                $uno->nomProfe = $uno->Profesor->ShortName;
            }
        }
        return $this->sendResponse($data, 'OK');
    }


    public function unsecure(Request $datosProfesor)
    {
        $profesor = Profesor::find($datosProfesor->dni);
        if ($datosProfesor->api_token === $profesor->api_token) {
            $reserva = Reserva::where('idProfesor', $datosProfesor->dni)
                ->where('dia', Hoy())
                ->where('hora', sesion(hora()))
                ->first();
            if ($reserva && $espacio=Espacio::find($reserva->idEspacio)) {
                if ($espacio->dispositivo) {
                    $action = $closed?'unsecure':'secure';
                    if ($this->action($action, $espacio)) {
                        return $this->sendResponse('Modificat estat Porta');
                    }
                    return $this->sendError("No s'ha pogut modificar la porta");
                }
                return $this->sendError('Eixe espai no te obertura', 401);
            }

            $reserva = Reserva::where('idProfesor', $datosProfesor->dni)
                ->where('dia', Hoy())
                ->first();
            if ($reserva && $espacio=Espacio::find($reserva->idEspacio)) {
                if ($espacio->dispositivo) {
                    if ($this->action('secure', $espacio)) {
                        return $this->sendResponse('Porta Tancada');
                    }
                    return $this->sendError("No s'ha pogut tancar la porta");
                }
                return $this->sendError('Eixe espai no te obertura', 401);
            }
            return $this->sendError('No tens cap reserva per ara', 401);
        }
        return $this->sendError('Persona no identificada', 401);
    }

    private function getJson($dispositivo)
    {
        $user = config('variables.domotica.user');
        $pass = config('variables.domotica.pass');
        $link = 'http://172.16.10.74/api/devices/'.$dispositivo;

        // Llança excepció si no és 2xx, així no tractem HTML/JSON d’error com si fora vàlid
        $response = Http::withBasicAuth($user, $pass)
            ->acceptJson()
            ->get($link)
            ->throw();

        return $response->json(); // array|mixed
    }

    private function action($action, $espacio): bool
    {
        $user = config('variables.domotica.user');
        $pass =  config('variables.domotica.pass');
        $link = str_replace(
            '{dispositivo}',
            $espacio->dispositivo,
            config('variables.ipDomotica')
            )."/".$action;
        $response = Http::withBasicAuth($user, $pass)
            ->accept('application/json')
            ->post($link, ['args'=>[]]);
        return $response->successful()?true:false;
    }

    private function checkSecuredStatus($data) {
        $secured = $data['properties']['secured'];
        return ($secured>0)?true:false;
    }
}
