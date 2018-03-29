<?php

namespace Intranet\Http\Controllers\API;

use Intranet\Entities\Falta_profesor;
use Intranet\Entities\Profesor;
use Illuminate\Http\Request;
use Jenssegers\Date\Date;

class FicharController extends ApiBaseController
{

    protected $model = 'Falta_profesor';

    public function fichar(Request $datosProfesor)
    {
        $profesor = Profesor::find($datosProfesor->dni);
        if ($datosProfesor->api_token === $profesor->api_token) {
            $ultimo = Falta_profesor::Hoy($profesor->dni)
                    ->last();

            if (($ultimo == null) || ($ultimo->salida != null)) {
                $ultimo = new Falta_profesor;
                $ultimo->idProfesor = $profesor->dni;
                $ultimo->dia = date("Y-m-d", time());
                $ultimo->entrada = date("H:i:s", time());
            } else
                $ultimo->salida = date("H:i:s", time());

            $ultimo->save();
            $ultimo->profesor = $profesor->FullName;
            return response()->view('ficha', compact('ultimo'), 200)->header('Content-type', 'text/html');
        } else
            return $this->sendResponse(['updated' => false], 'Profesor no identificado');
    }

//    public function miraFicha(Request $datosProfesor)
//    {
//        $dni = $datosProfesor->dni;
//        $fec_hoy = time();
//        $que_dia = date("Y-m-d", $fec_hoy);
//        $registro = Falta_profesor::where('dia', '=', $que_dia)
//                ->where('idProfesor', '=', $dni)
//                ->get()
//                ->last();
//        return $this->sendResponse($registro, 'OK');
//    }

    public function entrefechas(Request $datos)
    {
        $registros = Falta_profesor::where('dia', '>=', $datos->desde)
                ->where('dia', '<=', $datos->hasta)
                ->where('idProfesor', '=', $datos->profesor)
                ->get();
        foreach ($registros as $registro) {
            if ($registro->salida != null) {
                if (isset($dias[$registro->dia])) {
                    $dias[$registro->dia]['horas'] = sumarHoras($dias[$registro->dia]['horas'], restarHoras($registro->entrada, $registro->salida));
                } else
                    $dias[$registro->dia] = array('fecha' => $registro->dia, 'horas' =>
                        restarHoras($registro->entrada, $registro->salida));
            }
            else {
                if (isset($dias[$registro->dia])) {
                    $dias[$registro->dia]['horas'] = sumarHoras($dias[$registro->dia]['horas'], "01:00:00");
                } else
                    $dias[$registro->dia] = array('fecha' => $registro->dia, 'horas' => '01:00:00');
            }
        }
        foreach ($dias as $dia) {
            $dia['horas'] = number_format(Horas($dia['horas']), 1);
            $def[] = $dia;
        }

        return $this->sendResponse(['ok'], $def);
    }
    

}
