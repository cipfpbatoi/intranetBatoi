<?php

declare(strict_types=1);

namespace Intranet\Application\Seguimiento;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Intranet\Entities\Activity;
use Intranet\Entities\Seguimiento;

/**
 * Servei d'aplicació per a registrar seguiments de domini.
 *
 * En esta fase manté només la nova escriptura estructurada. La lectura
 * legacy continua convivint amb `activities` fins que es complete el
 * desacoblament dels panells.
 */
class SeguimientoService
{
    /**
     * Registra un seguiment estructurat per a un model de domini.
     *
     * @param Model $model Agregat arrel afectat (`Colaboracion`, `Fct`, `AlumnoFct`, ...).
     * @param string $contactType Tipus normalitzat (`phone`, `email`, `review`, ...).
     * @param string $title Títol curt o resultat visible del contacte.
     * @param string|null $comment Comentari llarg opcional.
     * @param array<string, mixed>|null $meta Metadades transitòries de convivència.
     * @return Seguimiento
     */
    public function record(
        Model $model,
        string $contactType,
        string $title,
        ?string $comment = null,
        ?array $meta = null
    ): Seguimiento {
        $seguimiento = new Seguimiento([
            'domain_type' => class_basename($model),
            'domain_id' => (string) $model->getKey(),
            'contact_type' => $contactType,
            'title' => $title,
            'comment' => $comment,
            'author_id' => auth()->user()?->dni,
            'contacted_at' => now(),
            'meta' => $meta,
        ]);

        $seguimiento->save();

        return $seguimiento;
    }

    /**
     * Busca el seguiment mirall associat a una activitat legacy.
     */
    public function findByActivityId(int|string $activityId): ?Seguimiento
    {
        return Seguimiento::query()
            ->where('meta->activity_id', (string) $activityId)
            ->first();
    }

    /**
     * Sincronitza o crea el mirall estructurat a partir d'una activitat legacy.
     */
    public function syncFromActivity(Activity $activity): Seguimiento
    {
        $seguimiento = $this->findByActivityId((string) $activity->id)
            ?? new Seguimiento();

        $seguimiento->domain_type = class_basename((string) $activity->model_class);
        $seguimiento->domain_id = (string) $activity->model_id;
        $seguimiento->contact_type = (string) $activity->action;
        $seguimiento->title = (string) $activity->document;
        $seguimiento->comment = $activity->comentari;
        $seguimiento->author_id = $activity->author_id;
        $seguimiento->contacted_at = $activity->created_at ?? now();
        $seguimiento->meta = array_merge($seguimiento->meta ?? [], [
            'source' => 'activities',
            'activity_id' => (string) $activity->id,
        ]);

        $seguimiento->save();

        return $seguimiento;
    }

    /**
     * Retorna els seguiments d'un conjunt de col·laboracions combinant nova persistència i històric legacy.
     *
     * @param array<int, int|string> $colaboracionIds
     * @return Collection<string, Collection<int, Activity>>
     */
    public function groupedActivitiesForColaboraciones(array $colaboracionIds): Collection
    {
        $ids = collect($colaboracionIds)
            ->map(static fn ($id): string => (string) $id)
            ->filter()
            ->values();

        if ($ids->isEmpty()) {
            return collect();
        }

        $legacyByModel = Activity::query()
            ->modelo('Colaboracion')
            ->notUpdate()
            ->whereIn('model_id', $ids->all())
            ->orderBy('created_at')
            ->get()
            ->groupBy(static fn (Activity $activity): string => (string) $activity->model_id);

        $seguimientosByModel = Seguimiento::query()
            ->where('domain_type', 'Colaboracion')
            ->whereIn('domain_id', $ids->all())
            ->orderBy('contacted_at')
            ->get()
            ->groupBy(static fn (Seguimiento $seguimiento): string => (string) $seguimiento->domain_id);

        return $ids->mapWithKeys(function (string $id) use ($legacyByModel, $seguimientosByModel): array {
            $legacy = $legacyByModel->get($id, collect());
            $seguimientos = $seguimientosByModel->get($id, collect());
            $mirroredActivityIds = $seguimientos
                ->pluck('meta')
                ->filter()
                ->map(static fn (array $meta): ?string => isset($meta['activity_id']) ? (string) $meta['activity_id'] : null)
                ->filter()
                ->values();

            $legacyOnly = $legacy
                ->reject(static fn (Activity $activity): bool => $mirroredActivityIds->contains((string) $activity->id))
                ->values();

            $merged = $legacyOnly
                ->concat($seguimientos->map(fn (Seguimiento $seguimiento): Activity => $this->asActivity($seguimiento)))
                ->sortBy(static fn (Activity $activity) => strtotime((string) $activity->created_at))
                ->values();

            return [$id => $merged];
        });
    }

