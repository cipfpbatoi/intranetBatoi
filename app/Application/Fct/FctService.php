<?php

declare(strict_types=1);

namespace Intranet\Application\Fct;

use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Intranet\Domain\Fct\FctRepositoryInterface;
use Intranet\Entities\Colaborador;
use Intranet\Entities\Fct;

/**
 * Casos d'ús d'aplicació per al domini FCT.
 */
class FctService
{
    public function __construct(private readonly FctRepositoryInterface $fctRepository)
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
        return $this->fctRepository->panelListingByProfesor($dni);
    }

    public function setInstructor(int|string $idFct, string $idInstructor): Fct
    {
        $fct = $this->findOrFail($idFct);
        $fct->idInstructor = $idInstructor;

        return $this->fctRepository->save($fct);
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

    public function createFromRequest(Request $request): Fct
    {
        $new = new Fct();
        $new->fillAll($request);

        return $new->fresh();
    }

    public function attachAlumnoFromStoreRequest(Fct $fct, Request $request): void
    {
        $this->fctRepository->attachAlumno(
            (int) $fct->id,
            (string) $request->idAlumno,
            [
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
