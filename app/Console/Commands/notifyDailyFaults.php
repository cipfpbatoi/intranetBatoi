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


class notifyDailyFaults extends Command
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
        if (config('variables.controlDiario')){
            $guardias = hazArray(Guardia::where('dia', Hoy())
                ->where('realizada',-1)
                ->get(),'idProfesor','idProfesor');
            $profesores = $this->noHanFichado(Hoy());
            foreach ($profesores as $profesor)
                avisa($profesor,'No has fixat hui dia ' . Hoy('d-m-Y'), '#', 'Sistema');
            foreach ($guardias as $guardia)
                avisa($guardia,'No has fixat la guardia  hui dia ' . Hoy('d-m-Y'), '#', 'Sistema');
        }
    }

    private function noHanFichado($dia)
    {
        $profesores = Profesor::select('dni')->Activo()->get();

        // mira qui no ha fitxat
        $noHanFichado = [];
        foreach ($profesores as $profesor) {
            if (Falta_profesor::haFichado($dia, $profesor->dni)->count() == 0)
                if (Horario::Profesor($profesor->dni)->Dia(nameDay(new Date($dia)))->count() > 1)
                    $noHanFichado[$profesor->dni] = $profesor->dni;
        }


        // comprova que no estigues d'activitat
        $actividades = Actividad::Dia($dia)->where('fueraCentro','=',1)->get();
        foreach ($actividades as $actividad) {
            foreach ($actividad->profesores as $profesor) {
                if (in_array($profesor->dni, $noHanFichado))
                    unset($noHanFichado[$profesor->dni]);
            }
        }

        // comprova que no està de comissió
        $comisiones = Comision::Dia($dia)->get();
        foreach ($comisiones as $comision) {
            if (in_array($comision->idProfesor, $noHanFichado))
                unset($noHanFichado[$comision->idProfesor]);
        }

        // compova que no tinga falta
        $faltas = Falta::Dia($dia)->get();
        foreach ($faltas as $falta) {
            if (in_array($falta->idProfesor, $noHanFichado))
                unset($noHanFichado[$falta->idProfesor]);
        }

        return $noHanFichado;
    }


}
