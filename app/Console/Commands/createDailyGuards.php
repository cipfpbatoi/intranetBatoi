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
    public function handle()
    {
        if (config('variables.controlDiario')) {
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
        $yaEsta = Guardia::where('idProfesor', $dades['idProfesor'])
                ->where('dia', $dades['dia'])
                ->where('hora', $dades['hora'])
                ->count();
        if ($yaEsta == 0) {
            $guardia = new Guardia();
            foreach ($dades as $key => $value)
                $guardia->$key = $value;
            $guardia->save();
        }
    }

}
