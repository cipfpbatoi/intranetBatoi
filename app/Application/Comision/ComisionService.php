<?php

declare(strict_types=1);

namespace Intranet\Application\Comision;

use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Intranet\Domain\Comision\ComisionRepositoryInterface;
use Intranet\Entities\Comision;

/**
 * Casos d'ús d'aplicació per al domini de comissions.
 */
class ComisionService
{
    public function __construct(private readonly ComisionRepositoryInterface $comisionRepository)
    {
    }

    /**
     * @return EloquentCollection<int, Comision>
     */
    public function pendingAuthorization(): EloquentCollection
    {
        return $this->comisionRepository->pendingAuthorization();
    }

    /**
     * @return EloquentCollection<int, Comision>
     */
    public function byDay(string $dia): EloquentCollection
    {
        return $this->comisionRepository->byDay($dia);
    }

    /**
     * @return EloquentCollection<int, Comision>
     */
    public function withProfesorByDay(string $dia): EloquentCollection
    {
        return $this->comisionRepository->withProfesorByDay($dia);
    }

    /**
     * @return EloquentCollection<int, Comision>
     */
    public function authorizationApiList(): EloquentCollection
    {
        return $this->comisionRepository->authorizationApiList();
    }

    /**
     * @return EloquentCollection<int, Comision>
     */
    public function prePayByProfesor(string $dni): EloquentCollection
    {
        return $this->comisionRepository->prePayByProfesor($dni);
    }

    public function hasPendingUnpaidByProfesor(string $dni): bool
    {
        return $this->comisionRepository->hasPendingUnpaidByProfesor($dni);
    }

    public function setEstado(int $id, int $estado): Comision
    {
        return $this->comisionRepository->setEstado($id, $estado);
    }

    public function attachFct(int $comisionId, int $fctId, string $horaIni, bool $aviso): void
    {
        $this->comisionRepository->attachFct($comisionId, $fctId, $horaIni, $aviso);
    }

    public function detachFct(int $comisionId, int $fctId): void
    {
        $this->comisionRepository->detachFct($comisionId, $fctId);
    }
}
