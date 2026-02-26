<?php

namespace Intranet\Http\Controllers\API;

use Intranet\Application\Horario\HorarioService;
use Intranet\Services\Notifications\NotificationService;
use Intranet\Entities\Falta_itaca;
use Illuminate\Http\Request;
use Intranet\Services\General\StateService;
use Intranet\Services\HR\FitxatgeService;
use Illuminate\Support\Carbon;
use function sumarHoras;

class FaltaItacaController extends ApiResourceController
{

    protected $model = 'Falta_itaca';
    private ?HorarioService $horarioService = null;
    private ?FitxatgeService $fitxatgeService = null;

    private function horarios(): HorarioService
    {
        if ($this->horarioService === null) {
            $this->horarioService = app(HorarioService::class);
        }

        return $this->horarioService;
    }

    private function fitxatge(): FitxatgeService
    {
        if ($this->fitxatgeService === null) {
            $this->fitxatgeService = app(FitxatgeService::class);
        }

        return $this->fitxatgeService;
    }

    public function potencial($dia, $idProfesor)
    {

        if (!config('variables.controlDiario') || $this->fitxatge()->hasFichado($dia, (string) $idProfesor)) {
            $horas = $this->horarios()->lectivasByProfesorAndDayOrdered((string) $idProfesor, nameDay($dia));
            $horasJ = [];

            foreach ($horas as $hora) {

                $horasT['idProfesor'] = $idProfesor;
                $horasT['desde'] = $hora->desde;
                $horasT['hasta'] = $hora->hasta;
                $horasT['idGrupo'] = $hora->idGrupo;
                $horasT['sesion_orden'] = $hora->sesion_orden;
                $horasT['dia'] = $dia;

                if ($falta = Falta_itaca::where('idProfesor', $idProfesor)
                        ->where('sesion_orden', $hora->sesion_orden)
                        ->where('dia', $dia)
                        ->first()) {
                    $horasT['checked'] = TRUE;
                    $horasT['justificacion'] = $falta->justificacion;
                    $horasT['enCentro'] = $falta->enCentro;
                    $horasT['estado'] = $falta->estado;
                } else {
                    $horasT['checked'] = FALSE;
                    $horasT['estado'] = 0;
                    $horasT['justificacion'] = '';
                    if ($this->fitxatge()->wasInsideAt((string) $idProfesor, (string) $dia, (string) sumarHoras($hora->desde, '00:30:00'))) {
                        $horasT['enCentro'] = TRUE;
                    }
                    else {
                        $horasT['enCentro'] = FALSE;
                    }
                }
                $horasJ[] = $horasT;

            }
            return $this->sendResponse($horasJ, 'OK');
        } else {
            return $this->sendResponse([], 'OK');
        }
    }

    public function guarda(Request $request)
    {
        $alta = false;
        $respuesta = [];
        foreach ($request->toArray() as $hora) {
            if (isset($hora['idProfesor'])) {
                if ($falta = Falta_itaca::where('idProfesor', $hora['idProfesor'])
                        ->where('sesion_orden', $hora['sesion_orden'])
                        ->where('dia', $hora['dia'])
                        ->first()) {
                    if (!$hora['checked']){
                        $falta->delete();
                        $respuesta[$hora['sesion_orden']]=0;
                    }
                    else {
                        $falta->justificacion = $hora['justificacion'];
                        $falta->save();
                    }
                } else

                if ($hora['checked']) {
                    $dia = new Carbon($hora['dia']);
                    $falta = new Falta_itaca();
                    $falta->idProfesor = $hora['idProfesor'];
                    $falta->dia = $dia->format('Y-m-d');
                    $falta->sesion_orden = $hora['sesion_orden'];
                    $falta->idGrupo = $hora['idGrupo'];
                    $falta->enCentro = $hora['enCentro'];
                    $falta->justificacion = $hora['justificacion'];
                    $falta->estado = 1;
                     
                    $falta->save();

                    $alta = true;
                    $respuesta[$hora['sesion_orden']]=1;
                }
            }
        }
        if ($alta){
            app(NotificationService::class)->send(config('avisos.director'),'Oblit Birret');
        }

        
        return $this->sendResponse($respuesta, 'OK');
    }

}
