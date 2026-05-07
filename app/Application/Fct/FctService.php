<?php

declare(strict_types=1);

namespace Intranet\Application\Fct;

use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Intranet\Application\Seguimiento\SeguimientoService;
use Intranet\Domain\Fct\FctRepositoryInterface;
use Intranet\Entities\Colaborador;
use Intranet\Entities\Fct;
use Intranet\Entities\Instructor;

/**
 * Casos d'ús d'aplicació per al domini FCT.
 */
class FctService
{
    public function __construct(
        private readonly FctRepositoryInterface $fctRepository,
        private readonly SeguimientoService $seguimientoService
    )
    {
    }

    public function find(int|string $id): ?Fct
    {
        return $this->fctRepository->find($id);
    }

    public function findOrFail(int|string $id): Fct
    {
        return $this->fctRepository->findOrFail($id);
    }

    /**
     * @return EloquentCollection<int, Fct>
     */
    public function panelListingByProfesor(string $dni): EloquentCollection
    {
        $items = $this->fctRepository->panelListingByProfesor($dni);
        $contactosByFct = $this->seguimientoService->groupedMailActivitiesForFcts($items->pluck('id')->all());

        return $items->map(function (Fct $fct) use ($contactosByFct): Fct {
            $fct->setRelation('Contactos', $contactosByFct->get((string) $fct->id, collect()));

            return $fct;
        });
    }

    /**
     * Hidrata una FCT concreta amb els contactes combinats de convivència temporal.
     */
    public function hydrateContactos(Fct $fct): Fct
    {
        $contactos = $this->seguimientoService
            ->groupedMailActivitiesForFcts([(string) $fct->id])
            ->get((string) $fct->id, collect());

        $fct->setRelation('Contactos', $contactos);
        $this->hydrateAlumnoFctContactos($fct);

        return $fct;
    }

    /**
     * Hidrata els contactes de cada AlumnoFct d'una FCT per a la vista detallada.
     */
    public function hydrateAlumnoFctContactos(Fct $fct): Fct
    {
        $alumnoFcts = $fct->relationLoaded('AlFct')
            ? $fct->AlFct
            : $fct->AlFct()->with('Alumno')->get();

        $contactosByAlumnoFct = $this->seguimientoService
            ->groupedMailActivitiesForAlumnoFcts($alumnoFcts->pluck('id')->all());

        $alumnoFcts->each(function ($alumnoFct) use ($contactosByAlumnoFct): void {
            $alumnoFct->setRelation('Contactos', $contactosByAlumnoFct->get((string) $alumnoFct->id, collect()));
        });

        $fct->setRelation('AlFct', $alumnoFcts);

        return $fct;
    }

    /**
     * Actualitza l'instructor principal d'una FCT i el vincula al centre si encara no té centre.
     */
    public function setInstructor(int|string $idFct, string $idInstructor): Fct
    {
        $fct = $this->findOrFail($idFct);
        $fct->idInstructor = $idInstructor;

        $saved = $this->fctRepository->save($fct);
        $this->associateInstructorWithCentroIfOrphan($saved);

        return $saved;
    }

    public function findBySignature(
        int|string $idColaboracion,
        int|string $asociacion,
        int|string $idInstructor
    ): ?Fct {
        return $this->fctRepository->firstByColaboracionAsociacionInstructor(
            $idColaboracion,
            $asociacion,
            $idInstructor
        );
    }

    /**
     * Crea una FCT des d'un request i vincula l'instructor orfe al centre de la col·laboració.
     */
    public function createFromRequest(Request $request): Fct
    {
        $new = new Fct();
        $new->fillAll($request);

        $fct = $new->fresh();
        $this->associateInstructorWithCentroIfOrphan($fct);

        return $fct;
    }

    /**
     * Associa als centres de les seues FCT els instructors que encara no tenen cap centre.
     *
     * @return array{instructors:int, assignments:int}
     */
    public function assignOrphanInstructorsToFctCentros(bool $dryRun = false): array
    {
        $instructors = Instructor::query()
            ->whereDoesntHave('Centros')
            ->whereHas('Fcts', function ($query): void {
                $query->whereNotNull('idColaboracion')
                    ->whereHas('Colaboracion', function ($subquery): void {
                        $subquery->whereNotNull('idCentro');
                    });
            })
            ->with(['Fcts.Colaboracion'])
            ->get();

        $assignments = 0;

        foreach ($instructors as $instructor) {
            $centroIds = $instructor->Fcts
                ->pluck('Colaboracion.idCentro')
                ->filter()
                ->unique()
                ->values()
                ->all();

            $assignments += count($centroIds);

            if (!$dryRun && $centroIds !== []) {
                $instructor->Centros()->syncWithoutDetaching($centroIds);
            }
        }

        return [
            'instructors' => $instructors->count(),
            'assignments' => $assignments,
        ];
    }

    /**
     * Associa l'instructor de la FCT al centre de treball si encara no pertany a cap centre.
     */
    private function associateInstructorWithCentroIfOrphan(?Fct $fct): void
    {
        if ($fct === null || empty($fct->idInstructor)) {
            return;
        }

        $centroId = $fct->Colaboracion?->idCentro;
        if ($centroId === null) {
            return;
        }

        $instructor = Instructor::find($fct->idInstructor);
        if ($instructor === null || $instructor->Centros()->exists()) {
            return;
        }

        $instructor->Centros()->syncWithoutDetaching($centroId);
    }

    public function attachAlumnoFromStoreRequest(Fct $fct, Request $request): void
    {
        $this->fctRepository->attachAlumno(
            (int) $fct->id,
            (string) $request->idAlumno,
            [
                'idProfesor' => (string) authUser()->dni,
                'desde' => FechaInglesa($request->desde),
                'hasta' => FechaInglesa($request->hasta),
                'horas' => $request->horas,
                'autorizacion' => $request->autorizacion ?? 0,
            ]
        );
    }

    public function attachAlumnoSimple(int|string $idFct, Request $request): void
    {
        $this->fctRepository->attachAlumno(
            $idFct,
            (string) $request->idAlumno,
            [
                'idProfesor' => (string) authUser()->dni,
                'calificacion' => 0,
                'calProyecto' => 0,
                'actas' => 0,
                'insercion' => 0,
                'desde' => FechaInglesa($request->desde),
                'hasta' => FechaInglesa($request->hasta),
                'horas' => $request->horas,
            ]
        );
    }

    public function detachAlumno(int|string $idFct, string $idAlumno): void
    {
        $this->fctRepository->detachAlumno($idFct, $idAlumno);
    }

    public function addColaborador(int|string $idFct, Colaborador $colaborador): void
    {
        $this->fctRepository->saveColaborador($idFct, $colaborador);
    }

    public function deleteColaborador(int|string $idFct, string $idInstructor): int
    {
        return $this->fctRepository->deleteColaborador($idFct, $idInstructor);
    }

    public function updateColaboradorHoras(int|string $idFct, array $horasByInstructor): void
    {
        foreach ($horasByInstructor as $dni => $horas) {
            $this->fctRepository->updateColaboradorHoras($idFct, (string) $dni, $horas);
        }
    }

    public function setCotutor(int|string $idFct, ?string $cotutor): void
    {
        $this->fctRepository->setCotutor($idFct, $cotutor);
    }

    public function empresaIdByFct(int|string $idFct): ?int
    {
        return $this->fctRepository->empresaIdByFct($idFct);
    }

    public function deleteFct(int|string $idFct): void
    {
        DB::transaction(function () use ($idFct): void {
            $fct = $this->findOrFail($idFct);
            $fct->delete();
        });
    }
}
