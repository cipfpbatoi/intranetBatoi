<?php

namespace Intranet\Console\Commands;

use Illuminate\Console\Command;
use Intranet\Entities\Actividad;
use Intranet\Entities\CalendariEscolar;
use Intranet\Application\Comision\ComisionService;
use Intranet\Application\Horario\HorarioService;
use Intranet\Application\Profesor\ProfesorService;
use Intranet\Entities\Falta;
use Intranet\Entities\Falta_profesor;
use Intranet\Entities\Guardia;
use Illuminate\Support\Carbon;
use Throwable;
use Illuminate\Support\Facades\Log;

/**
 * Envia avisos diaris de fitxatge i guàrdies pendents.
 */
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
     * Executa la notificació diària de fitxatges pendents.
     *
     * @return int
     */
    public function handle(): int
    {
        try {
            if (!config('variables.controlDiario')) {
                return self::SUCCESS;
            }

            $dia = hoy();

            if (CalendariEscolar::esNoLectiuOFestiu($dia)) {
                return self::SUCCESS;
            }

            $dataFormatada = Carbon::parse($dia)->format('d-m-Y');

            $guardias = hazArray(
                Guardia::where('dia', $dia)->where('realizada', -1)->get(),
                'idProfesor',
                'idProfesor'
            );
            $profesores = $this->noHanFichado($dia);
            foreach ($profesores as $profesor) {
                avisa($profesor, 'No has fitxat hui dia '.$dataFormatada, '#', 'Sistema');
            }
            foreach ($guardias as $guardia) {
                avisa($guardia, 'No has fixtat la guàrdia  hui dia '.$dataFormatada, '#', 'Sistema');
            }

            return self::SUCCESS;
        } catch (Throwable $e) {
            report($e);
            Log::error('Error notificació d\'ausències diàries.', [
                'exception' => $e->getMessage(),
            ]);

            return self::FAILURE;
        }
    }

    /**
     * Retorna els DNI del professorat que no ha fitxat i no té justificació.
     *
     * @param string $dia
     * @return array<string, string>
     */
    private function noHanFichado($dia): array
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
     * Detecta el professorat que té horari lectiu però cap fitxatge en el dia.
     *
     * @param string $dia
     * @param array<string, string> $noHanFichado
     * @return void
     */
    private function profeSinFichar($dia, array &$noHanFichado): void
    {
        $profesores = $this->profesorService->activosOrdered();
        foreach ($profesores as $profesor) {
            if (Falta_profesor::haFichado($dia, $profesor->dni)->count() == 0 &&
                $this->horarioService->countByProfesorAndDay((string) $profesor->dni, nameDay(new Carbon($dia))) > 1) {
                $noHanFichado[$profesor->dni] = $profesor->dni;
            }
        }
    }

    /**
     * Exclou del llistat el professorat que està d'activitat fora del centre.
     *
     * @param string $dia
     * @param array<string, string> $noHanFichado
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
     * Exclou del llistat el professorat amb comissió de serveis.
     *
     * @param string $dia
     * @param array<string, string> $noHanFichado
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
     * Exclou del llistat el professorat amb falta registrada.
     *
     * @param string $dia
     * @param array<string, string> $noHanFichado
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
