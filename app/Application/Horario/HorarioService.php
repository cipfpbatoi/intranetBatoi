<?php

declare(strict_types=1);

namespace Intranet\Application\Horario;

use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use Intranet\Domain\Horario\HorarioRepositoryInterface;
use Intranet\Entities\Horario;

class HorarioService
{
    public function __construct(private readonly HorarioRepositoryInterface $horarioRepository)
    {
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function semanalByProfesor(string $dni): array
    {
        return $this->horarioRepository->semanalByProfesor($dni);
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function semanalByGrupo(string $grupo): array
    {
        return $this->horarioRepository->semanalByGrupo($grupo);
    }

    /**
     * @return EloquentCollection<int, mixed>
     */
    public function lectivosByDayAndSesion(string $dia, int $sesion): EloquentCollection
    {
        return $this->horarioRepository->lectivosByDayAndSesion($dia, $sesion);
    }

    public function countByProfesorAndDay(string $dni, string $dia): int
    {
        return $this->horarioRepository->countByProfesorAndDay($dni, $dia);
    }

    /**
     * @return EloquentCollection<int, mixed>
     */
    public function guardiaAllByDia(string $dia): EloquentCollection
    {
        return $this->horarioRepository->guardiaAllByDia($dia);
    }

    /**
     * @param array<int, int> $sesiones
     * @return EloquentCollection<int, mixed>
     */
    public function guardiaAllByProfesorAndDiaAndSesiones(string $dni, string $dia, array $sesiones): EloquentCollection
    {
        return $this->horarioRepository->guardiaAllByProfesorAndDiaAndSesiones($dni, $dia, $sesiones);
    }

    /**
     * @return EloquentCollection<int, mixed>
     */
    public function guardiaAllByProfesorAndDia(string $dni, string $dia): EloquentCollection
    {
        return $this->horarioRepository->guardiaAllByProfesorAndDia($dni, $dia);
    }

    /**
     * @return EloquentCollection<int, mixed>
     */
    public function guardiaAllByProfesor(string $dni): EloquentCollection
    {
        return $this->horarioRepository->guardiaAllByProfesor($dni);
    }

    public function firstByProfesorDiaSesion(string $dni, string $dia, int|string $sesion): ?Horario
    {
        return $this->horarioRepository->firstByProfesorDiaSesion($dni, $dia, $sesion);
    }

    /**
     * @return EloquentCollection<int, Horario>
     */
    public function byProfesor(string $dni): EloquentCollection
    {
        return $this->horarioRepository->byProfesor($dni);
    }

    /**
     * @param array<int, string> $relations
     * @return EloquentCollection<int, Horario>
     */
    public function byProfesorWithRelations(string $dni, array $relations): EloquentCollection
    {
        return $this->horarioRepository->byProfesorWithRelations($dni, $relations);
    }

    /**
     * @return EloquentCollection<int, Horario>
     */
    public function lectivasByProfesorAndDayOrdered(string $dni, string $dia): EloquentCollection
    {
        return $this->horarioRepository->lectivasByProfesorAndDayOrdered($dni, $dia);
    }

    public function reassignProfesor(string $fromDni, string $toDni): int
    {
        return $this->horarioRepository->reassignProfesor($fromDni, $toDni);
    }

    public function deleteByProfesor(string $dni): int
    {
        return $this->horarioRepository->deleteByProfesor($dni);
    }

    /**
     * @return Collection<int, mixed>
     */
    public function gruposByProfesor(string $dni): Collection
    {
        return $this->horarioRepository->gruposByProfesor($dni);
    }

    /**
     * @param array<int, int|string> $sesiones
     * @return Collection<int, mixed>
     */
    public function gruposByProfesorDiaAndSesiones(string $dni, string $dia, array $sesiones): Collection
    {
        return $this->horarioRepository->gruposByProfesorDiaAndSesiones($dni, $dia, $sesiones);
    }

    /**
     * @param array<int, string> $grupos
     * @return Collection<int, mixed>
     */
    public function profesoresByGruposExcept(array $grupos, string $emisorDni): Collection
    {
        return $this->horarioRepository->profesoresByGruposExcept($grupos, $emisorDni);
    }

    /**
     * @return EloquentCollection<int, Horario>
     */
    public function primeraByProfesorAndDateOrdered(string $dni, string $date): EloquentCollection
    {
        return $this->horarioRepository->primeraByProfesorAndDateOrdered($dni, $date);
    }

    public function firstByModulo(string $modulo): ?Horario
    {
        return $this->horarioRepository->firstByModulo($modulo);
    }

    /**
     * @return EloquentCollection<int, Horario>
     */
    public function byProfesorDiaOrdered(string $dni, string $dia): EloquentCollection
    {
        return $this->horarioRepository->byProfesorDiaOrdered($dni, $dia);
    }

    /**
     * @return Collection<int, string|null>
     */
    public function distinctModulos(): Collection
    {
        return $this->horarioRepository->distinctModulos();
    }

    /**
     * @param array<string, mixed> $data
     */
    public function create(array $data): Horario
    {
        return $this->horarioRepository->create($data);
    }

    /**
     * @return EloquentCollection<int, Horario>
     */
    public function forProgramacionImport(): EloquentCollection
    {
        return $this->horarioRepository->forProgramacionImport();
    }

    public function firstForDepartamentoAsignacion(string $dni): ?Horario
    {
        return $this->horarioRepository->firstForDepartamentoAsignacion($dni);
    }
}
