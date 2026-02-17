<?php

declare(strict_types=1);

namespace Intranet\Application\Expediente;

use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Http\Request;
use Intranet\Domain\Expediente\ExpedienteRepositoryInterface;
use Intranet\Entities\Expediente;
use Intranet\Entities\TipoExpediente;

/**
 * Casos d'ús d'aplicació per al domini d'expedients.
 */
class ExpedienteService
{
    public function __construct(private readonly ExpedienteRepositoryInterface $expedienteRepository)
    {
    }

    public function find(int|string $id): ?Expediente
    {
        return $this->expedienteRepository->find($id);
    }

    public function findOrFail(int|string $id): Expediente
    {
        return $this->expedienteRepository->findOrFail($id);
    }

    public function createFromRequest(Request $request): Expediente
    {
        return $this->expedienteRepository->createFromRequest($request);
    }

    public function updateFromRequest(int|string $id, Request $request): Expediente
    {
        return $this->expedienteRepository->updateFromRequest($id, $request);
    }

    /**
     * @return EloquentCollection<int, Expediente>
     */
    public function pendingAuthorization(): EloquentCollection
    {
        return $this->expedienteRepository->pendingAuthorization();
    }

    /**
     * @return EloquentCollection<int, Expediente>
     */
    public function readyToPrint(): EloquentCollection
    {
        return $this->expedienteRepository->readyToPrint();
    }

    /**
     * @return EloquentCollection<int, TipoExpediente>
     */
    public function allTypes(): EloquentCollection
    {
        return $this->expedienteRepository->allTypes();
    }
}

