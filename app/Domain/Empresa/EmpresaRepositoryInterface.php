<?php

declare(strict_types=1);

namespace Intranet\Domain\Empresa;

use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use Intranet\Entities\Ciclo;
use Intranet\Entities\Empresa;

/**
 * Contracte de persistència per al domini d'empreses.
 */
interface EmpresaRepositoryInterface
{
    /**
     * @return EloquentCollection<int, Empresa>
     */
    public function listForGrid(): EloquentCollection;

    public function findForShow(int $id): Empresa;

    /**
     * @param array<int, int> $centerIds
     * @return Collection<int, mixed>
     */
    public function colaboracionIdsByCycleAndCenters(int $cycleId, array $centerIds): Collection;

    /**
     * @return EloquentCollection<int, Ciclo>
     */
    public function cyclesByDepartment(string $department): EloquentCollection;

    /**
     * @return EloquentCollection<int, Empresa>
     */
    public function convenioList(): EloquentCollection;

    /**
     * @return EloquentCollection<int, Empresa>
     */
    public function socialConcertList(): EloquentCollection;

    /**
     * @return EloquentCollection<int, Empresa>
     */
    public function erasmusList(): EloquentCollection;
}
