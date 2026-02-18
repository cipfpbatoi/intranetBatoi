<?php

declare(strict_types=1);

namespace Intranet\Application\Colaboracion;

use Illuminate\Support\Collection;
use Intranet\Entities\Activity;
use Intranet\Entities\Colaboracion;

/**
 * Consultes de lectura per al domini de col·laboracions.
 */
class ColaboracionQueryService
{
    /**
     * @return Collection<int, Colaboracion>
     */
    public function myColaboraciones(string $dni): Collection
    {
        return Colaboracion::query()
            ->MiColaboracion(null, $dni)
            ->with(['Propietario', 'Centro', 'Centro.Empresa', 'Ciclo'])
            ->get();
    }

    /**
     * @param Collection<int, Colaboracion> $meves
     * @return Collection<int, Colaboracion>
     */
    public function relatedByCenterDepartment(Collection $meves): Collection
    {
        $parelles = $meves
            ->filter(static fn ($item): bool => optional($item->Ciclo)->departamento !== null)
            ->map(static fn ($item): array => [
                'idCentro' => $item->idCentro,
                'departamento' => $item->Ciclo->departamento,
            ])
            ->unique(static fn (array $pair): string => $pair['idCentro'] . '|' . $pair['departamento'])
            ->values();

        if ($parelles->isEmpty()) {
            return collect();
        }

        return Colaboracion::query()
            ->with(['Ciclo', 'Propietario', 'Centro', 'Centro.Empresa'])
            ->whereNotIn('id', $meves->pluck('id'))
            ->where(function ($query) use ($parelles): void {
                foreach ($parelles as $pair) {
                    $query->orWhere(function ($inner) use ($pair): void {
                        $inner->where('idCentro', $pair['idCentro'])
                            ->whereHas('Ciclo', function ($cycleQuery) use ($pair): void {
                                $cycleQuery->where('departamento', $pair['departamento']);
                            });
                    });
                }
            })
            ->get()
            ->filter(function ($related) use ($meves): bool {
                $relatedCiclo = $related->idCiclo ?? $related->ciclo_id ?? null;

                return $meves->contains(function ($mine) use ($related, $relatedCiclo): bool {
                    $mineCiclo = $mine->idCiclo ?? $mine->ciclo_id ?? null;

                    return $mine->idCentro == $related->idCentro
                        && optional($mine->Ciclo)->departamento === optional($related->Ciclo)->departamento
                        && $mineCiclo !== $relatedCiclo;
                });
            })
            ->values();
    }

    /**
     * @param Collection<int, Colaboracion> $colaboraciones
     * @return Collection<string, Collection<int, Activity>>
     */
    public function groupedActivitiesByColaboracion(Collection $colaboraciones): Collection
    {
        if ($colaboraciones->isEmpty()) {
            return collect();
        }

        return Activity::query()
            ->modelo('Colaboracion')
            ->notUpdate()
            ->ids($colaboraciones->pluck('id')->all())
            ->orderBy('created_at')
            ->get()
            ->groupBy('model_id');
    }

    /**
     * @param Collection<int, Colaboracion> $meves
     * @param Collection<int, Colaboracion> $relacionades
     * @param Collection<string, Collection<int, Activity>> $activitiesByColab
     * @return Collection<int, Colaboracion>
     */
    public function attachRelatedAndContacts(
        Collection $meves,
        Collection $relacionades,
        Collection $activitiesByColab
    ): Collection {
        $pairKey = static fn ($item): string => $item->idCentro . '|' . (string) optional($item->Ciclo)->departamento;
        $relacionadesPerParella = $relacionades->groupBy($pairKey);

        $meves->each(function ($mine) use ($pairKey, $relacionadesPerParella, $activitiesByColab): void {
            $llista = $relacionadesPerParella->get($pairKey($mine), collect());

            $llista->each(function ($related) use ($activitiesByColab): void {
                $related->contactos = $activitiesByColab->get($related->id, collect());
            });

            $mine->relacionadas = $llista->values();
        });

        return $meves;
    }
}

