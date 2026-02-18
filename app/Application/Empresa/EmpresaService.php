<?php

declare(strict_types=1);

namespace Intranet\Application\Empresa;

use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use Intranet\Domain\Empresa\EmpresaRepositoryInterface;
use Intranet\Entities\Ciclo;
use Intranet\Entities\Empresa;

/**
 * Casos d'ús d'aplicació per al domini d'empreses.
 */
class EmpresaService
{
    public function __construct(private readonly EmpresaRepositoryInterface $empresaRepository)
    {
    }

    /**
     * @return EloquentCollection<int, Empresa>
     */
    public function listForGrid(): EloquentCollection
    {
        return $this->empresaRepository->listForGrid();
    }

    public function findForShow(int $empresaId): Empresa
    {
        return $this->empresaRepository->findForShow($empresaId);
    }

    /**
     * @return Collection<int, mixed>
     */
    public function colaboracionIdsForTutorCycle(?int $tutorCycleId, Empresa $empresa): Collection
    {
        if ($tutorCycleId === null) {
            return collect();
        }

        return $this->empresaRepository->colaboracionIdsByCycleAndCenters(
            $tutorCycleId,
            $empresa->centros->pluck('id')->all()
        );
    }

    /**
     * @return EloquentCollection<int, Ciclo>
     */
    public function departmentCycles(?string $department): EloquentCollection
    {
        if ($department === null || $department === '') {
            return collect();
        }

        return $this->empresaRepository->cyclesByDepartment($department);
    }

    /**
     * @return EloquentCollection<int, Empresa>
     */
    public function convenioList(): EloquentCollection
    {
        return $this->empresaRepository->convenioList();
    }

    /**
     * @return EloquentCollection<int, Empresa>
     */
    public function socialConcertList(): EloquentCollection
    {
        return $this->empresaRepository->socialConcertList();
    }

    /**
     * @return EloquentCollection<int, Empresa>
     */
    public function erasmusList(): EloquentCollection
    {
        return $this->empresaRepository->erasmusList();
    }
}
