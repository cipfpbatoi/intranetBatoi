<?php

namespace Intranet\Http\Controllers\API;

use Intranet\Entities\Falta_itaca;
use Intranet\Entities\Horario;
use Intranet\Entities\Falta_profesor;
use Intranet\Entities\Profesor;
use Illuminate\Http\Request;
use Jenssegers\Date\Date;

class FaltaItacaController extends ApiBaseController
{

    protected $model = 'Falta_itaca';

    public function potencial($dia, $idProfesor)
    {
        if (Falta_profesor::haFichado($dia, $idProfesor)->count()) {
            $horas = Horario::Profesor($idProfesor)
                    ->Dia(nameDay($dia))
                    ->whereNotIn('modulo', config('constants.modulosNoLectivos'))
                    ->whereNull('ocupacion')
                    ->orderBy('sesion_orden')
                    ->get();
            $horasJ = [];
            foreach ($horas as $hora) {

                $horasJ[$hora->id]['idProfesor'] = $idProfesor;
                $horasJ[$hora->id]['desde'] = $hora->desde;
                $horasJ[$hora->id]['hasta'] = $hora->hasta;
                $horasJ[$hora->id]['idGrupo'] = $hora->idGrupo;
                $horasJ[$hora->id]['sesion_orden'] = $hora->sesion_orden;
                $horasJ[$hora->id]['dia'] = $dia;

                if ($falta = Falta_itaca::where('idProfesor', $idProfesor)
                        ->where('sesion_orden', $hora->sesion_orden)
                        ->where('dia', $dia)
                        ->first()) {
                    $horasJ[$hora->id]['checked'] = TRUE;
                    $horasJ[$hora->id]['justificacion'] = $falta->justificacion;
                    $horasJ[$hora->id]['enCentro'] = $falta->enCentro;
                    $horasJ[$hora->id]['estado'] = $falta->estado;
                } else {
                    $horasJ[$hora->id]['checked'] = FALSE;
                    $horasJ[$hora->id]['estado'] = 0;
                    $horasJ[$hora->id]['justificacion'] = '';
                    if (estaInstituto($idProfesor, $dia, sumarHoras($hora->desde, '00:30:00')))
                        $horasJ[$hora->id]['enCentro'] = TRUE;
                    else
                        $horasJ[$hora->id]['enCentro'] = FALSE;
                }
            }
            return $this->sendResponse($horasJ, 'OK');
        } else
            return $this->sendResponse([], 'OK');
    }

    public function guarda(Request $request)
    {
        $alta = FALSE;
        $respuesta = [];
        foreach ($request->toArray() as $hora) {
            if (isset($hora['idProfesor'])) {
                if ($falta = Falta_itaca::where('idProfesor', $hora['idProfesor'])
                        ->where('sesion_orden', $hora['sesion_orden'])
                        ->where('dia', $hora['dia'])
                        ->first()) {
                    if (!$hora['checked']){
                        $falta->delete();
                        $respuesta[$hora['sesion_orden']]='No comunicada';
                    }
                    else {
                        $falta->justificacion = $hora['justificacion'];
                        $falta->save();
                    }
                } else

                if ($hora['checked']) {
                    //dd($hora);
                    $dia = new Date($hora['dia']);
                    $falta = new Falta_itaca();
                    $falta->idProfesor = $hora['idProfesor'];
                    $falta->dia = $dia->format('Y-m-d');
                    $falta->sesion_orden = $hora['sesion_orden'];
                    $falta->idGrupo = $hora['idGrupo'];
                    $falta->enCentro = $hora['enCentro'];
                    $falta->justificacion = $hora['justificacion'];
                    $falta->save();
                    $alta = $falta->id;
                    $respuesta[$hora['sesion_orden']]='Pendent';
                }
            }
        }
        if ($alta)  Falta_itaca::putEstado($alta,1,'Birret Pendent');
        
        return $this->sendResponse($respuesta, 'OK');
    }

}
