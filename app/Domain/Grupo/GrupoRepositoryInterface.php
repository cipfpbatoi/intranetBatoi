<?php

declare(strict_types=1);

namespace Intranet\Domain\Grupo;

use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Intranet\Entities\Grupo;

interface GrupoRepositoryInterface
{
    /**
     * @param array<string, mixed> $attributes
     */
    public function create(array $attributes): Grupo;

    public function find(string $codigo): ?Grupo;

    /**
     * @return EloquentCollection<int, Grupo>
     */
    public function all(): EloquentCollection;

    /**
     * @return EloquentCollection<int, Grupo>
     */
    public function qTutor(string $dni): EloquentCollection;

    public function firstByTutor(string $dni): ?Grupo;

    public function largestByTutor(string $dni): ?Grupo;

    /**
     * @return EloquentCollection<int, Grupo>
     */
    public function byCurso(int $curso): EloquentCollection;

    /**
     * @return EloquentCollection<int, Grupo>
     */
    public function byDepartamento(int $departamento): EloquentCollection;

    /**
     * @return array<int, string>
     */
    public function tutoresDniList(): array;

    public function reassignTutor(string $fromDni, string $toDni): int;

    /**
     * @return EloquentCollection<int, Grupo>
     */
    public function misGrupos(): EloquentCollection;

    /**
     * @return EloquentCollection<int, Grupo>
     */
    public function misGruposByProfesor(string $dni): EloquentCollection;

    /**
     * @return EloquentCollection<int, Grupo>
     */
    public function withActaPendiente(): EloquentCollection;

    public function byTutorOrSubstitute(string $dni, ?string $sustituyeA): ?Grupo;

    /**
     * @return EloquentCollection<int, Grupo>
     */
    public function withStudents(): EloquentCollection;

    public function firstByTutorDual(string $dni): ?Grupo;

    /**
     * @param array<int, string> $codigos
     * @return EloquentCollection<int, Grupo>
     */
    public function byCodes(array $codigos): EloquentCollection;

    /**
     * @return EloquentCollection<int, Grupo>
     */
    public function allWithTutorAndCiclo(): EloquentCollection;

    /**
     * @return EloquentCollection<int, Grupo>
     */
    public function misGruposWithCiclo(): EloquentCollection;
}
