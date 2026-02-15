<?php

declare(strict_types=1);

namespace Intranet\Infrastructure\Persistence\Eloquent\AlumnoFct;

use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Intranet\Domain\AlumnoFct\AlumnoFctRepositoryInterface;
use Intranet\Entities\AlumnoFct;

/**
 * Implementació Eloquent del repositori d'AlumnoFct.
 */
class EloquentAlumnoFctRepository implements AlumnoFctRepositoryInterface
{
    public function all(): EloquentCollection
    {
        return AlumnoFct::all();
    }

    public function totesFcts(?string $profesor = null): EloquentCollection
    {
        return AlumnoFct::query()->totesFcts($profesor)->get();
    }

    public function find(int|string $id): ?AlumnoFct
    {
        return AlumnoFct::find($id);
    }

    public function findOrFail(int|string $id): AlumnoFct
    {
        /** @var AlumnoFct $registro */
        $registro = AlumnoFct::query()->findOrFail($id);

        return $registro;
    }

    public function firstByIdSao(int|string $idSao): ?AlumnoFct
    {
        return AlumnoFct::query()
            ->where('idSao', $idSao)
            ->first();
    }

    public function byAlumno(string $nia): EloquentCollection
    {
        return AlumnoFct::query()
            ->where('idAlumno', $nia)
            ->get();
    }

    public function byAlumnoWithA56(string $nia): EloquentCollection
    {
        return AlumnoFct::query()
            ->where('idAlumno', $nia)
            ->where('a56', '>', 0)
            ->get();
    }

    public function byGrupoEsFct(string $grupo): EloquentCollection
    {
        return AlumnoFct::query()
            ->Grupo($grupo)
            ->esFct()
            ->get();
    }

    public function byGrupoEsDual(string $grupo): EloquentCollection
    {
        return AlumnoFct::query()
            ->Grupo($grupo)
            ->esDual()
            ->get();
    }

    public function reassignProfesor(string $fromDni, string $toDni): int
    {
        return AlumnoFct::query()
            ->where('idProfesor', $fromDni)
            ->update(['idProfesor' => $toDni]);
    }
}
