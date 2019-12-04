<?php

namespace Intranet\Entities\Poll;

use Intranet\Entities\Actividad as ActividadPoll;

class Actividad extends ActividadPoll implements ModelPoll
{

    public static function loadPoll(){
        $actividades = collect();
        foreach (AuthUser()->Grupo as $grupo) {
            foreach ($grupo->Actividades as $actividad) {
                $actividades->push(['option1' => $actividad]);
            }
        }
        return $actividades;
    }
    public static function interviewed(){
        return 'Intranet\\Entities\\Alumno';
    }
    public static function keyInterviewed(){
        return 'nia';
    }

}
