<?php

declare(strict_types=1);

namespace Intranet\Application\Colaboracion;

use Illuminate\Support\Carbon;
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
            ->with(['Propietario', 'Centro', 'Centro.Empresa', 'Centro.instructores', 'Ciclo'])
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
            ->with(['Ciclo', 'Propietario', 'Centro', 'Centro.Empresa', 'Centro.instructores'])
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
            $this->hydratePanelIndicators($mine, $activitiesByColab->get($mine->id, collect()));

            $llista->each(function ($related) use ($activitiesByColab): void {
                $this->hydratePanelIndicators($related, $activitiesByColab->get($related->id, collect()));
            });

            $mine->relacionadas = $llista->values();
        });

        return $meves;
    }

    /**
     * Adjunta els indicadors de fitxa necessaris per al panell sense dispersar la lògica en la vista.
     *
     * @param Collection<int, Activity> $activities
     */
    private function hydratePanelIndicators(Colaboracion $colaboracion, Collection $activities): void
    {
        $colaboracion->contactos = $activities;
        $colaboracion->ultimaActividad = $activities->last();
        $colaboracion->ultima_actividad = $colaboracion->ultimaActividad?->created_at;
        $colaboracion->diesSenseContacte = $this->daysSince($colaboracion->ultima_actividad);

        $empresa = $colaboracion->Centro->Empresa ?? null;
        $hasInstructor = ($colaboracion->Centro->instructores?->isNotEmpty()) ?? false;
        $conveniTall = $this->annexICutoffDate();
        $conveniData = $this->annexISignatureDate($empresa);
        $conveniPendent = blank($empresa?->concierto)
            || $conveniData === null
            || $conveniData->lt($conveniTall);

        $badges = collect();

        if ($this->isBlankText($colaboracion->contacto ?? null)) {
            $badges->push($this->panelBadge('Sense contacte', 'bg-danger', 'fa-user'));
        }

        if ($this->isBlankText($colaboracion->telefono ?? null)) {
            $badges->push($this->panelBadge('Sense telèfon', 'bg-warning text-dark', 'fa-phone'));
        }

        if ($this->isBlankText($colaboracion->email ?? null)) {
            $badges->push($this->panelBadge('Sense email', 'bg-warning text-dark', 'fa-envelope'));
        }

        if (!$hasInstructor) {
            $badges->push($this->panelBadge('Sense instructor', 'bg-warning text-dark', 'fa-user-secret'));
        }

        if ($conveniPendent) {
            $badges->push($this->panelBadge('Conveni pendent', 'bg-danger', 'fa-file-text-o'));
        }

        $prioritatFitxa = 0;
        $prioritatFitxa += $this->isBlankText($colaboracion->contacto ?? null) ? 5 : 0;
        $prioritatFitxa += !$hasInstructor ? 4 : 0;
        $prioritatFitxa += $conveniPendent ? 4 : 0;
        $prioritatFitxa += $this->isBlankText($colaboracion->email ?? null) ? 2 : 0;
        $prioritatFitxa += $this->isBlankText($colaboracion->telefono ?? null) ? 2 : 0;

        $colaboracion->hasInstructor = $hasInstructor;
        $colaboracion->conveniPendent = $conveniPendent;
        $colaboracion->prioritatFitxa = $prioritatFitxa;
        $colaboracion->fitxaBadges = $badges;
        $colaboracion->fitxaIncompleta = $badges->isNotEmpty();
        $colaboracion->estatFitxaLabel = $badges->isEmpty() ? 'Fitxa al dia' : 'Cal revisar';
        $colaboracion->estatFitxaClass = $badges->isEmpty() ? 'bg-success' : 'bg-warning text-dark';
        $colaboracion->annexIData = $conveniData?->format('d-m-Y');
        $colaboracion->annexITall = $conveniTall->format('d-m-Y');
    }

    /**
     * Normalitza el format dels badges de qualitat de fitxa del panell.
     *
     * @return array{label:string,class:string,icon:string}
     */
    private function panelBadge(string $label, string $class, string $icon): array
    {
        return [
            'label' => $label,
            'class' => $class,
            'icon' => $icon,
        ];
    }

    private function isBlankText(?string $value): bool
    {
        return trim((string) $value) === '';
    }

    private function daysSince($dateTime): ?int
    {
        if ($dateTime === null) {
            return null;
        }

        return (int) Carbon::parse($dateTime)->startOfDay()->diffInDays(Carbon::now()->startOfDay());
    }

    private function annexISignatureDate($empresa): ?Carbon
    {
        $rawDate = $empresa?->getRawOriginal('data_signatura');

        if (!$rawDate) {
            return null;
        }

        return Carbon::parse($rawDate)->startOfDay();
    }

    private function annexICutoffDate(): Carbon
    {
        return Carbon::create(2024, 1, 1)->startOfDay();
    }
}
