<?php

namespace Intranet\Entities\Poll;

use Faker\Calculator\TCNo;
use Intranet\Entities\Fct;

class AlumnoFct extends ModelPoll
{
    public static function loadPoll($votes)
    {
        $alumno = AuthUser();
        $fcts = collect();
        $alumnos = $alumno->fcts()
            ->where('alumno_fcts.desde', '<', Hoy())
            ->esFct()
            ->whereNotIn('fcts.id', $votes)
            ->get();
        foreach ($alumnos as $fct) {
            $fcts->push(['option1'=>$fct]);
        }
        if (count($fcts)) {
            return $fcts;
        }
        return null;
    }

    public static function loadVotes($id)
    {
        $fcts = hazArray(Fct::misFcts()->get(), 'id');
        $votes = Vote::getVotes($id, $fcts)->get();
        $classified = [];
        foreach ($votes as $vote) {
            $classified[$vote->idOption1][$vote->option_id] = isset($vote->text)?$vote->text:$vote->value;
        }
        if (count($classified)) {
            return $classified;
        }
        return null;
    }


    public static function aggregate(&$votes, $option1, $option2)
    {
        foreach ($option1 as $idFct => $vote) {
                $ciclo = Fct::find($idFct)->Colaboracion->Ciclo ?? null;
                if ($ciclo) {
                    foreach ($vote as $key => $optionVotes) {
                        foreach ($optionVotes as $optionVote) {
                            $votes['cicle'][$ciclo->id][$key]->push($optionVote);
                            $votes['departament'][$ciclo->departamento][$key]->push($optionVote);
                        }
                    }
                }
        }
    }

    public static function loadGroupVotes($id)
    {
        return [];
    }
    public static function vista()
    {
        return 'Fct';
    }
    public static function has()
    {
        return count(AuthUser()->fcts);
    }
}
