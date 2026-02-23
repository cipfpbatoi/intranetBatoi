<?php

declare(strict_types=1);

namespace Intranet\Application\Colaboracion;

use Illuminate\Support\Collection;
use Intranet\Entities\Colaboracion;

/**
 * Casos d'ús d'aplicació per al panell de col·laboracions.
 */
class ColaboracionService
{
    public function __construct(private readonly ColaboracionQueryService $queryService)
    {
    }

    /**
     * @return Collection<int, Colaboracion>
     */
    public function panelListingByTutor(string $dni): Collection
    {
        $meves = $this->queryService->myColaboraciones($dni);

        if ($meves->isEmpty()) {
            return $meves;
        }

        $relacionades = $this->queryService->relatedByCenterDepartment($meves);
        $activitiesByColab = $this->queryService->groupedActivitiesByColaboracion($relacionades);

        return $this->queryService
            ->attachRelatedAndContacts($meves, $relacionades, $activitiesByColab)
            ->sortBy(static fn ($item) => $item->empresa)
            ->values();
    }

    /**
     * @param Collection<int, Colaboracion> $colaboraciones
     */
    public function resolvePanelTitle(Collection $colaboraciones): ?string
    {
        if ($colaboraciones->isEmpty()) {
            return null;
        }

        return optional($colaboraciones->first()->Ciclo)->literal;
    }
}

