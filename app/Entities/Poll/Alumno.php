<?php

declare(strict_types=1);

namespace Intranet\Entities\Poll;

use Illuminate\Support\Collection;
use Intranet\Entities\Alumno as AlumnoEntity;
use Intranet\Entities\Grupo;

/**
 * Tipus d'enquesta genèrica per a alumnat.
 *
 * Permet que qualsevol alumne amb grup assignat puga respondre una enquesta
 * sense dependre de tindre FCT. Les respostes es vinculen a una clau numèrica
 * derivada del NIA per mantindre compatibilitat amb `votes.idOption1`.
 */
class Alumno extends ModelPoll
{
    /**
     * Carrega el qüestionari pendent per a l'alumne autenticat.
     *
     * @param array<mixed> $votes
     * @return Collection<int, array{option1: object}>|null
     */
    public static function loadPoll($votes)
    {
        if (count($votes)) {
            return null;
        }

        $alumno = authUser();
        if (!$alumno instanceof AlumnoEntity) {
            return null;
        }

        $grupo = $alumno->Grupo->first();
        if (!$grupo instanceof Grupo) {
            return null;
        }

        return collect([
            ['option1' => self::buildContext($alumno, $grupo)],
        ]);
    }

    /**
     * Retorna els vots propis de l'alumne autenticat.
     *
     * @param int|string $id
     * @return array<int, array<int, int|string>>|null
     */
    public static function loadVotes($id)
    {
        $alumno = authUser();
        if (!$alumno instanceof AlumnoEntity) {
            return null;
        }

        $votes = Vote::getVotes($id, [self::studentVoteKey((string) $alumno->nia)])->get();
        $classified = [];

        foreach ($votes as $vote) {
            $classified[$vote->idOption1][$vote->option_id] = isset($vote->text) ? $vote->text : $vote->value;
        }

        return count($classified) ? $classified : null;
    }

    /**
     * L'enquesta genèrica d'alumnat no necessita comparatives específiques
     * per al panell individual de l'usuari.
     *
     * @param int|string $id
     * @return array<int, mixed>
     */
    public static function loadGroupVotes($id)
    {
        return [];
    }

    /**
     * Agrega vots per grup, cicle i departament a partir del NIA hashat.
     *
     * @param array<string, mixed> $votes
     * @param iterable<mixed> $option1
     * @param iterable<mixed> $option2
     * @return void
     */
    public static function aggregate(&$votes, $option1, $option2): void
    {
        foreach (AlumnoEntity::with(['Grupo.Ciclo'])->get() as $alumno) {
            $grupo = $alumno->Grupo->first();
            if (!$grupo instanceof Grupo) {
                continue;
            }

            $studentKey = self::studentVoteKey((string) $alumno->nia);
            if (!isset($option1[$studentKey])) {
                continue;
            }

            foreach ($option1[$studentKey] as $optionId => $optionVotes) {
                foreach ($optionVotes as $optionVote) {
                    $votes['grup'][$grupo->codigo][$optionId]->push($optionVote);

                    if ($grupo->idCiclo !== null) {
                        $votes['cicle'][$grupo->idCiclo][$optionId]->push($optionVote);
                    }

                    $departamento = $grupo->Ciclo?->departamento;
                    if ($departamento !== null) {
                        $votes['departament'][$departamento][$optionId]->push($optionVote);
                    }
                }
            }
        }
    }

    /**
     * Determina si l'alumne autenticat té context suficient per a respondre.
     */
    public static function has()
    {
        $alumno = authUser();

        return $alumno instanceof AlumnoEntity && $alumno->Grupo->isNotEmpty();
    }

    /**
     * Construeix el context visual mínim necessari per al formulari.
     */
    private static function buildContext(AlumnoEntity $alumno, Grupo $grupo): object
    {
        $label = 'Grup ' . $grupo->codigo;
        if ($grupo->Ciclo?->ciclo) {
            $label .= ' · ' . $grupo->Ciclo->ciclo;
        }

        return (object) [
            'id' => self::studentVoteKey((string) $alumno->nia),
            'grupo' => $grupo->codigo,
            'cicle' => $grupo->Ciclo?->ciclo,
            'label' => $label,
        ];
    }

    /**
     * Genera una clau numèrica estable a partir del NIA.
     *
     * Es fa servir un hash curt perquè `votes.idOption1` és enter sense signe.
     */
    private static function studentVoteKey(string $nia): int
    {
        return (int) sprintf('%u', crc32($nia));
    }
}
