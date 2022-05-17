<?php

namespace Intranet\Http\Controllers\API;

use Intranet\Entities\Reserva;
use Illuminate\Http\Request;

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

    public function fichar(Request $datosProfesor)
    {
        $profesor = Profesor::find($datosProfesor->dni);
        if ($datosProfesor->api_token === $profesor->api_token) {
            $ultimo = Falta_profesor::fichar($profesor->dni);
            return response()->view('ficha', compact('ultimo'), 200)->header('Content-type', 'text/html');
        }
        return $this->sendResponse(['updated' => false], 'Profesor no identificado');
    }

    public function abrir(Request $datosProfesor)
    {
        $profesor = Profesor::find($datosProfesor->dni);
        if ($datosProfesor->api_token === $profesor->api_token){
            $reserva = Reserva::where('idProfesor',$datosProfesor->dni)->where('dia',Hoy())->where('hora',hora())->first();
            if ($reserva){
                return $this->sendResponse(['updated'=>true,$reserva]);
            } else {
                return $this->sendResponse(['updated' => false], 'No tens cap reserva per ara');
            }

        }
        return $this->sendResponse(['updated' => false], 'Profesor no identificado');
    }
}
