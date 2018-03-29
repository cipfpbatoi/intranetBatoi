<?php

namespace Intranet\Http\Controllers\API;

use Intranet\Entities\Falta_profesor;
use Illuminate\Http\Request;
use \DB;

class FaltaProfesorController extends ApiBaseController
{

    protected $model = 'Falta_profesor';
    
    public function horas($cadena){
        $result = parent::show($cadena,false);
        //dd($result);
        foreach ($result as $registro) {
            if ($registro->salida != null) {
                if (isset($dias[$registro->idProfesor][$registro->dia])) {
                    $dias[$registro->idProfesor][$registro->dia]['horas'] = sumarHoras($dias[$registro->idProfesor][$registro->dia]['horas'], restarHoras($registro->entrada, $registro->salida));
                } else
                    $dias[$registro->idProfesor][$registro->dia] = array('idProfesor'=>$registro->idProfesor,'fecha' => $registro->dia, 'horas' =>
                        restarHoras($registro->entrada, $registro->salida));
            }
            else {
                if (isset($dias[$registro->idProfesor][$registro->dia])) {
                    $dias[$registro->idProfesor][$registro->dia]['horas'] = sumarHoras($dias[$registro->idProfesor][$registro->dia]['horas'], "01:00:00");
                } else
                    $dias[$registro->idProfesor][$registro->dia] = array('idProfesor'=>$registro->idProfesor,'fecha' => $registro->dia, 'horas' => '01:00:00');
            }
        }
        return $this->sendResponse(['ok'], $dias);
    }
    

}
