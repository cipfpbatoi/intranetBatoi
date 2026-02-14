<?php

declare(strict_types=1);

namespace Intranet\Domain\Comision;

use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Intranet\Entities\Comision;

/**
 * Contracte de persistència per al domini de Comissió.
 *
 * Aquest contracte està definit segons els casos d'ús actuals del projecte
 * (controllers, serveis i comandos) per facilitar una migració incremental.
 */
interface ComisionRepositoryInterface
{
    public function find(int $id): ?Comision;

    public function findOrFail(int $id): Comision;

    /**
     * @return EloquentCollection<int, Comision>
     */
    public function byDay(string $dia): EloquentCollection;

    /**
     * @return EloquentCollection<int, Comision>
     */
    public function withProfesorByDay(string $dia): EloquentCollection;

    /**
     * @return EloquentCollection<int, Comision>
     */
    public function pendingAuthorization(): EloquentCollection;

    /**
     * Llistat per a l'API d'autorització (inclou nom concatenat de professor/a).
     *
     * @return EloquentCollection<int, Comision>
     */
    public function authorizationApiList(): EloquentCollection;

    public function authorizeAllPending(): int;

    /**
     * @return EloquentCollection<int, Comision>
     */
    public function prePayByProfesor(string $dni): EloquentCollection;

    public function setEstado(int $id, int $estado): Comision;

    public function hasPendingUnpaidByProfesor(string $dni): bool;

    public function attachFct(int $comisionId, int $fctId, string $horaIni, bool $aviso): void;

    public function detachFct(int $comisionId, int $fctId): void;
}
