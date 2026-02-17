<?php

declare(strict_types=1);

namespace Intranet\Domain\Horario;

use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use Intranet\Entities\Horario;

/**
 * Contracte de persistència per a l'agregat Horario.
 */
interface HorarioRepositoryInterface
{
    /**
     * @return array<string, array<int, mixed>>
     */
    public function semanalByProfesor(string $dni): array;

    /**
     * @return array<string, array<int, mixed>>
     */
    public function semanalByGrupo(string $grupo): array;

    /**
     * @return EloquentCollection<int, mixed>
     */
    public function lectivosByDayAndSesion(string $dia, int $sesion): EloquentCollection;

    public function countByProfesorAndDay(string $dni, string $dia): int;

    /**
     * @return EloquentCollection<int, mixed>
     */
    public function guardiaAllByDia(string $dia): EloquentCollection;

    /**
     * @param array<int, int> $sesiones
     * @return EloquentCollection<int, mixed>
     */
    public function guardiaAllByProfesorAndDiaAndSesiones(string $dni, string $dia, array $sesiones): EloquentCollection;

    /**
     * @return EloquentCollection<int, mixed>
     */
    public function guardiaAllByProfesorAndDia(string $dni, string $dia): EloquentCollection;

    /**
     * @return EloquentCollection<int, mixed>
     */
    public function guardiaAllByProfesor(string $dni): EloquentCollection;

    public function firstByProfesorDiaSesion(string $dni, string $dia, int|string $sesion): ?Horario;

    /**
     * @return EloquentCollection<int, Horario>
     */
    public function byProfesor(string $dni): EloquentCollection;

    /**
     * @param array<int, string> $relations
     * @return EloquentCollection<int, Horario>
     */
    public function byProfesorWithRelations(string $dni, array $relations): EloquentCollection;

    /**
     * @return EloquentCollection<int, Horario>
     */
    public function lectivasByProfesorAndDayOrdered(string $dni, string $dia): EloquentCollection;

    public function reassignProfesor(string $fromDni, string $toDni): int;

    public function deleteByProfesor(string $dni): int;

    /**
     * @return Collection<int, mixed>
     */
    public function gruposByProfesor(string $dni): Collection;

    /**
     * @param array<int, int|string> $sesiones
     * @return Collection<int, mixed>
     */
    public function gruposByProfesorDiaAndSesiones(string $dni, string $dia, array $sesiones): Collection;

    /**
     * @param array<int, string> $grupos
     * @return Collection<int, mixed>
     */
    public function profesoresByGruposExcept(array $grupos, string $emisorDni): Collection;

    /**
     * @return EloquentCollection<int, Horario>
     */
    public function primeraByProfesorAndDateOrdered(string $dni, string $date): EloquentCollection;

    public function firstByModulo(string $modulo): ?Horario;

    /**
     * @return EloquentCollection<int, Horario>
     */
    public function byProfesorDiaOrdered(string $dni, string $dia): EloquentCollection;

    /**
     * @return Collection<int, string|null>
     */
    public function distinctModulos(): Collection;

    /**
     * @param array<string, mixed> $data
     */
    public function create(array $data): Horario;

    /**
     * @return EloquentCollection<int, Horario>
     */
    public function forProgramacionImport(): EloquentCollection;

    public function firstForDepartamentoAsignacion(string $dni): ?Horario;
}
