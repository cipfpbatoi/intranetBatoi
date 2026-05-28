<?php

namespace Intranet\Entities\Poll;

use Intranet\Entities\Fct;

/**
 * Tipus d'enquesta de valoració FCT contestada per l'alumnat.
 */
class AlumnoFct extends ModelPoll
{
    /**
     * Carrega les FCT actives i no contestades de l'alumne autenticat.
     *
     * @param array<mixed> $votes
     * @return \Illuminate\Support\Collection<int, array{option1: \Intranet\Entities\Fct}>|null
     */
    public static function loadPoll($votes)
    {
        $alumno = authUser();
        $fcts = collect();
        $alumnos = $alumno->fcts()
            ->where('alumno_fcts.desde', '<', hoy())
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

    /**
     * Carrega els vots propis de l'alumne autenticat agrupats per FCT.
     *
     * @param int|string $id
     * @return array<int, array<int, int|string>>|null
     */
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


    /**
     * Agrega els vots per grup, cicle i departament.
     *
     * En este tipus d'enquesta `idOption1` identifica la FCT i cada vot
     * manté en `user_id` el NIA de l'alumne o el seu hash md5 si la poll és
     * anònima. Això permet reconstruir el grup real del respondedor i evitar
     * que la fulla "Grups" quede buida.
     *
     * @param array<string, mixed> $votes
     * @param iterable<mixed> $option1
     * @param iterable<mixed> $option2
     * @return void
     */
    public static function aggregate(&$votes, $option1, $option2)
    {
        foreach ($option1 as $idFct => $vote) {
            $fct = Fct::with(['Colaboracion.Ciclo', 'Alumnos.Grupo'])->find($idFct);
            $ciclo = $fct?->Colaboracion?->Ciclo;
            if (!$fct || !$ciclo) {
                continue;
            }

            $groupsByRespondent = self::groupsByRespondent($fct, (int) $ciclo->id);

            foreach ($vote as $key => $optionVotes) {
                foreach ($optionVotes as $optionVote) {
                    $groupCode = $groupsByRespondent[(string) ($optionVote->user_id ?? '')] ?? null;
                    if ($groupCode !== null) {
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
     * Este tipus no mostra comparatives específiques en el panell individual.
     *
     * @param int|string $id
     * @return array<int, mixed>
     */
    public static function loadGroupVotes($id)
    {
        return [];
    }

    /**
     * Construeix un mapa `nia|md5(nia) => codi de grup` per als alumnes de la FCT.
     *
     * @return array<string, string>
     */
    private static function groupsByRespondent(Fct $fct, int $cicloId): array
    {
        $groups = [];

        foreach ($fct->Alumnos as $alumno) {
            $groupCode = self::resolveStudentGroupCode($alumno, $cicloId);
            if ($groupCode === null) {
                continue;
            }

            $nia = (string) $alumno->nia;
            $groups[$nia] = $groupCode;
            $groups[md5($nia)] = $groupCode;
        }

        return $groups;
    }

    /**
     * Resol el grup de l'alumne dins del mateix cicle que la FCT.
     */
    private static function resolveStudentGroupCode(object $alumno, int $cicloId): ?string
    {
        foreach ($alumno->Grupo ?? [] as $grupo) {
            if ((int) ($grupo->idCiclo ?? 0) === $cicloId) {
                return (string) $grupo->codigo;
            }
        }

        return null;
    }

    public static function vista()
    {
        return 'Fct';
    }

    /**
     * Indica si l'usuari autenticat té FCT disponibles per contestar.
     */
    public static function has()
    {
        return count(authUser()->fcts);
    }
}
