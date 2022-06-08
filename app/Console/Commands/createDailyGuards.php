<?php

namespace Intranet\Console\Commands;

use Illuminate\Console\Command;
use Intranet\Entities\Comision;
use Intranet\Entities\Horario;
use Intranet\Entities\Hora;
use Intranet\Entities\Guardia;
use Intranet\Entities\Actividad;
use Intranet\Entities\Actividad_profesor;
use Intranet\Entities\Falta;
use Intranet\Entities\Profesor;

class createDailyGuards extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'guards:Daily';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crea Guardias Diarias';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */


    private function substitutoActual($dni){
        do {
            $substituto = Profesor::where('sustituye_a',$dni)->first();
            if ($substituto){
                $dni = $substituto->dni;
            }
        } while ($substituto && !$substituto->fecha_baja);
        return $dni;
    }

    public function handle()
    {
        if (config('variables.controlDiario')) {
            $dia_semana = nameDay(Hoy());
            foreach (Horario::GuardiaAll()
                         ->Dia($dia_semana)
                         ->get() as $horario){
                $profesor = Profesor::find($horario->idProfesor);
                if ($profesor->fecha_baja){
                    $guardia['idProfesor'] = $this->substitutoActual($horario->idProfesor);
                } else {
                    $guardia['idProfesor'] = $horario->idProfesor;
                }
                $guardia['dia'] = Hoy();
                $guardia['hora'] = $horario->sesion_orden;
                $guardia['realizada'] = -1;
                $guardia['observaciones'] = '';
                $this->saveGuardia($guardia);
            }
            $comisiones = Comision::Dia(Hoy())->get();
            foreach ($comisiones as $elemento) {
                $this->creaGuardia($elemento, 'El professor està en comissió de servei autoritzada');
            }
            $actividades = Actividad::Dia(Hoy())
                    ->where('fueraCentro', '=', 1)
                    ->get();
            foreach ($actividades as $actividad) {
                foreach ($actividad->profesores as $profesor) {
                    $this->creaGuardia($actividad, 'El professor està en Activitat extraescolar', $profesor->dni);
                }
            }
            $faltas = Falta::Dia(Hoy())->get();
            foreach ($faltas as $falta) {
                $this->creaGuardia($falta, 'El professor ha notificado ausencia');
            }
        }
    }

    private function creaGuardia($elemento, $mensaje, $idProfesor = null)
    {
        $idProfesor = $idProfesor ? $idProfesor : $elemento->idProfesor;
        $dia_semana = nameDay(Hoy());
        if (esMismoDia($elemento->desde, $elemento->hasta)) {
            if (isset($elemento->hora_ini))
                $horas = Hora::horasAfectadas($elemento->hora_ini, $elemento->hora_fin);
            else
                $horas = Hora::horasAfectadas(hora($elemento->desde), hora($elemento->hasta));
            if (count($horas)) {
                $horario = Horario::distinct()
                        ->Profesor($idProfesor)
                        ->Dia($dia_semana)
                        ->whereIn('sesion_orden', $horas)
                        ->where('ocupacion', config('constants.codigoGuardia'))
                        ->get();
            }
        } else {
            $horario = Horario::distinct()
                    ->Profesor($idProfesor)
                    ->Dia($dia_semana)
                    ->where('ocupacion', config('constants.codigoGuardia'))
                    ->get();
        }
        foreach ($horario as $horasAfectadas) {
            $guardia['idProfesor'] = $idProfesor;
            $guardia['dia'] = Hoy();
            $guardia['hora'] = $horasAfectadas->sesion_orden;
            $guardia['realizada'] = 0;
            $guardia['observaciones'] = $mensaje;
            $guardia['obs_personal'] = '';
            $this->saveGuardia($guardia);
        }
    }

    private function saveGuardia($dades)
    {
        $guardia = Guardia::where('idProfesor', $dades['idProfesor'])
                ->where('dia', $dades['dia'])
                ->where('hora', $dades['hora'])
                ->first();
        if (!$guardia) {
            $guardia = new Guardia();
            foreach ($dades as $key => $value)
                $guardia->$key = $value;
            $guardia->save();
        } else {
            if ($dades['observaciones'] != ''){
                $guardia->realizada = 0;
                $guardia->observaciones = $dades['observaciones'];
            }
        }
    }

}
