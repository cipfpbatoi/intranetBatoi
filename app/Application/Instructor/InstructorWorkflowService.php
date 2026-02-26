<?php

declare(strict_types=1);

namespace Intranet\Application\Instructor;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Intranet\Entities\Centro;
use Intranet\Entities\Fct;
use Intranet\Entities\Instructor;
use Illuminate\Support\Carbon;

class InstructorWorkflowService
{
    public function searchForTutorFcts(): Collection
    {
        $instructores = [];

        foreach (Fct::misFcts()->get() as $fct) {
            foreach ($fct->Colaboracion->Centro->Instructores ?? [] as $instructor) {
                $instructores[] = $instructor->dni ?? '';
            }
        }

        return Instructor::whereIn('dni', $instructores)->get();
    }

    public function empresaIdFromInstructor(int|string $id): ?int
    {
        return Instructor::find($id)?->Centros?->first()?->idEmpresa;
    }

    public function upsertAndAttachToCentro(object $request, int|string $centro, callable $createInstructor): int
    {
        DB::transaction(function () use ($request, $centro, $createInstructor): void {
            $instructor = Instructor::find($request->dni);
            if (!$instructor) {
                if (!$request->dni) {
                    $max = Instructor::where('dni', '>', 'EU0000000')
                        ->where('dni', '<', 'EU9999999')
                        ->max('dni');
                    $max = (int) substr((string) $max, 2) + 1;
                    $dni = 'EU' . str_pad((string) $max, 7, '0', STR_PAD_LEFT);
                    $request->merge(['dni' => $dni]);
                }
                $createInstructor($request);
            }

            $instructor = Instructor::find($request->dni);
            $instructor?->Centros()->syncWithoutDetaching($centro);
        });

        return (int) Centro::findOrFail($centro)->idEmpresa;
    }

    public function detachFromCentroAndDeleteIfOrphan(int|string $id, int|string $centro, callable $deleteInstructor): int
    {
        $instructor = Instructor::findOrFail($id);
        $instructor->Centros()->detach($centro);
        if ($instructor->Centros()->count() == 0) {
            try {
                $deleteInstructor($id);
            } catch (\Exception) {
            }
        }

        return (int) Centro::findOrFail($centro)->idEmpresa;
    }

    public function copyInstructorToCentro(int|string $id, int|string $sourceCentro, int|string $targetCentro, string $action): int
    {
        $instructor = Instructor::findOrFail($id);
        $instructor->Centros()->attach($targetCentro);
        if ($action === 'mou') {
            $instructor->Centros()->detach($sourceCentro);
        }

        return (int) Centro::findOrFail($sourceCentro)->idEmpresa;
    }

    public function ultimaFecha(Collection|array|null $fcts): ?Carbon
    {
        $fcts = $fcts instanceof Collection ? $fcts : collect($fcts);
        $conHasta = $fcts->filter(fn ($fct) => !empty($fct->hasta));

        if ($conHasta->isEmpty()) {
            return null;
        }

        $posterior = new Carbon($conHasta->first()->hasta);
        foreach ($conHasta as $fct) {
            $posterior = FechaPosterior($fct->hasta, $posterior);
        }

        return $posterior;
    }
}