    /**
     * Retorna els contactes "de correu/telefonada/visita/revisió" d'un conjunt de FCT.
     *
     * @param array<int, int|string> $fctIds
     * @return Collection<string, Collection<int, Activity>>
     */
    public function groupedMailActivitiesForFcts(array $fctIds): Collection
    {
        return $this->groupedMailActivitiesForDomain('Fct', $fctIds);
    }

    /**
     * Retorna els contactes tipus mail/revisió d'un conjunt d'`AlumnoFct`.
     *
     * @param array<int, int|string> $alumnoFctIds
     * @return Collection<string, Collection<int, Activity>>
     */
    public function groupedMailActivitiesForAlumnoFcts(array $alumnoFctIds): Collection
    {
        return $this->groupedMailActivitiesForDomain('AlumnoFct', $alumnoFctIds);
    }

    /**
     * Retorna l'últim contacte visible d'una col·laboració.
     */
    public function latestActivityForColaboracion(int|string $colaboracionId): ?Activity
    {
        return $this->groupedActivitiesForColaboraciones([(string) $colaboracionId])
            ->get((string) $colaboracionId, collect())
            ->last();
    }

    /**
     * Construeix l'anotació agregada per a un tipus concret de contacte en una col·laboració.
     */
    public function commentLogForColaboracion(int|string $colaboracionId, string $contactType): string
    {
        return $this->groupedActivitiesForColaboraciones([(string) $colaboracionId])
            ->get((string) $colaboracionId, collect())
            ->filter(static fn (Activity $activity): bool => $activity->action === $contactType)
            ->pluck('comentari')
            ->filter()
            ->implode("\n");
    }

    /**
     * Adapta un seguiment de domini al format Activity legacy per a la capa de presentació actual.
     */
    private function asActivity(Seguimiento $seguimiento): Activity
    {
        $activityId = (string) ($seguimiento->meta['activity_id'] ?? ('seguimiento-' . $seguimiento->id));

        return tap(new Activity(), function (Activity $activity) use ($seguimiento, $activityId): void {
            $activity->id = $activityId;
            $activity->action = $seguimiento->contact_type;
            $activity->model_class = 'Intranet\\Entities\\' . $seguimiento->domain_type;
            $activity->model_id = $seguimiento->domain_id;
            $activity->author_id = $seguimiento->author_id;
            $activity->document = $seguimiento->title;
            $activity->comentari = $seguimiento->comment;
            $activity->created_at = $seguimiento->contacted_at;
            $activity->updated_at = $seguimiento->updated_at;
        });
    }

    /**
     * Retorna contactes tipus mail legacy combinats amb seguiments del domini indicat.
     *
     * @param string $domainType
     * @param array<int, int|string> $domainIds
     * @return Collection<string, Collection<int, Activity>>
     */
    private function groupedMailActivitiesForDomain(string $domainType, array $domainIds): Collection
    {
        $ids = collect($domainIds)
            ->map(static fn ($id): string => (string) $id)
            ->filter()
            ->values();

        if ($ids->isEmpty()) {
            return collect();
        }

        $legacyByModel = Activity::query()
            ->modelo($domainType)
            ->mail()
            ->whereIn('model_id', $ids->all())
            ->orderBy('created_at')
            ->get()
            ->groupBy(static fn (Activity $activity): string => (string) $activity->model_id);

        $seguimientosByModel = Seguimiento::query()
            ->where('domain_type', $domainType)
            ->whereIn('domain_id', $ids->all())
            ->whereIn('contact_type', ['email', 'phone', 'visita', 'review'])
            ->orderBy('contacted_at')
            ->get()
            ->groupBy(static fn (Seguimiento $seguimiento): string => (string) $seguimiento->domain_id);

        return $ids->mapWithKeys(function (string $id) use ($legacyByModel, $seguimientosByModel): array {
            $legacy = $legacyByModel->get($id, collect());
            $seguimientos = $seguimientosByModel->get($id, collect());
            $mirroredActivityIds = $seguimientos
                ->pluck('meta')
                ->filter()
                ->map(static fn (array $meta): ?string => isset($meta['activity_id']) ? (string) $meta['activity_id'] : null)
                ->filter()
                ->values();

            $legacyOnly = $legacy
                ->reject(static fn (Activity $activity): bool => $mirroredActivityIds->contains((string) $activity->id))
                ->values();

            $merged = $legacyOnly
                ->concat($seguimientos->map(fn (Seguimiento $seguimiento): Activity => $this->asActivity($seguimiento)))
                ->sortBy(static fn (Activity $activity) => strtotime((string) $activity->created_at))
                ->values();

            return [$id => $merged];
        });
    }
}
