<?php

namespace Intranet\Entities\Poll;

class Actividad extends ModelPoll
{

    public static function loadPoll($allVotes)
    {
        $actividades = collect();
        foreach (authUser()->Grupo as $grupo) {
            foreach ($grupo->Actividades as $actividad) {
                $actividades->push(['option1' => $actividad]);
            }
        }
        return $actividades;
    }




}
