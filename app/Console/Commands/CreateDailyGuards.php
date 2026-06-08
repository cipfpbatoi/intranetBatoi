<?php

namespace Intranet\Console\Commands;

use Illuminate\Console\Command;
use Intranet\Application\Comision\ComisionService;
use Intranet\Application\Horario\HorarioService;
use Intranet\Application\Profesor\ProfesorService;
use Intranet\Entities\CalendariEscolar;
use Intranet\Entities\Hora;
use Intranet\Entities\Guardia;
use Intranet\Entities\Actividad;
use Intranet\Entities\Falta;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Crea les guàrdies diàries del professorat de forma idempotent.
 */
class CreateDailyGuards extends Command
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
    protected $signature = 'guards:Daily';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crea Guardias Diarias';


    /**
     * Retorna el DNI del substitut actiu del professor, si en té.
     *
     * @param mixed $dni
     * @return mixed
     */
    private function substitutoActual($dni)
    {
        do {
            $substituto = $this->profesorService->findBySustituyeA((string) $dni);
            if ($substituto) {
                $dni = $substituto->dni;
            }
        } while ($substituto && !$substituto->fecha_baja);
        return $dni;
    }

    /**
     * Executa la creació diària de guàrdies si el dia és lectiu.
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
                Log::info('No es creen guardies diàries perquè el dia no és lectiu.', [
                    'dia' => $dia,
                ]);

                return self::SUCCESS;
            }

            $this->createGuardias();
            $comisiones = $this->comisionService->byDay($dia);
            foreach ($comisiones as $elemento) {
                $this->creaGuardia($elemento, 'El professor està en comissió de servei autoritzada');
            }
            $actividades = Actividad::Dia($dia)
                ->where('fueraCentro', '=', 1)
                ->get();
            foreach ($actividades as $actividad) {
                foreach ($actividad->profesores as $profesor) {
                    $this->creaGuardia($actividad, 'El professor està en Activitat extraescolar', $profesor->dni);
                }
            }
            $faltas = Falta::Dia($dia)->get();
            foreach ($faltas as $falta) {
                $this->creaGuardia($falta, 'El professor ha notificado ausencia');
            }

            return self::SUCCESS;
        } catch (Throwable $e) {
            report($e);
            Log::error('Error creant guardies diàries.', [
                'exception' => $e->getMessage(),
            ]);

            return self::FAILURE;
        }
    }

    /**
     * Crea les guàrdies associades a una incidència del professorat.
     *
     * @param mixed $elemento
     * @param string $mensaje
     * @param string|null $idProfesor
     * @return void
     */
    private function creaGuardia($elemento, $mensaje, $idProfesor = null)
    {
        $idProfesor = $idProfesor ? $idProfesor : $elemento->idProfesor;
        $diaSemana = nameDay(hoy());

        if (esMismoDia($elemento->desde, $elemento->hasta)) {
            if (isset($elemento->hora_ini)) {
                $horas = Hora::horasAfectadas($elemento->hora_ini, $elemento->hora_fin)->toArray();
            } else {
                $horas = Hora::horasAfectadas(hora($elemento->desde), hora($elemento->hasta))->toArray();
            }

            if (count($horas)) {
                $horario = $this->horarioService->guardiaAllByProfesorAndDiaAndSesiones(
                    (string) $idProfesor,
                    (string) $diaSemana,
                    $horas
                );
            }
        } else {
            $horario = $this->horarioService->guardiaAllByProfesorAndDia((string) $idProfesor, (string) $diaSemana);
        }

        foreach ($horario as $horasAfectadas) {
            $guardia['idProfesor'] = $idProfesor;
            $guardia['dia'] = hoy();
            $guardia['hora'] = $horasAfectadas->sesion_orden;
            $guardia['realizada'] = 0;
            $guardia['observaciones'] = $mensaje;
            $guardia['obs_personal'] = '';
            $this->saveGuardia($guardia);
        }
    }


    /**
     * Guarda una guàrdia de forma idempotent.
     *
     * Les guàrdies base només s'insereixen si no existeixen. Les guàrdies amb
     * incidència actualitzen el registre existent perquè prevalga el motiu.
     *
     * @param array<string, mixed> $dades
     * @return void
     */
    private function saveGuardia(array $dades): void
    {
        $now = Carbon::now();
        $row = [
            'idProfesor' => $dades['idProfesor'],
            'dia' => $dades['dia'],
            'hora' => $dades['hora'],
            'realizada' => $dades['realizada'],
            'observaciones' => $dades['observaciones'] ?? '',
            'obs_personal' => $dades['obs_personal'] ?? '',
            'created_at' => $now,
            'updated_at' => $now,
        ];

        if ($row['observaciones'] === '') {
            Guardia::insertOrIgnore([$row]);
            return;
        }

        Guardia::upsert(
            [$row],
            ['idProfesor', 'dia', 'hora'],
            ['realizada', 'observaciones', 'updated_at']
        );
    }

    /**
     * Crea les guàrdies base del dia segons l'horari.
     *
     * @return void
     */
    private function createGuardias(): void
    {
        $diaSemana = nameDay(hoy());
        foreach ($this->horarioService->guardiaAllByDia((string) $diaSemana) as $horario) {
            $profesor = $this->profesorService->find((string) $horario->idProfesor);
            if (!$profesor) {
                continue;
            }
            if ($profesor->fecha_baja) {
                $guardia['idProfesor'] = $this->substitutoActual($horario->idProfesor);
            } else {
                $guardia['idProfesor'] = $horario->idProfesor;
            }
            $guardia['dia'] = hoy();
            $guardia['hora'] = $horario->sesion_orden;
            $guardia['realizada'] = -1;
            $guardia['observaciones'] = '';
            $this->saveGuardia($guardia);
        }
    }

}
