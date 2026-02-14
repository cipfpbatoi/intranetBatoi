<?php

declare(strict_types=1);

namespace Intranet\Infrastructure\Persistence\Eloquent\Horario;

use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use Intranet\Domain\Horario\HorarioRepositoryInterface;
use Intranet\Entities\Horario;

class EloquentHorarioRepository implements HorarioRepositoryInterface
{
    public function semanalByProfesor(string $dni): array
    {
        return Horario::HorarioSemanal($dni);
    }

    public function semanalByGrupo(string $grupo): array
    {
        return Horario::HorarioGrupo($grupo);
    }

    public function lectivosByDayAndSesion(string $dia, int $sesion): EloquentCollection
    {
        return Horario::distinct()
            ->Dia($dia)
            ->where('sesion_orden', $sesion)
            ->Lectivos()
            ->whereNotNull('idGrupo')
            ->whereNull('ocupacion')
            ->get();
    }

    public function countByProfesorAndDay(string $dni, string $dia): int
    {
        return Horario::Profesor($dni)->Dia($dia)->count();
    }

    public function guardiaAllByDia(string $dia): EloquentCollection
    {
        return Horario::GuardiaAll()->Dia($dia)->get();
    }

    public function guardiaAllByProfesorAndDiaAndSesiones(string $dni, string $dia, array $sesiones): EloquentCollection
    {
        return Horario::distinct()
            ->Profesor($dni)
            ->Dia($dia)
            ->GuardiaAll()
            ->whereIn('sesion_orden', $sesiones)
            ->get();
    }

    public function guardiaAllByProfesorAndDia(string $dni, string $dia): EloquentCollection
    {
        return Horario::distinct()
            ->Profesor($dni)
            ->Dia($dia)
            ->GuardiaAll()
            ->get();
    }

    public function guardiaAllByProfesor(string $dni): EloquentCollection
    {
        return Horario::Profesor($dni)
            ->GuardiaAll()
            ->get();
    }

    public function firstByProfesorDiaSesion(string $dni, string $dia, int|string $sesion): ?Horario
    {
        return Horario::Dia($dia)
            ->Orden($sesion)
            ->Profesor($dni)
            ->first();
    }

    public function byProfesor(string $dni): EloquentCollection
    {
        return Horario::Profesor($dni)->get();
    }

    public function byProfesorWithRelations(string $dni, array $relations): EloquentCollection
    {
        return Horario::Profesor($dni)
            ->with($relations)
            ->get();
    }

    public function lectivasByProfesorAndDayOrdered(string $dni, string $dia): EloquentCollection
    {
        return Horario::Profesor($dni)
            ->Dia($dia)
            ->whereNotIn('modulo', config('constants.modulosNoLectivos'))
            ->whereNull('ocupacion')
            ->orderBy('sesion_orden')
            ->get();
    }

    public function reassignProfesor(string $fromDni, string $toDni): int
    {
        return Horario::where('idProfesor', $fromDni)->update(['idProfesor' => $toDni]);
    }

    public function deleteByProfesor(string $dni): int
    {
        return Horario::where('idProfesor', $dni)->delete();
    }

    public function gruposByProfesor(string $dni): Collection
    {
        return Horario::distinct()
            ->select('idGrupo')
            ->Profesor($dni)
            ->whereNotNull('idGrupo')
            ->get();
    }

    public function gruposByProfesorDiaAndSesiones(string $dni, string $dia, array $sesiones): Collection
    {
        return Horario::distinct()
            ->select('idGrupo')
            ->Profesor($dni)
            ->Dia($dia)
            ->whereNotNull('idGrupo')
            ->whereIn('sesion_orden', $sesiones)
            ->get();
    }

    public function profesoresByGruposExcept(array $grupos, string $emisorDni): Collection
    {
        return Horario::distinct()
            ->select('idProfesor')
            ->whereIn('idGrupo', $grupos)
            ->where('idProfesor', '<>', $emisorDni)
            ->get();
    }

    public function primeraByProfesorAndDateOrdered(string $dni, string $date): EloquentCollection
    {
        return Horario::Primera($dni, $date)
            ->orderBy('sesion_orden')
            ->get();
    }

    public function firstByModulo(string $modulo): ?Horario
    {
        return Horario::where('modulo', $modulo)->first();
    }

    public function byProfesorDiaOrdered(string $dni, string $dia): EloquentCollection
    {
        return Horario::Profesor($dni)
            ->Dia($dia)
            ->orderBy('sesion_orden')
            ->get();
    }

    public function distinctModulos(): Collection
    {
        return Horario::query()
            ->distinct()
            ->pluck('modulo');
    }

    public function create(array $data): Horario
    {
        return Horario::create($data);
    }

    public function forProgramacionImport(): EloquentCollection
    {
        return Horario::distinct()
            ->whereNotNull('idGrupo')
            ->whereNotNull('modulo')
            ->whereNotNull('idProfesor')
            ->whereNotIn('modulo', config('constants.modulosSinProgramacion'))
            ->get();
    }

    public function firstForDepartamentoAsignacion(string $dni): ?Horario
    {
        return Horario::where('idProfesor', $dni)
            ->whereNull('ocupacion')
            ->where('modulo', '!=', 'TU02CF')
            ->where('modulo', '!=', 'TU01CF')
            ->first();
    }
}
