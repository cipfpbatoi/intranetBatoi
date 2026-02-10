<?php

namespace Intranet\Http\Controllers;

use Intranet\Entities\Profesor;
use Intranet\Entities\Horario;
use Intranet\Entities\Hora;
use Intranet\Entities\Actividad;
use Intranet\Entities\Comision;
use Intranet\Entities\Falta;
use Intranet\UI\Botones\BotonBasico;


class PanelGuardiaController extends BaseController
{

    protected $perfil = 'profesor';
    protected $model = 'Horario';
    protected $gridFields = ['aula', 'Profesor', 'XGrupo', 'donde'];
    protected $titulo = ['quien' => 'Guardia'];

    protected function iniBotones()
    {
        $this->panel->setBoton('index', new BotonBasico('guardia.', ['text' => 'Tornar Guàrdia']));
    }

    public function search()
    {
        $sesion = sesion(hora());
        $dia_semana = nameDay(Hoy());


        // Que s'hauria d'estar fent ara a l'institut
        $ahora = Horario::distinct()
                ->Dia($dia_semana)
                ->where('sesion_orden', $sesion)
                ->Lectivos()
                ->whereNotNull('idGrupo')
                ->whereNull('ocupacion')
                ->get();

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

        // Mire si tot el món és al seu lloc
        foreach ($ahora as $horario)
            // si no està d'extraescolar
        {
            if (!isset($horario->donde)) {
                $profesor = Profesor::find($horario->idProfesor);
                if (estaDentro($profesor->dni))
                    $horario->donde = 'Al centre';
                else {
                    $comision = Comision::Dia(Hoy())->where('idProfesor', $profesor->dni)->first();
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
