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
                    $open = $this->checkSecuredStatus($this->getJson($espacio->dispositivo));
                    $action = $open?'secure':'unsecure';
                    if ($this->action($action, $espacio)) {
                        return $this->sendResponse('Modificat estat Porta');
                    } else {
                        return $this->sendError("No s'ha pogut modificar la porta");
                    }
                } else {
                    return $this->sendError('Eixe espai no te obertura', 401);
                }
            } else {
                $reserva = Reserva::where('idProfesor', $datosProfesor->dni)
                    ->where('dia', Hoy())
                    ->first();
                if ($reserva && $espacio=Espacio::find($reserva->idEspacio)) {
                    if ($espacio->dispositivo) {
                        if ($this->action('secure', $espacio)) {
                            return $this->sendResponse('Porta Tancada');
                        } else {
                            return $this->sendError("No s'ha pogut tancar la porta");
                        }
                    } else {
                        return $this->sendError('Eixe espai no te obertura', 401);
                    }
                } else {
                    return $this->sendError('No tens cap reserva per ara', 401);
                }
            }
        }
        return $this->sendError('Persona no identificada', 401);
    }

    private function getJson($dispositivo)
    {
        $user = config('variables.domotica.user');
        $pass =  config('variables.domotica.pass');
        $link = config('variables.ipDomotica')."/".$dispositivo;
        dd($link);
        $response = Http::withBasicAuth($user, $pass)
            ->accept('application/json')
            ->get($link);
        return $response->json();
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
        dd($data);
        $secured = $data['properties']['secured'];
        if ($secured === 255) {
            return 1;
        } elseif ($secured === 0) {
            return 0;
        } else {
            return 'Error: Valor de "secured" no reconegut';
        }
    }
}
