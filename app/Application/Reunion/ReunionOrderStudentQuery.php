<?php

declare(strict_types=1);

namespace Intranet\Application\Reunion;

use Illuminate\Support\Collection;
use Intranet\Entities\Alumno;
use Intranet\Entities\AlumnoFct;

/**
 * Consultes d'alumnat necessàries per a generar punts de reunió.
 */
class ReunionOrderStudentQuery
{
    /**
     * Retorna l'alumnat amb dificultats del tutor indicat.
     *
     * @return Collection<int, string>
     */
    public function learningDifficulties(string $teacher): Collection
    {
        return Alumno::query()
            ->misDA($teacher)
            ->get()
            ->pluck('fullName')
            ->map(static fn (mixed $name): string => (string) $name)
            ->values();
    }

    /**
     * Retorna l'alumnat LOE del tutor indicat.
     *
     * @return Collection<int, string>
     */
    public function loeStudents(string $teacher): Collection
    {
        return Alumno::query()
            ->misLOE($teacher)
            ->get()
            ->pluck('FullName')
            ->map(static fn (mixed $name): string => (string) $name)
            ->values();
    }

    /**
     * Retorna l'alumnat amb projectes pendents de defensa.
     *
     * @return Collection<int, string>
     */
    public function projectStudents(string $teacher): Collection
    {
        return AlumnoFct::query()
            ->misProyectos($teacher)
            ->get()
            ->pluck('FullName')
            ->map(static fn (mixed $name): string => (string) $name)
            ->values();
    }
}
