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
    /**
     * {@inheritdoc}
     */
    public function all(): EloquentCollection
    {
        return AlumnoFct::all();
    }

    /**
     * {@inheritdoc}
     */
    public function totesFcts(?string $profesor = null): EloquentCollection
    {
        return AlumnoFct::query()->totesFcts($profesor)->get();
    }

    /**
     * {@inheritdoc}
     */
    public function find(int|string $id): ?AlumnoFct
    {
        return AlumnoFct::find($id);
    }

    /**
     * {@inheritdoc}
     */
    public function findOrFail(int|string $id): AlumnoFct
    {
        /** @var AlumnoFct $registro */
        $registro = AlumnoFct::query()->findOrFail($id);

        return $registro;
    }

    /**
     * {@inheritdoc}
     */
    public function firstByIdSao(int|string $idSao): ?AlumnoFct
    {
        return AlumnoFct::query()
            ->where('idSao', $idSao)
            ->first();
    }

    /**
     * {@inheritdoc}
     */
    public function byAlumno(string $nia): EloquentCollection
    {
        return AlumnoFct::query()
            ->where('idAlumno', $nia)
            ->get();
    }

    /**
     * {@inheritdoc}
     */
    public function byAlumnoWithA56(string $nia): EloquentCollection
    {
        return AlumnoFct::query()
            ->where('idAlumno', $nia)
            ->where('a56', '>', 0)
            ->get();
    }

    /**
     * {@inheritdoc}
     */
    public function byGrupoEsFct(string $grupo): EloquentCollection
    {
        return AlumnoFct::query()
            ->Grupo($grupo)
            ->esFct()
            ->get();
    }

    /**
     * {@inheritdoc}
     */
    public function byGrupoEsDual(string $grupo): EloquentCollection
    {
        return AlumnoFct::query()
            ->Grupo($grupo)
            ->esDual()
            ->get();
    }

    /**
     * {@inheritdoc}
     */
    public function reassignProfesor(string $fromDni, string $toDni): int
    {
        return AlumnoFct::query()
            ->where('idProfesor', $fromDni)
            ->update(['idProfesor' => $toDni]);
    }

    /**
     * {@inheritdoc}
     */
    public function avalDistinctAlumnoIdsByProfesor(?string $profesor = null): array
    {
        return AlumnoFct::query()
            ->misFcts($profesor)
            ->esAval()
            ->distinct()
            ->pluck('idAlumno')
            ->map(static fn ($id): string => (string) $id)
            ->values()
            ->all();
    }

    /**
     * {@inheritdoc}
     */
    public function latestAvalByAlumnoAndProfesor(string $idAlumno, ?string $profesor = null): ?AlumnoFct
    {
        return AlumnoFct::query()
            ->misFcts($profesor)
            ->esAval()
            ->where('idAlumno', $idAlumno)
            ->orderByDesc('idSao')
            ->first();
    }

    /**
     * {@inheritdoc}
     */
    public function avaluablesNoAval(?string $profesor = null, mixed $grupo = null): EloquentCollection
    {
        $query = AlumnoFct::query()
            ->misFcts($profesor)
            ->esAval()
            ->where('actas', '<', 2);

        if ($grupo !== null) {
            $query->Grupo($grupo);
        }

        return $query->get();
    }
}
