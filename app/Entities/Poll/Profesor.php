<?php

namespace Intranet\Entities\Poll;

use Intranet\Entities\Grupo;
use Intranet\Entities\Modulo_grupo;
use Intranet\Entities\Departamento;

class Profesor extends ModelPoll
{
    public static function loadPoll($votes)
    {
        if (count($votes)) {
            return null;
        }
        $modulos = collect();
        foreach (authUser()->Grupo as $grupo) {
            foreach ($grupo->Modulos as $modulo) {
                $modulos->push(['option1'=>$modulo,'option2'=>$modulo->Profesores()]);
            }
        }
        return $modulos;
    }

    public static function loadVotes($id)
    {
        $myVotes = array();
        foreach (Modulo_grupo::misModulos() as $modulo) {
            $myVotes[$modulo->ModuloCiclo->Modulo->literal][$modulo->Grupo->codigo]
                = Vote::myVotes($id, $modulo->id)->get();
        }
        return count($myVotes)?$myVotes:null;
    }

    public static function loadGroupVotes($id)
    {
        foreach (Grupo::misGrupos()->get() as $grup) {
            $modulos = hazArray(Grupo::find($grup->codigo)->Modulos, 'id');
            $myGroupsVotes[$grup->codigo] = Vote::myGroupVotes($id, $modulos)->get();
        }
        return $myGroupsVotes;
    }

    public static function aggregate(&$votes, $option1, $option2)
    {
        self::aggregateGrupo($option1, $votes);
        self::aggregateDepartamento($option2, $votes);
    }

    public static function has()
    {
        return count(authUser()->Grupo);
    }

    /**
     * @param $option1
     * @param $votes
     * @return array
     */
    private static function aggregateGrupo($option1, &$votes): void
    {
        foreach (Grupo::all() as $grupo) {
            foreach ($grupo->Modulos as $modulo) {
                if (isset($option1[$modulo->id])) {
                    foreach ($option1[$modulo->id] as $key => $optionVotes) {
                        foreach ($optionVotes as $optionVote) {
                            $votes['grup'][$grupo->codigo][$key]->push($optionVote);
                            $votes['cicle'][$modulo->ModuloCiclo->idCiclo][$key]->push($optionVote);
                        }
                    }
                }
            }
        }
    }

    /**
     * @param $option2
     * @param $votes
     * @return void
     */
    private static function aggregateDepartamento($option2, &$votes): void
    {
        foreach (Departamento::all() as $departamento) {
            foreach ($departamento->Profesor as $profesor) {
                if (isset($option2[$profesor->dni])) {
                    foreach ($option2[$profesor->dni] as $key => $optionVotes) {
                        foreach ($optionVotes as $optionVote) {
                            $votes['departamento'][$departamento->id][$key]->push($optionVote);
                        }
                    }
                }
            }
        }
    }

}
