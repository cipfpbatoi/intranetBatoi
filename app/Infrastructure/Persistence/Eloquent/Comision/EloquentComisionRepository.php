<?php

declare(strict_types=1);

namespace Intranet\Infrastructure\Persistence\Eloquent\Comision;

use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Facades\DB;
use Intranet\Domain\Comision\ComisionRepositoryInterface;
use Intranet\Entities\Comision;

class EloquentComisionRepository implements ComisionRepositoryInterface
{
    public function find(int $id): ?Comision
    {
        return Comision::find($id);
    }

    public function findOrFail(int $id): Comision
    {
        return Comision::findOrFail($id);
    }

    public function byDay(string $dia): EloquentCollection
    {
        return Comision::Dia($dia)->get();
    }

    public function withProfesorByDay(string $dia): EloquentCollection
    {
        return Comision::with('profesor')->Dia($dia)->get();
    }

    public function pendingAuthorization(): EloquentCollection
    {
        return Comision::where('estado', 1)->get();
    }

    public function authorizationApiList(): EloquentCollection
    {
        $items = Comision::with('Profesor')
            ->whereIn('estado', [1, 2])
            ->whereNull('deleted_at')
            ->get();

        $items->each(function (Comision $comision): void {
            $profesor = $comision->Profesor;
            $comision->nombre = $profesor
                ? trim("{$profesor->apellido1} {$profesor->apellido2},{$profesor->nombre}")
                : '';
        });

        return $items;
    }

    public function authorizeAllPending(): int
    {
        return Comision::where('estado', 1)->update(['estado' => 2]);
    }

    public function prePayByProfesor(string $dni): EloquentCollection
    {
        return DB::transaction(function () use ($dni): EloquentCollection {
            $data = Comision::where('idProfesor', $dni)
                ->where('estado', 4)
                ->get();

            foreach ($data as $item) {
                $item->estado = 6;
                $item->save();
            }

            return $data->fresh();
        });
    }

    public function setEstado(int $id, int $estado): Comision
    {
        $comision = $this->findOrFail($id);
        $comision->estado = $estado;
        $comision->save();

        return $comision->fresh();
    }

    public function hasPendingUnpaidByProfesor(string $dni): bool
    {
        return Comision::where('idProfesor', $dni)
            ->where('estado', '<', 4)
            ->where('estado', '>', 0)
            ->where(function ($query): void {
                $query->where('comida', '>', 0)
                    ->orWhere('gastos', '>', 0)
                    ->orWhere('alojamiento', '>', 0)
                    ->orWhere('kilometraje', '>', 0);
            })
            ->exists();
    }

    public function attachFct(int $comisionId, int $fctId, string $horaIni, bool $aviso): void
    {
        $comision = $this->findOrFail($comisionId);
        $comision->fcts()->syncWithoutDetaching([
            $fctId => [
                'hora_ini' => $horaIni,
                'aviso' => $aviso ? 1 : 0,
            ],
        ]);
    }

    public function detachFct(int $comisionId, int $fctId): void
    {
        $comision = $this->findOrFail($comisionId);
        $comision->fcts()->detach($fctId);
    }
}
