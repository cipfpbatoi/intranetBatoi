<?php

declare(strict_types=1);

namespace Intranet\Infrastructure\Persistence\Eloquent\Profesor;

use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Intranet\Domain\Profesor\ProfesorRepositoryInterface;
use Intranet\Entities\Profesor;

class EloquentProfesorRepository implements ProfesorRepositoryInterface
{
    public function plantillaOrderedWithDepartamento(): EloquentCollection
    {
        return Profesor::orderBy('apellido1')
            ->with('Departamento')
            ->Plantilla()
            ->get();
    }

    public function activosByDepartamentosWithHorario(array $departamentosIds, string $dia, int $sesion): EloquentCollection
    {
        return Profesor::orderBy('apellido1')
            ->whereIn('departamento', $departamentosIds)
            ->with('Departamento')
            ->with([
                'Horari' => function ($query) use ($dia, $sesion): void {
                    $query->where('dia_semana', $dia)
                        ->where('sesion_orden', $sesion)
                        ->with('Ocupacion')
                        ->with('Modulo')
                        ->with('Grupo');
                },
            ])
            ->Activo()
            ->get();
    }

    public function activosOrdered(): EloquentCollection
    {
        return Profesor::Activo()
            ->orderBy('apellido1', 'asc')
            ->orderBy('apellido2', 'asc')
            ->get();
    }

    public function all(): EloquentCollection
    {
        return Profesor::all();
    }

    public function plantilla(): EloquentCollection
    {
        return Profesor::Plantilla()->get();
    }

    public function plantillaByDepartamento(int|string $departamento): EloquentCollection
    {
        return Profesor::Plantilla()
            ->where('departamento', '=', (string) $departamento)
            ->get();
    }

    public function activos(): EloquentCollection
    {
        return Profesor::Activo()->get();
    }

    public function byDepartamento(int|string $departamento): EloquentCollection
    {
        return Profesor::where('departamento', '=', (string) $departamento)->get();
    }

    public function byGrupo(string $grupo): EloquentCollection
    {
        return Profesor::orderBy('apellido1', 'asc')
            ->orderBy('apellido2', 'asc')
            ->Grupo($grupo)
            ->get();
    }

    public function byGrupoTrabajo(string $grupoTrabajo): EloquentCollection
    {
        return Profesor::GrupoT($grupoTrabajo)->get();
    }

    public function byDnis(array $dnis): EloquentCollection
    {
        return Profesor::whereIn('dni', $dnis)->get();
    }

    public function find(string $dni): ?Profesor
    {
        return Profesor::find($dni);
    }

    public function findOrFail(string $dni): Profesor
    {
        return Profesor::findOrFail($dni);
    }

    public function findBySustituyeA(string $dni): ?Profesor
    {
        return Profesor::where('sustituye_a', $dni)->first();
    }

    public function findByCodigo(string $codigo): ?Profesor
    {
        return Profesor::where('codigo', '=', $codigo)->first();
    }

    public function findByApiToken(string $apiToken): ?Profesor
    {
        return Profesor::where('api_token', $apiToken)->first();
    }

    public function findByEmail(string $email): ?Profesor
    {
        return Profesor::where('email', $email)->first();
    }

    public function plantillaOrderedByDepartamento(): EloquentCollection
    {
        return Profesor::Plantilla()
            ->orderBy('departamento')
            ->orderBy('apellido1')
            ->orderBy('apellido2')
            ->get();
    }

    public function plantillaForResumen(): EloquentCollection
    {
        return Profesor::Plantilla()
            ->select('dni', 'nombre', 'apellido1', 'apellido2')
            ->orderBy('apellido1')
            ->orderBy('apellido2')
            ->orderBy('nombre')
            ->get();
    }

    public function allOrderedBySurname(): EloquentCollection
    {
        return Profesor::select('dni', 'apellido1', 'apellido2', 'nombre')
            ->orderBy('apellido1')
            ->orderBy('apellido2')
            ->get();
    }

    public function clearFechaBaja(): int
    {
        return Profesor::whereNotNull('fecha_baja')->update(['fecha_baja' => null]);
    }

    public function countByCodigo(int|string $codigo): int
    {
        return Profesor::where('codigo', $codigo)->count();
    }

    public function create(array $data): Profesor
    {
        return Profesor::create($data);
    }

    public function withSustituyeAssigned(): EloquentCollection
    {
        return Profesor::where('sustituye_a', '>', ' ')->get();
    }
}
