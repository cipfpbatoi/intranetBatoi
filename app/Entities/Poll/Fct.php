<?php

namespace Intranet\Entities\Poll;

use Intranet\Entities\Fct as realFct;

/**
 * Tipus d'enquesta FCT contestada pel professorat tutor.
 */
class Fct extends ModelPoll
{
    public static function loadPoll($votes, ?Poll $poll = null)
    {
        $fcts = collect();
        foreach (realFct::misFctsColaboracion()->esFct()->whereNotIn('id', $votes)->get() as $fct) {
            $fcts->push(['option1'=>$fct]);
        }
        if (count($fcts)) {
            return $fcts;
        }
        return null;
    }
    public static function interviewed()
    {
        return 'Intranet\\Entities\\Profesor';
    }
    public static function keyInterviewed()
    {
        return 'dni';
    }
    public static function loadVotes($id)
    {
        $fcts = hazArray(realFct::misFctsColaboracion()->esFct()->get(), 'id');
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

    public static function aggregate(&$votes, $option1, $option2, ?Poll $poll = null)
    {
        foreach ($option1 as $idFct => $vote) {
            $fct = realFct::with(['Colaboracion.Ciclo', 'Alumnos.Grupo'])->find($idFct);
            $ciclo = $fct?->Colaboracion?->Ciclo;
            if (!$fct || !$ciclo) {
                continue;
            }

            $groupCodes = self::groupCodesForFct($fct, (int) $ciclo->id);

            foreach ($vote as $key => $optionVotes) {
                foreach ($optionVotes as $optionVote) {
                    foreach ($groupCodes as $groupCode) {
                        $votes['grup'][$groupCode][$key] ??= collect();
                        $votes['grup'][$groupCode][$key]->push($optionVote);
                    }

                    $votes['cicle'][$ciclo->id][$key]->push($optionVote);
                    $votes['departament'][$ciclo->departamento][$key]->push($optionVote);
                }
            }
        }
    }

    /**
     * Retorna els codis de grup de l'alumnat associat a la FCT dins del cicle.
     *
     * @return array<int, string>
     */
    private static function groupCodesForFct(realFct $fct, int $cicloId): array
    {
        $groups = [];

        foreach ($fct->Alumnos as $alumno) {
            foreach ($alumno->Grupo ?? [] as $grupo) {
                if ((int) ($grupo->idCiclo ?? 0) !== $cicloId) {
                    continue;
                }

                $groups[] = (string) $grupo->codigo;
            }
        }

        return array_values(array_unique($groups));
    }

    public static function loadGroupVotes($id)
    {
        return [];
    }

    public static function has(?Poll $poll = null)
    {
        return realFct::misFctsColaboracion()->esFct()->count();
    }


}
