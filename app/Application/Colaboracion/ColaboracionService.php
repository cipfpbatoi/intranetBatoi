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
    public function __construct(
        private readonly ColaboracionQueryService $queryService,
        private readonly ColaboracionPreasignacionService $preasignacionService
    )
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
        $activitiesByColab = $this->queryService->groupedActivitiesByColaboracion(
            $meves->concat($relacionades)->values()
        );

        $panel = $this->queryService
            ->attachRelatedAndContacts($meves, $relacionades, $activitiesByColab)
            ->sortBy(static fn ($item) => $item->empresa)
            ->values();

        return $this->preasignacionService->hydrateForPanel($panel, $dni);
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
