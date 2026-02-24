<?php

namespace Intranet\Http\Controllers;

use Intranet\Application\Comision\ComisionService;
use Intranet\Application\Horario\HorarioService;
use Intranet\Application\Profesor\ProfesorService;
use Intranet\Http\Controllers\Core\BaseController;

use Illuminate\Support\Facades\Gate;
use Intranet\Entities\Hora;
use Intranet\Entities\Actividad;
use Intranet\Entities\Falta;
use Intranet\Entities\Profesor;
use Intranet\UI\Botones\BotonBasico;
use Intranet\Services\HR\FitxatgeService;


class PanelGuardiaController extends BaseController
{

    protected $perfil = 'profesor';
    protected $model = 'Horario';
    protected $gridFields = ['aula', 'Profesor', 'XGrupo', 'donde'];
    protected $titulo = ['quien' => 'Guardia'];

    private function comisions(): ComisionService
    {
        return app(ComisionService::class);
    }

    private function profesores(): ProfesorService
    {
        return app(ProfesorService::class);
    }

    private function horarios(): HorarioService
    {
        return app(HorarioService::class);
    }

    private function fitxatge(): FitxatgeService
    {
        return app(FitxatgeService::class);
    }

    protected function iniBotones()
    {
        Gate::authorize('manageAttendance', Profesor::class);
        $this->panel->setBoton('index', new BotonBasico('guardia.', ['text' => 'Tornar Guàrdia']));
    }

    public function search()
    {
        Gate::authorize('manageAttendance', Profesor::class);
        $sesion = sesion(hora());
        $dia_semana = nameDay(Hoy());


        // Que s'hauria d'estar fent ara a l'institut
        $ahora = $this->horarios()->lectivosByDayAndSesion($dia_semana, $sesion);

        // Quines activitats extraescolar estan programades ara al Centre
        $actividades = Actividad::Dia(Hoy())
                ->where('fueraCentro', '=', 1)
                ->get();

        // Ompli les dades de les extaescolas per grup i per profe
        foreach ($actividades as $actividad) {
            if ($this->coincideHorario($actividad, $sesion)) {
                foreach ($actividad->grupos as $grupo) {
                    $horario = $ahora->firstWhere('idGrupo', $grupo->codigo);
                    if ($horario) {
                        $horario->donde = "Extraescolar Grup";
                    }
                }
                foreach ($actividad->profesores as $profesor) {
                    $horario = $ahora->firstWhere('idProfesor', $profesor->dni);
                    if ($horario && !isset($horario->donde)) {
                        $horario->donde = "Extraescolar Profesor";
                    }
                }
            }
        }

        $comisionesHui = $this->comisions()->byDay(Hoy());

        // Mire si tot el món és al seu lloc
        foreach ($ahora as $horario)
            // si no està d'extraescolar
        {
            if (!isset($horario->donde)) {
                $profesor = $this->profesores()->find((string) $horario->idProfesor);
                if (!$profesor) {
                    $horario->donde = 'No ha fitxat';
                    continue;
                }
                if ($this->fitxatge()->isInside((string) $profesor->dni, false))
                    $horario->donde = 'Al centre';
                else {
                    $comision = $comisionesHui->firstWhere('idProfesor', $profesor->dni);
                    if ($comision && $this->coincideHorario($comision, $sesion))
                        $horario->donde = 'En comisión de servicio';
                    else {
                        $falta = Falta::Dia(Hoy())->where('idProfesor', $profesor->dni)->first();
                        if ($falta && $this->coincideHorario($falta, $sesion))
                            $horario->donde = 'Comunica Ausencia';
                        else
                            $horario->donde = 'No ha fitxat';
                    }
                }
            }
        }
        return $ahora;

    }

    private function coincideHorario($elemento, $sesion): bool
    {
        if (!esMismoDia($elemento->desde, $elemento->hasta)) {
            return true;
        }

        if (!empty($elemento->dia_completo)) {
            return true;
        }

        $horas = $this->getHorasAfectas($elemento);

        if (empty($horas)) {
            return false;
        }

        $horaInici = $horas[0];
        $horaFi = end($horas);

        return $sesion >= $horaInici && $sesion <= $horaFi;
    }

    private function getHorasAfectas($elemento): array
    {
        $inicio = $elemento->hora_ini ?? hora($elemento->desde);
        $fi     = $elemento->hora_fin ?? hora($elemento->hasta);

        return Hora::horasAfectadas($inicio, $fi)->toArray();
    }


}
