<?php

declare(strict_types=1);

namespace Intranet\Domain\Profesor;

use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Intranet\Entities\Profesor;

interface ProfesorRepositoryInterface
{
    /**
     * @return EloquentCollection<int, Profesor>
     */
    public function plantillaOrderedWithDepartamento(): EloquentCollection;

    /**
     * @param array<int, int|string> $departamentosIds
     * @return EloquentCollection<int, Profesor>
     */
    public function activosByDepartamentosWithHorario(array $departamentosIds, string $dia, int $sesion): EloquentCollection;

    /**
     * @return EloquentCollection<int, Profesor>
     */
    public function activosOrdered(): EloquentCollection;

    /**
     * @return EloquentCollection<int, Profesor>
     */
    public function all(): EloquentCollection;

    /**
     * @return EloquentCollection<int, Profesor>
     */
    public function plantilla(): EloquentCollection;

    /**
     * @return EloquentCollection<int, Profesor>
     */
    public function plantillaByDepartamento(int|string $departamento): EloquentCollection;

    /**
     * @return EloquentCollection<int, Profesor>
     */
    public function activos(): EloquentCollection;

    /**
     * @return EloquentCollection<int, Profesor>
     */
    public function byDepartamento(int|string $departamento): EloquentCollection;

    /**
     * @return EloquentCollection<int, Profesor>
     */
    public function byGrupo(string $grupo): EloquentCollection;

    /**
     * @return EloquentCollection<int, Profesor>
     */
    public function byGrupoTrabajo(string $grupoTrabajo): EloquentCollection;

    /**
     * @param array<int, string> $dnis
     * @return EloquentCollection<int, Profesor>
     */
    public function byDnis(array $dnis): EloquentCollection;

    public function find(string $dni): ?Profesor;

    public function findOrFail(string $dni): Profesor;

    public function findBySustituyeA(string $dni): ?Profesor;

    public function findByCodigo(string $codigo): ?Profesor;

    public function findByApiToken(string $apiToken): ?Profesor;

    public function findByEmail(string $email): ?Profesor;

    /**
     * @return EloquentCollection<int, Profesor>
     */
    public function plantillaOrderedByDepartamento(): EloquentCollection;

    /**
     * @return EloquentCollection<int, Profesor>
     */
    public function plantillaForResumen(): EloquentCollection;

    /**
     * @return EloquentCollection<int, Profesor>
     */
    public function allOrderedBySurname(): EloquentCollection;

    public function clearFechaBaja(): int;

    public function countByCodigo(int|string $codigo): int;

    /**
     * @param array<string, mixed> $data
     */
    public function create(array $data): Profesor;

    /**
     * @return EloquentCollection<int, Profesor>
     */
    public function withSustituyeAssigned(): EloquentCollection;
}
