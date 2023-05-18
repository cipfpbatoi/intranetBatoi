<?php

namespace Intranet\Http\Controllers\API;

use Intranet\Entities\Falta_profesor;
use Intranet\Entities\IpGuardia;
use Intranet\Entities\Profesor;
use Illuminate\Http\Request;

class FicharController extends ApiBaseController
{

    protected $model = 'Falta_profesor';

    public function fichar(Request $datosProfesor)
    {
        $profesor = Profesor::find($datosProfesor->dni);
        if ($datosProfesor->api_token === $profesor->api_token) {
            $ultimo = Falta_profesor::fichar($profesor->dni);
            return response()->view('ficha', compact('ultimo'), 200)->header('Content-type', 'text/html');
        }
        return $this->sendResponse(['updated' => false], 'Profesor no identificado');
    }



    public function entrefechas(Request $datos)
    {
        $registros = Falta_profesor::where('dia', '>=', $datos->desde)
                ->where('dia', '<=', $datos->hasta)
                ->where('idProfesor', '=', $datos->profesor)
                ->get();
        foreach ($registros as $registro) {
            if ($registro->salida != null) {
                if (isset($dias[$registro->dia])) {
                    $dias[$registro->dia]['horas'] =
                        sumarHoras(
                            $dias[$registro->dia]['horas'],
                            restarHoras($registro->entrada, $registro->salida)
                        );
                } else {
                    $dias[$registro->dia] = array('fecha' => $registro->dia, 'horas' =>
                        restarHoras($registro->entrada, $registro->salida));
                }
            } else {
                if (isset($dias[$registro->dia])) {
                    $dias[$registro->dia]['horas'] = sumarHoras($dias[$registro->dia]['horas'], "01:00:00");
                } else {
                    $dias[$registro->dia] = array('fecha' => $registro->dia, 'horas' => '01:00:00');
                }
            }
        }
        foreach ($dias as $dia) {
            $dia['horas'] = number_format(Horas($dia['horas']), 1);
            $def[] = $dia;
        }
        return $this->sendResponse(['ok'], $def);
    }
}
