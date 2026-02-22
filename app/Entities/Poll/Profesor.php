<?php

namespace Intranet\Entities\Poll;

use Intranet\Application\Grupo\GrupoService;
use Intranet\Entities\Departamento;
use Intranet\Services\School\ModuloGrupoService;

class Profesor extends ModelPoll
{
    public static function loadPoll($votes)
    {
        if (count($votes)) {
            return null;
        }
        $modulos = collect();
        $moduloGrupoService = app(ModuloGrupoService::class);
        foreach (authUser()->Grupo as $grupo) {
            foreach ($grupo->Modulos as $modulo) {
                $modulos->push([
                    'option1' => $modulo,
                    // Manté el format esperat pel wizard de polls (array de llistes de DNIs).
                    'option2' => [$moduloGrupoService->profesorIds($modulo)->values()->all()],
                ]);
            }
        }
        return $modulos;
    }

    public static function loadVotes($id)
    {
        $myVotes = array();
        foreach (app(ModuloGrupoService::class)->misModulos() as $modulo) {
            $myVotes[$modulo->ModuloCiclo->Modulo->literal][$modulo->Grupo->codigo]
                = Vote::myVotes($id, $modulo->id)->get();
        }
        return count($myVotes)?$myVotes:null;
    }

    public static function loadGroupVotes($id)
    {
        $myGroupsVotes = [];
        foreach (app(GrupoService::class)->misGrupos() as $grup) {
            $modulos = hazArray($grup->Modulos, 'id');
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
        foreach (app(GrupoService::class)->all() as $grupo) {
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
                            $votes['departament'][$departamento->id][$key]->push($optionVote);
                        }
                    }
                }
            }
        }
    }

}
