<?php

namespace Intranet\Entities\Poll;

/**
 * Tipus d'enquesta vinculada a activitats dels grups de l'usuari.
 */
class Actividad extends ModelPoll
{

    public static function loadPoll($allVotes, ?Poll $poll = null)
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
