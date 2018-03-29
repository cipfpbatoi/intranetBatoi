<?php

namespace Intranet\Http\Controllers;

use Intranet\Entities\Profesor;
use Intranet\Entities\Alumno;
use Intranet\Entities\Hora;
use Intranet\Entities\Horario;
use Jenssegers\Date\Date;

trait traitNotificar
{
    protected function notify($id)
    {
        $elemento = $this->class::findOrFail($id);
        $this->avisaProfe($elemento, $elemento->idProfesor, $mensaje = "No estaré en el centre des de " . $elemento->desde . " fins " . $elemento->hasta);
        return back();
    }

    private function avisaProfe($elemento, $idProfesor, $mensaje, $emisor = null)
    {
        if (esMismoDia($elemento->desde, $elemento->hasta)) {
            $dia_semana = nameDay($elemento->desde);
            if (isset($elemento->dia_completo)) {
                if ($elemento->dia_completo) {
                    $hora_ini = '07:00';
                    $hora_fin = '23:00';
                } else {
                    $hora_ini = $elemento->hora_ini;
                    $hora_fin = $elemento->hora_fin;
                }
            } else {
                $hora_ini = hora($elemento->desde);
                $hora_fin = hora($elemento->hasta);
            }
            $horas = Hora::horasAfectadas($hora_ini, $hora_fin);
            if (count($horas)) {
                $grupos = Horario::distinct()
                        ->select('idGrupo')
                        ->Profesor($idProfesor)
                        ->Dia($dia_semana)
                        ->whereNotNull('idGrupo')
                        ->whereIn('sesion_orden', $horas)
                        ->get();
            }
        } else {
            $grupos = Horario::distinct()
                    ->select('idGrupo')
                    ->Profesor($idProfesor)
                    ->whereNotNull('idGrupo')
                    ->get();
        }
        if (count($grupos)) {
            $grupos_afectados = $grupos->toArray();
            $profesores = Horario::distinct()
                    ->select('idProfesor')
                    ->whereIn('idGrupo', $grupos_afectados)
                    ->get();
            foreach ($profesores as $profesor) {
                avisa($profesor->idProfesor, $mensaje, '#', $emisor);
            }
        }
    }

}
