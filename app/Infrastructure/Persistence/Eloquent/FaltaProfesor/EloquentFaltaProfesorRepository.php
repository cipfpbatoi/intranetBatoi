<?php

declare(strict_types=1);

namespace Intranet\Infrastructure\Persistence\Eloquent\FaltaProfesor;

use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Intranet\Domain\FaltaProfesor\FaltaProfesorRepositoryInterface;
use Intranet\Entities\Falta_profesor;

/**
 * Implementació Eloquent del repositori de fitxatges de professorat.
 */
class EloquentFaltaProfesorRepository implements FaltaProfesorRepositoryInterface
{
    public function lastTodayByProfesor(string $dni): ?Falta_profesor
    {
        return Falta_profesor::query()
            ->where('dia', date('Y-m-d'))
            ->where('idProfesor', $dni)
            ->orderByDesc('id')
            ->first();
    }

    public function hasFichadoOnDay(string $dia, string $dni): bool
    {
        return Falta_profesor::query()
            ->where('dia', $dia)
            ->where('idProfesor', $dni)
            ->exists();
    }

    public function createEntry(string $dni, string $dia, string $hora): Falta_profesor
    {
        return Falta_profesor::create([
            'idProfesor' => $dni,
            'dia' => $dia,
            'entrada' => $hora,
        ]);
    }

    public function closeExit(Falta_profesor $fichaje, string $hora): Falta_profesor
    {
        $fichaje->salida = $hora;
        $fichaje->save();

        return $fichaje;
    }

    public function rangeByProfesor(string $dni, string $desde, string $hasta): EloquentCollection
    {
        return Falta_profesor::query()
            ->where('dia', '>=', $desde)
            ->where('dia', '<=', $hasta)
            ->where('idProfesor', $dni)
            ->get();
    }
}

