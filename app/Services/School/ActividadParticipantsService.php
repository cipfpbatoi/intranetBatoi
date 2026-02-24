<?php

namespace Intranet\Services\School;

use Illuminate\Support\Facades\DB;
use Intranet\Application\Grupo\GrupoService;
use Intranet\Entities\Actividad;
use Intranet\Entities\ActividadProfesor;

/**
 * Gestiona participants i coordinació d'activitats.
 *
 * Encapsula la lògica de:
 * - altes/baixes de grups i professorat al pivot,
 * - garantia de coordinador únic en canvis de responsable,
 * - reassignació de coordinador en baixa del responsable actual.
 */
class ActividadParticipantsService
{
    /**
     * Assigna coordinador i grup per defecte en crear l'activitat.
     *
     * @param Actividad $actividad
     * @param string|null $dni
     * @return void
     */
    public function assignInitialParticipants(Actividad $actividad, ?string $dni = null): void
    {
        $dni = $dni ?? (authUser()?->dni);

        if (!$dni) {
            return;
        }

        $actividad->profesores()->syncWithoutDetaching([
            $dni => ['coordinador' => 1],
        ]);

        $grupo = app(GrupoService::class)->largestByTutor((string) $dni);
        if ($grupo) {
            $actividad->grupos()->syncWithoutDetaching([$grupo->codigo]);
        }
    }

    /**
     * Afig un grup sense desassignar els existents.
     *
     * @param int|string $actividadId
     * @param string $groupId
     * @return void
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function addGroup(int|string $actividadId, string $groupId): void
    {
        $actividad = Actividad::findOrFail($actividadId);
        $actividad->grupos()->syncWithoutDetaching([$groupId]);
    }

    /**
     * Esborra un grup del pivot.
     *
     * @param int|string $actividadId
     * @param string $groupId
     * @return void
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function removeGroup(int|string $actividadId, string $groupId): void
    {
        $actividad = Actividad::findOrFail($actividadId);
        $actividad->grupos()->detach($groupId);
    }

    /**
     * Afig un professor sense duplicar pivots.
     *
     * @param int|string $actividadId
     * @param string $profesorId
     * @return void
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function addProfesor(int|string $actividadId, string $profesorId): void
    {
        $actividad = Actividad::findOrFail($actividadId);
        $actividad->profesores()->syncWithoutDetaching([$profesorId]);
    }

    /**
     * Esborra un professor i, si era coordinador, en reassigna un de nou.
     *
     * @param int|string $actividadId
     * @param string $profesorId
     * @return bool `false` quan només quedava un professor i no es pot esborrar.
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function removeProfesor(int|string $actividadId, string $profesorId): bool
    {
        $actividad = Actividad::findOrFail($actividadId);

        if ($actividad->profesores()->count() === 1) {
            return false;
        }

        $eraCoordinador = $actividad->profesores()
            ->where('dni', $profesorId)
            ->wherePivot('coordinador', 1)
            ->exists();

        DB::transaction(function () use ($actividad, $actividadId, $profesorId, $eraCoordinador): void {
            $actividad->profesores()->detach($profesorId);

            if (!$eraCoordinador) {
                return;
            }

            ActividadProfesor::where('idActividad', $actividadId)->update(['coordinador' => 0]);
            $nuevoCoordinador = $actividad->profesores()->first();

            if ($nuevoCoordinador) {
                $actividad->profesores()->updateExistingPivot($nuevoCoordinador->dni, ['coordinador' => 1]);
            }
        });

        return true;
    }

    /**
     * Marca un únic coordinador per a l'activitat.
     *
     * @param int|string $actividadId
     * @param string $profesorId
     * @return bool `false` si el professor no participa en l'activitat.
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function assignCoordinator(int|string $actividadId, string $profesorId): bool
    {
        $actividad = Actividad::findOrFail($actividadId);

        if (!$actividad->profesores()->where('dni', $profesorId)->exists()) {
            return false;
        }

        DB::transaction(function () use ($actividad, $actividadId, $profesorId): void {
            ActividadProfesor::where('idActividad', $actividadId)->update(['coordinador' => 0]);
            $actividad->profesores()->updateExistingPivot($profesorId, ['coordinador' => 1]);
        });

        return true;
    }
}
