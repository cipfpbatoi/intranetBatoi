<?php

namespace Intranet\Http\Controllers\API;

use Intranet\Entities\Espacio;
use Intranet\Entities\Reserva;
use Intranet\Entities\Profesor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ReservaController extends ApiBaseController
{

    protected $model = 'Reserva';
    
    public function show($cadena,$send=true){
        $data = parent::show($cadena,false);
        foreach ($data as $uno){
            if (isset($uno->Profesor->nombre)) {
                $uno->nomProfe = $uno->Profesor->ShortName;
            }
        }
        return $this->sendResponse($data, 'OK');
    }


    public function unsecure(Request $datosProfesor)
    {
        $profesor = Profesor::find($datosProfesor->dni);
        if ($datosProfesor->api_token === $profesor->api_token){
            $reserva = Reserva::where('idProfesor',$datosProfesor->dni)->where('dia',Hoy())->where('hora',sesion(hora()))->first();
            if ($reserva && $espacio=Espacio::find($reserva->idEspacio)){
                if ($espacio->dispositivo){
                    $link = str_replace('{dispositivo}',$espacio->dispositivo,config('variables.ipDomotica')).'/unsecure';
                    $response = Http::withBasicAuth('admin', 'Admin*HC3*Batoi22')->accept('application/json')->post($link,['args'=>[]]);
                    if ($response->successful()){
                        return $this->sendResponse('Porta oberta');
                    } else {
                        return $this->sendError( "No s'ha pogut obrir la porta: ".$response->status());
                    }
                } else {
                    return $this->sendError( 'Eixe espai no te obertura',401);
                }
            } else {
                return $this->sendError( 'No tens cap reserva per ara',401);
            }

        }
        return $this->sendError('Persona no identificada',401);
    }
}
