<?php

namespace Intranet\Console\Commands;

use Illuminate\Console\Command;
use Intranet\Entities\Actividad;
use Intranet\Entities\Comision;
use Intranet\Entities\Falta;
use Intranet\Entities\Falta_profesor;
use Intranet\Entities\Guardia;
use Intranet\Entities\Horario;
use Intranet\Entities\Profesor;
use Intranet\Http\Controllers\FicharController;
use Jenssegers\Date\Date;


class NotifyDailyFaults extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fault:Daily';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Notifica ausencias diarias';


    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if (config('variables.controlDiario')) {
            $guardias = hazArray(
                Guardia::where('dia', Hoy()->where('realizada', -1)->get()),
                'idProfesor',
                'idProfesor'
            );
            $profesores = $this->noHanFichado(Hoy());
            foreach ($profesores as $profesor) {
                avisa($profesor, 'No has fixat hui dia '.Hoy('d-m-Y'), '#', 'Sistema');
            }
            foreach ($guardias as $guardia) {
                avisa($guardia, 'No has fixat la guardia  hui dia '.Hoy('d-m-Y'), '#', 'Sistema');
            }
        }
    }

    private function noHanFichado($dia)
    {


        // mira qui no ha fitxat
        $noHanFichado = [];
        $this->profeSinFichar($dia, $noHanFichado);


        // comprova que no estigues d'activitat
        $this->profesoresEnActividad($dia, $noHanFichado);

        // comprova que no està de comissió
        $this->profesoresDeComision($dia, $noHanFichado);

        // compova que no tinga falta
        $this->profesoresDeBaja($dia, $noHanFichado);

        return $noHanFichado;
    }

    /**
     * @param $profesores
     * @param $dia
     * @param  array  $noHanFichado
     * @param $profesor
     * @return void
     */
    private function profeSinFichar($dia, array &$noHanFichado): void
    {
        $profesores = Profesor::select('dni')->Activo()->get();
        foreach ($profesores as $profesor) {
            if (Falta_profesor::haFichado($dia, $profesor->dni)->count() == 0 &&
                Horario::Profesor($profesor->dni)->Dia(nameDay(new Date($dia)))->count() > 1) {
                $noHanFichado[$profesor->dni] = $profesor->dni;
            }
        }
    }

    /**
     * @param $dia
     * @param  array  $noHanFichado
     * @return void
     */
    private function profesoresEnActividad($dia, array &$noHanFichado): void
    {
        $actividades = Actividad::Dia($dia)->where('fueraCentro', '=', 1)->get();
        foreach ($actividades as $actividad) {
            foreach ($actividad->profesores as $profesor) {
                if (in_array($profesor->dni, $noHanFichado)) {
                    unset($noHanFichado[$profesor->dni]);
                }
            }
        }
    }

    /**
     * @param $dia
     * @param  array  $noHanFichado
     * @return void
     */
    private function profesoresDeComision($dia, array &$noHanFichado): void
    {
        $comisiones = Comision::Dia($dia)->get();
        foreach ($comisiones as $comision) {
            if (in_array($comision->idProfesor, $noHanFichado)) {
                unset($noHanFichado[$comision->idProfesor]);
            }
        }
    }

    /**
     * @param $dia
     * @param  array  $noHanFichado
     * @return void
     */
    private function profesoresDeBaja($dia, array &$noHanFichado): void
    {
        $faltas = Falta::Dia($dia)->get();
        foreach ($faltas as $falta) {
            if (in_array($falta->idProfesor, $noHanFichado)) {
                unset($noHanFichado[$falta->idProfesor]);
            }
        }
    }


}
