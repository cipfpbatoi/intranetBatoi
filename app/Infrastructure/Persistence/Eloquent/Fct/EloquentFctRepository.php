<?php

declare(strict_types=1);

namespace Intranet\Infrastructure\Persistence\Eloquent\Fct;

use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Intranet\Domain\Fct\FctRepositoryInterface;
use Intranet\Entities\Colaborador;
use Intranet\Entities\Fct;

/**
 * Implementació Eloquent del repositori FCT.
 */
class EloquentFctRepository implements FctRepositoryInterface
{
    public function find(int|string $id): ?Fct
    {
        return Fct::find($id);
    }

    public function findOrFail(int|string $id): Fct
    {
        return Fct::findOrFail($id);
    }

    public function firstByColaboracionAsociacionInstructor(
        int|string $idColaboracion,
        int|string $asociacion,
        int|string $idInstructor
    ): ?Fct {
        return Fct::where('idColaboracion', $idColaboracion)
            ->where('asociacion', $asociacion)
            ->where('idInstructor', $idInstructor)
            ->first();
    }

    public function panelListingByProfesor(string $dni): EloquentCollection
    {
        /** @var EloquentCollection<int, Fct> $items */
        $items = Fct::where(function ($query): void {
            $query->esFct()->orWhere->esDual();
        })
            ->where(function ($query) use ($dni): void {
                $query->misFcts($dni)->orWhere('cotutor', $dni);
            })
            ->has('AlFct')
            ->get();

        return $items->sortBy('centro')->values();
    }

    public function save(Fct $fct): Fct
    {
        $fct->save();
        return $fct->fresh();
    }

    public function create(array $attributes): Fct
    {
        $fct = new Fct($attributes);
        $fct->save();
        return $fct->fresh();
    }

    public function attachAlumno(int|string $idFct, string $idAlumno, array $pivotAttributes): void
    {
        $fct = $this->findOrFail($idFct);
        $fct->Alumnos()->attach($idAlumno, $pivotAttributes);
    }

    public function detachAlumno(int|string $idFct, string $idAlumno): void
    {
        $fct = $this->findOrFail($idFct);
        $fct->Alumnos()->detach($idAlumno);
    }

    public function saveColaborador(int|string $idFct, Colaborador $colaborador): void
    {
        $fct = $this->findOrFail($idFct);
        $fct->Colaboradores()->save($colaborador);
    }

    public function deleteColaborador(int|string $idFct, string $idInstructor): int
    {
        return Colaborador::where('idFct', $idFct)
            ->where('idInstructor', $idInstructor)
            ->delete();
    }

    public function updateColaboradorHoras(int|string $idFct, string $idInstructor, int|string $horas): int
    {
        $fct = $this->findOrFail($idFct);
        return $fct->Colaboradores()
            ->where('idInstructor', $idInstructor)
            ->update(['horas' => $horas]);
    }

    public function setCotutor(int|string $idFct, ?string $cotutor): void
    {
        DB::transaction(function () use ($idFct, $cotutor): void {
            Schema::disableForeignKeyConstraints();

            $fct = $this->find($idFct);
            if ($fct) {
                $fct->cotutor = $cotutor;
                $fct->save();
            }

            Schema::enableForeignKeyConstraints();
        });
    }

    public function empresaIdByFct(int|string $idFct): ?int
    {
        $fct = $this->find($idFct);
        return $fct?->Colaboracion?->Centro?->idEmpresa;
    }
}

