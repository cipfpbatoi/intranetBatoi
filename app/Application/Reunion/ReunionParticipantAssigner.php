<?php

declare(strict_types=1);

namespace Intranet\Application\Reunion;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Intranet\Application\Profesor\ProfesorService;
use Intranet\Entities\AlumnoFct;
use Intranet\Entities\Profesor;
use Intranet\Entities\Reunion;

/**
 * Assigna explícitament els participants d'una reunió segons el col·lectiu.
 */
class ReunionParticipantAssigner
{
    public function __construct(private readonly ProfesorService $profesorService)
    {
    }

    /**
     * Assigna professorat i, quan correspon, alumnat sense dependre del guard.
     */
    public function assign(Reunion $reunion, Profesor $creator): void
    {
        $meetingType = $reunion->Tipos();

        if (!$meetingType) {
            Log::warning("Reunió {$reunion->id} sense tipus configurat; no s'assignen participants.");

            return;
        }

        $teachers = match ($meetingType->colectivo) {
            'Departamento' => $this->profesorService->plantillaByDepartamento((string) $creator->departamento),
            'Profesor' => $this->profesorService->plantilla(),
            'GrupoTrabajo' => $this->profesorService->byGrupoTrabajo((string) $reunion->grupo),
            'Grupo' => $this->teachersForGroup($reunion, $creator),
            'Jefe' => $this->departmentHeads(),
            default => collect(),
        };

        $this->assignTeachers($reunion, $teachers);
    }

    /**
     * Resol el professorat i alumnat d'un grup docent.
     *
     * @return Collection<int, Profesor>
     */
    private function teachersForGroup(Reunion $reunion, Profesor $creator): Collection
    {
        $group = $reunion->GrupoClase;

        if (!$group) {
            Log::warning("Reunió {$reunion->id} de grup sense grup docent; no s'assignen participants.");

            return collect();
        }

        if ($reunion->extraOrdinaria) {
            $students = $group->Alumnos;

            if ((int) $group->curso === 2) {
                $graduatingStudents = AlumnoFct::query()
                    ->misFcts((string) $creator->dni)
                    ->titulan()
                    ->pluck('idAlumno')
                    ->all();
                $students = $students->whereNotIn('nia', $graduatingStudents);
            }

            $studentPivots = $students
                ->mapWithKeys(static fn ($student): array => [
                    (string) $student->nia => ['capacitats' => 3],
                ])
                ->all();

            $reunion->alumnos()->syncWithoutDetaching($studentPivots);
        }

        return $this->profesorService->byGrupo((string) $group->codigo);
    }

    /**
     * Retorna caps de departament i membres de Direcció actius.
     *
     * @return Collection<int, Profesor>
     */
    private function departmentHeads(): Collection
    {
        $headRole = (int) config('roles.rol.jefe_dpto');
        $directionRole = (int) config('roles.rol.direccion');

        return $this->profesorService->activos()
            ->filter(static function (Profesor $teacher) use ($headRole, $directionRole): bool {
                $role = (int) $teacher->rol;

                return ($headRole > 0 && $role % $headRole === 0)
                    || ($directionRole > 0 && $role % $directionRole === 0);
            })
            ->values();
    }

    /**
     * Persistix els assistents sense crear duplicats.
     *
     * @param iterable<int, Profesor> $teachers
     */
    private function assignTeachers(Reunion $reunion, iterable $teachers): void
    {
        $pivots = [];

        foreach ($teachers as $teacher) {
            $substitutedTeacher = trim((string) $teacher->sustituye_a);
            if ($substitutedTeacher !== '') {
                $pivots[$substitutedTeacher] = ['asiste' => false];
            }

            $pivots[(string) $teacher->dni] = ['asiste' => $teacher->fecha_baja === null];
        }

        $reunion->profesores()->syncWithoutDetaching($pivots);
    }
}
