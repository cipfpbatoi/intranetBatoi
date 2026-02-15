<?php

declare(strict_types=1);

namespace Intranet\Application\Grupo;

use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Intranet\Domain\Grupo\GrupoRepositoryInterface;
use Intranet\Entities\Grupo;

class GrupoService
{
    public function __construct(private readonly GrupoRepositoryInterface $grupoRepository)
    {
    }

    /**
     * @param array<string, mixed> $attributes
     */
    public function create(array $attributes): Grupo
    {
        return $this->grupoRepository->create($attributes);
    }

    public function find(string $codigo): ?Grupo
    {
        return $this->grupoRepository->find($codigo);
    }

    /**
     * @return EloquentCollection<int, Grupo>
     */
    public function all(): EloquentCollection
    {
        return $this->grupoRepository->all();
    }

    /**
     * @return EloquentCollection<int, Grupo>
     */
    public function qTutor(string $dni): EloquentCollection
    {
        return $this->grupoRepository->qTutor($dni);
    }

    public function firstByTutor(string $dni): ?Grupo
    {
        return $this->grupoRepository->firstByTutor($dni);
    }

    public function largestByTutor(string $dni): ?Grupo
    {
        return $this->grupoRepository->largestByTutor($dni);
    }

    /**
     * @return EloquentCollection<int, Grupo>
     */
    public function byCurso(int $curso): EloquentCollection
    {
        return $this->grupoRepository->byCurso($curso);
    }

    /**
     * @return EloquentCollection<int, Grupo>
     */
    public function byDepartamento(int $departamento): EloquentCollection
    {
        return $this->grupoRepository->byDepartamento($departamento);
    }

    /**
     * @return array<int, string>
     */
    public function tutoresDniList(): array
    {
        return $this->grupoRepository->tutoresDniList();
    }

    public function reassignTutor(string $fromDni, string $toDni): int
    {
        return $this->grupoRepository->reassignTutor($fromDni, $toDni);
    }

    /**
     * @return EloquentCollection<int, Grupo>
     */
    public function misGrupos(): EloquentCollection
    {
        return $this->grupoRepository->misGrupos();
    }

    /**
     * @return EloquentCollection<int, Grupo>
     */
    public function misGruposByProfesor(string $dni): EloquentCollection
    {
        return $this->grupoRepository->misGruposByProfesor($dni);
    }

    /**
     * @return EloquentCollection<int, Grupo>
     */
    public function withActaPendiente(): EloquentCollection
    {
        return $this->grupoRepository->withActaPendiente();
    }

    public function byTutorOrSubstitute(string $dni, ?string $sustituyeA): ?Grupo
    {
        return $this->grupoRepository->byTutorOrSubstitute($dni, $sustituyeA);
    }

    /**
     * @return EloquentCollection<int, Grupo>
     */
    public function withStudents(): EloquentCollection
    {
        return $this->grupoRepository->withStudents();
    }

    public function firstByTutorDual(string $dni): ?Grupo
    {
        return $this->grupoRepository->firstByTutorDual($dni);
    }

    /**
     * @param array<int, string> $codigos
     * @return EloquentCollection<int, Grupo>
     */
    public function byCodes(array $codigos): EloquentCollection
    {
        return $this->grupoRepository->byCodes($codigos);
    }

    /**
     * @return EloquentCollection<int, Grupo>
     */
    public function allWithTutorAndCiclo(): EloquentCollection
    {
        return $this->grupoRepository->allWithTutorAndCiclo();
    }

    /**
     * @return EloquentCollection<int, Grupo>
     */
    public function misGruposWithCiclo(): EloquentCollection
    {
        return $this->grupoRepository->misGruposWithCiclo();
    }
}
