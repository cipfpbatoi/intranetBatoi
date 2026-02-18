<?php

declare(strict_types=1);

namespace Intranet\Domain\FaltaProfesor;

use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Intranet\Entities\Falta_profesor;

/**
 * Contracte de persistència per al domini de fitxatges de professorat.
 */
interface FaltaProfesorRepositoryInterface
{
    public function lastTodayByProfesor(string $dni): ?Falta_profesor;

    public function hasFichadoOnDay(string $dia, string $dni): bool;

    public function createEntry(string $dni, string $dia, string $hora): Falta_profesor;

    public function closeExit(Falta_profesor $fichaje, string $hora): Falta_profesor;

    /**
     * @return EloquentCollection<int, Falta_profesor>
     */
    public function rangeByProfesor(string $dni, string $desde, string $hasta): EloquentCollection;
}

