<?php

namespace Intranet\Console\Commands;

use Illuminate\Console\Command;
use Intranet\Entities\Actividad;
use Intranet\Application\Comision\ComisionService;
use Intranet\Application\Horario\HorarioService;
use Intranet\Application\Profesor\ProfesorService;
use Intranet\Entities\Falta;
use Intranet\Entities\Falta_profesor;
use Intranet\Entities\Guardia;
use Jenssegers\Date\Date;


class NotifyDailyFaults extends Command
{
    private ComisionService $comisionService;
    private ProfesorService $profesorService;
    private HorarioService $horarioService;

    public function __construct()
    {
        parent::__construct();
        $this->comisionService = app(ComisionService::class);
        $this->profesorService = app(ProfesorService::class);
        $this->horarioService = app(HorarioService::class);
    }

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
                Guardia::where('dia', hoy())->where('realizada', -1)->get(),
                'idProfesor',
                'idProfesor'
            );
            $profesores = $this->noHanFichado(hoy());
            foreach ($profesores as $profesor) {
                avisa($profesor, 'No has fitxat hui dia '.hoy('d-m-Y'), '#', 'Sistema');
            }
            foreach ($guardias as $guardia) {
                avisa($guardia, 'No has fixtat la guàrdia  hui dia '.hoy('d-m-Y'), '#', 'Sistema');
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
        $profesores = $this->profesorService->activosOrdered();
        foreach ($profesores as $profesor) {
            if (Falta_profesor::haFichado($dia, $profesor->dni)->count() == 0 &&
                $this->horarioService->countByProfesorAndDay((string) $profesor->dni, nameDay(new Date($dia))) > 1) {
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
        $comisiones = $this->comisionService->byDay($dia);
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
