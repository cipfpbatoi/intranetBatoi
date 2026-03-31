<?php

declare(strict_types=1);

namespace Intranet\Application\Colaboracion;

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Intranet\Entities\Activity;
use Intranet\Entities\Colaboracion;
use Intranet\Application\Seguimiento\SeguimientoService;

/**
 * Consultes de lectura per al domini de col·laboracions.
 */
class ColaboracionQueryService
{
    public function __construct(private readonly SeguimientoService $seguimientoService)
    {
    }

    /**
     * @return Collection<int, Colaboracion>
     */
    public function myColaboraciones(string $dni): Collection
    {
        return Colaboracion::query()
            ->MiColaboracion(null, $dni)
            ->with(['Propietario', 'Centro', 'Centro.Empresa', 'Centro.instructores', 'Ciclo', 'fcts' => function ($query): void {
                $query->orderByDesc('id')->with('Instructor');
            }])
            ->withCount('fcts')
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
            ->with(['Ciclo', 'Propietario', 'Centro', 'Centro.Empresa', 'Centro.instructores', 'fcts' => function ($query): void {
                $query->orderByDesc('id')->with('Instructor');
            }])
            ->withCount('fcts')
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

        return $this->seguimientoService
            ->groupedActivitiesForColaboraciones($colaboraciones->pluck('id')->all());
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
        $colaboracion->proximaAccioText = $this->extractStructuredLine($colaboracion->ultimaActividad?->comentari, 'Pròxim pas: ');
        $colaboracion->proximaAccioData = $this->extractStructuredLine($colaboracion->ultimaActividad?->comentari, 'Data prevista: ');

        $fcts = $colaboracion->fcts ?? collect();
        $ultimaFct = $fcts->first();
        $teDocumentEmpresa = !blank($empresa?->fichero);
        $documentacioPendent = collect();

        if (!$teDocumentEmpresa) {
            $documentacioPendent->push('Sense document d\'empresa');
        }

        if ($conveniPendent) {
            $documentacioPendent->push('Conveni o Annex I pendent');
        }

        if (!$hasInstructor) {
            $documentacioPendent->push('Falta instructor');
        }

        $criterisPreparacio = [
            !$this->isBlankText($colaboracion->contacto ?? null),
            !$this->isBlankText($colaboracion->telefono ?? null),
            !$this->isBlankText($colaboracion->email ?? null),
            $hasInstructor,
            !$conveniPendent,
        ];

        $criterisPreparats = collect($criterisPreparacio)->filter()->count();
        $estatPreparacio = $this->resolvePreparationState($criterisPreparats, count($criterisPreparacio));

        $seguiment = $this->resolveFollowUpState(
            $colaboracion->ultimaActividad,
            $colaboracion->proximaAccioText,
            $colaboracion->proximaAccioData
        );

        $colaboracion->seguimentEstatKey = $seguiment['status_key'];
        $colaboracion->seguimentEstatLabel = $seguiment['status_label'];
        $colaboracion->seguimentEstatClass = $seguiment['status_class'];
        $colaboracion->seguimentUrgenciaKey = $seguiment['urgency_key'];
        $colaboracion->seguimentUrgenciaLabel = $seguiment['urgency_label'];
        $colaboracion->seguimentUrgenciaClass = $seguiment['urgency_class'];
        $colaboracion->teProximaAccio = !empty($colaboracion->proximaAccioText);
        $colaboracion->teDocumentEmpresa = $teDocumentEmpresa;
        $colaboracion->documentacioPendentItems = $documentacioPendent;
        $colaboracion->documentacioPendentLabel = $documentacioPendent->isEmpty()
            ? 'Documentació al dia'
            : $documentacioPendent->join(', ');
        $colaboracion->fctsAssociadesCount = (int) ($colaboracion->fcts_count ?? $fcts->count());
        $colaboracion->ultimaFct = $ultimaFct;
        $colaboracion->ultimaFctId = $ultimaFct?->id;
        $colaboracion->estatPreparacioKey = $estatPreparacio['key'];
        $colaboracion->estatPreparacioLabel = $estatPreparacio['label'];
        $colaboracion->estatPreparacioClass = $estatPreparacio['class'];
        $colaboracion->estatPreparacioRank = match ($estatPreparacio['key']) {
            'no_preparada' => 2,
            'parcial' => 1,
            default => 0,
        };
        $colaboracion->documentacioPendentCount = $documentacioPendent->count();
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

    private function extractStructuredLine(?string $comment, string $prefix): ?string
    {
        foreach (preg_split("/\\r\\n|\\r|\\n/", (string) $comment) ?: [] as $line) {
            $trimmed = trim($line);
            if (str_starts_with($trimmed, $prefix)) {
                return trim(substr($trimmed, strlen($prefix)));
            }
        }

        return null;
    }

    private function annexICutoffDate(): Carbon
    {
        return Carbon::create(2024, 1, 1)->startOfDay();
    }

    /**
     * Resol l'estat global de preparació documental i operativa de la col·laboració.
     *
     * @return array{key:string,label:string,class:string}
     */
    private function resolvePreparationState(int $completedCriteria, int $totalCriteria): array
    {
        if ($completedCriteria >= $totalCriteria) {
            return [
                'key' => 'preparada',
                'label' => 'Preparada',
                'class' => 'bg-success',
            ];
        }

        if ($completedCriteria >= 3) {
            return [
                'key' => 'parcial',
                'label' => 'Parcialment preparada',
                'class' => 'bg-warning text-dark',
            ];
        }

        return [
            'key' => 'no_preparada',
            'label' => 'No preparada',
            'class' => 'bg-danger',
        ];
    }

    /**
     * @return array{
     *   status_key:string,
     *   status_label:string,
     *   status_class:string,
     *   urgency_key:string,
     *   urgency_label:?string,
     *   urgency_class:string
     * }
     */
    private function resolveFollowUpState(?Activity $ultimaActividad, ?string $proximaAccio, ?string $dataPrevista): array
    {
        $today = Carbon::today();
        $plannedDate = $this->parsePlannedDate($dataPrevista);
        $resultat = $this->normalizeResult($ultimaActividad?->document ?? '');

        $statusKey = 'sense_seguiment';
        $statusLabel = 'Sense seguiment';
        $statusClass = 'bg-secondary';

        if ($ultimaActividad !== null) {
            $statusKey = 'tancat';
            $statusLabel = 'Seguiment tancat';
            $statusClass = 'bg-success';

            if (str_contains($resultat, 'pendent de resposta')) {
                $statusKey = 'pendent_resposta';
                $statusLabel = 'Pendent de resposta';
                $statusClass = 'bg-warning text-dark';
            } elseif (!empty($proximaAccio)) {
                $statusKey = 'en_curs';
                $statusLabel = 'En curs';
                $statusClass = 'bg-info text-dark';
            } elseif (
                str_contains($resultat, 'tancat')
                || str_contains($resultat, 'seguiment fet')
                || str_contains($resultat, 'resposta rebuda')
                || str_contains($resultat, 'visita realitzada')
                || str_contains($resultat, 'reunió realitzada')
                || str_contains($resultat, 'contactat')
            ) {
                $statusKey = 'tancat';
                $statusLabel = 'Seguiment tancat';
                $statusClass = 'bg-success';
            }
        }

        $urgencyKey = 'cap';
        $urgencyLabel = null;
        $urgencyClass = 'bg-secondary';

        if ($plannedDate !== null) {
            if ($plannedDate->lt($today)) {
                $urgencyKey = 'vençut';
                $urgencyLabel = 'Vençut';
                $urgencyClass = 'bg-danger';
            } elseif ($plannedDate->lte($today->copy()->addDays(7))) {
                $urgencyKey = 'esta_setmana';
                $urgencyLabel = 'Esta setmana';
                $urgencyClass = 'bg-warning text-dark';
            }
        }

        return [
            'status_key' => $statusKey,
            'status_label' => $statusLabel,
            'status_class' => $statusClass,
            'urgency_key' => $urgencyKey,
            'urgency_label' => $urgencyLabel,
            'urgency_class' => $urgencyClass,
        ];
    }

    private function parsePlannedDate(?string $date): ?Carbon
    {
        if (trim((string) $date) === '') {
            return null;
        }

        try {
            return Carbon::parse($date)->startOfDay();
        } catch (\Throwable) {
            return null;
        }
    }

    private function normalizeResult(string $document): string
    {
        return mb_strtolower($this->extractResultFromDocument($document));
    }

    private function extractResultFromDocument(string $document): string
    {
        $parts = explode('·', $document, 2);

        return isset($parts[1]) ? trim($parts[1]) : trim($document);
    }
}
