<?php

declare(strict_types=1);

namespace Intranet\Infrastructure\Persistence\Eloquent\Empresa;

use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use Intranet\Domain\Empresa\EmpresaRepositoryInterface;
use Intranet\Entities\Ciclo;
use Intranet\Entities\Colaboracion;
use Intranet\Entities\Empresa;

/**
 * Implementació Eloquent del repositori d'empreses.
 */
class EloquentEmpresaRepository implements EmpresaRepositoryInterface
{
    public function listForGrid(): EloquentCollection
    {
        return Empresa::query()
            ->select(['id', 'concierto', 'nombre', 'direccion', 'localidad', 'telefono', 'email', 'cif', 'actividad'])
            ->orderBy('nombre')
            ->get();
    }

    public function findForShow(int $id): Empresa
    {
        return Empresa::with([
            'centros.colaboraciones.ciclo',
            'centros.instructores',
        ])->findOrFail($id);
    }

    public function colaboracionIdsByCycleAndCenters(int $cycleId, array $centerIds): Collection
    {
        if ($centerIds === []) {
            return collect();
        }

        return Colaboracion::query()
            ->where('idCiclo', $cycleId)
            ->whereIn('idCentro', $centerIds)
            ->pluck('id');
    }

    public function cyclesByDepartment(string $department): EloquentCollection
    {
        return Ciclo::query()
            ->where('departamento', $department)
            ->get();
    }

    public function convenioList(): EloquentCollection
    {
        return Empresa::query()
            ->select([
                'id',
                'concierto',
                'nombre',
                'direccion',
                'localidad',
                'telefono',
                'email',
                'cif',
                'actividad',
                'fichero',
            ])
            ->where('concierto', '>', 0)
            ->where('europa', 0)
            ->orderBy('nombre')
            ->get();
    }

    public function socialConcertList(): EloquentCollection
    {
        return Empresa::whereNull('concierto')->get();
    }

    public function erasmusList(): EloquentCollection
    {
        return Empresa::where('europa', 1)->get();
    }
}
