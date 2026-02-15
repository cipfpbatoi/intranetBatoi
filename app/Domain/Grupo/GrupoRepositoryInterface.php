<?php

declare(strict_types=1);

namespace Intranet\Domain\Grupo;

use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Intranet\Entities\Grupo;

/**
 * Contracte d'accés a dades de l'agregat Grupo.
 *
 * Separa els casos d'ús de la implementació Eloquent.
 */
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

    /**
     * Retorna el primer grup on el professor és tutor o tutor substituït.
     *
     * @param string $dni DNI del professor autenticat
     * @param string|null $sustituyeA DNI del professor substituït
     */
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
