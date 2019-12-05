<?php

namespace Intranet\Entities\Poll;

class Actividad extends ModelPoll
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


}
