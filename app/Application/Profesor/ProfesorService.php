<?php

declare(strict_types=1);

namespace Intranet\Application\Profesor;

use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Intranet\Domain\Profesor\ProfesorRepositoryInterface;
use Intranet\Entities\Profesor;

class ProfesorService
{
    public function __construct(private readonly ProfesorRepositoryInterface $profesorRepository)
    {
    }

    /**
     * @return EloquentCollection<int, Profesor>
     */
    public function plantillaOrderedWithDepartamento(): EloquentCollection
    {
        return $this->profesorRepository->plantillaOrderedWithDepartamento();
    }

    /**
     * @param array<int, int|string> $departamentosIds
     * @return EloquentCollection<int, Profesor>
     */
    public function activosByDepartamentosWithHorario(array $departamentosIds, string $dia, int $sesion): EloquentCollection
    {
        return $this->profesorRepository->activosByDepartamentosWithHorario($departamentosIds, $dia, $sesion);
    }

    /**
     * @return EloquentCollection<int, Profesor>
     */
    public function activosOrdered(): EloquentCollection
    {
        return $this->profesorRepository->activosOrdered();
    }

    /**
     * @return EloquentCollection<int, Profesor>
     */
    public function all(): EloquentCollection
    {
        return $this->profesorRepository->all();
    }

    /**
     * @return EloquentCollection<int, Profesor>
     */
    public function plantilla(): EloquentCollection
    {
        return $this->profesorRepository->plantilla();
    }

    /**
     * @return EloquentCollection<int, Profesor>
     */
    public function plantillaByDepartamento(int|string $departamento): EloquentCollection
    {
        return $this->profesorRepository->plantillaByDepartamento($departamento);
    }

    /**
     * @return EloquentCollection<int, Profesor>
     */
    public function activos(): EloquentCollection
    {
        return $this->profesorRepository->activos();
    }

    /**
     * @return EloquentCollection<int, Profesor>
     */
    public function byDepartamento(int|string $departamento): EloquentCollection
    {
        return $this->profesorRepository->byDepartamento($departamento);
    }

    /**
     * @return EloquentCollection<int, Profesor>
     */
    public function byGrupo(string $grupo): EloquentCollection
    {
        return $this->profesorRepository->byGrupo($grupo);
    }

    /**
     * @return EloquentCollection<int, Profesor>
     */
    public function byGrupoTrabajo(string $grupoTrabajo): EloquentCollection
    {
        return $this->profesorRepository->byGrupoTrabajo($grupoTrabajo);
    }

    /**
     * @param array<int, string> $dnis
     * @return EloquentCollection<int, Profesor>
     */
    public function byDnis(array $dnis): EloquentCollection
    {
        return $this->profesorRepository->byDnis($dnis);
    }

    public function find(string $dni): ?Profesor
    {
        return $this->profesorRepository->find($dni);
    }

    public function findOrFail(string $dni): Profesor
    {
        return $this->profesorRepository->findOrFail($dni);
    }

    public function findBySustituyeA(string $dni): ?Profesor
    {
        return $this->profesorRepository->findBySustituyeA($dni);
    }

    public function findByCodigo(string $codigo): ?Profesor
    {
        return $this->profesorRepository->findByCodigo($codigo);
    }

    public function findByApiToken(string $apiToken): ?Profesor
    {
        return $this->profesorRepository->findByApiToken($apiToken);
    }

    public function findByEmail(string $email): ?Profesor
    {
        return $this->profesorRepository->findByEmail($email);
    }

    /**
     * @return EloquentCollection<int, Profesor>
     */
    public function plantillaOrderedByDepartamento(): EloquentCollection
    {
        return $this->profesorRepository->plantillaOrderedByDepartamento();
    }

    /**
     * @return EloquentCollection<int, Profesor>
     */
    public function plantillaForResumen(): EloquentCollection
    {
        return $this->profesorRepository->plantillaForResumen();
    }

    /**
     * @return EloquentCollection<int, Profesor>
     */
    public function allOrderedBySurname(): EloquentCollection
    {
        return $this->profesorRepository->allOrderedBySurname();
    }

    public function clearFechaBaja(): int
    {
        return $this->profesorRepository->clearFechaBaja();
    }

    public function countByCodigo(int|string $codigo): int
    {
        return $this->profesorRepository->countByCodigo($codigo);
    }

    /**
     * @param array<string, mixed> $data
     */
    public function create(array $data): Profesor
    {
        return $this->profesorRepository->create($data);
    }

    /**
     * @return EloquentCollection<int, Profesor>
     */
    public function withSustituyeAssigned(): EloquentCollection
    {
        return $this->profesorRepository->withSustituyeAssigned();
    }
}
