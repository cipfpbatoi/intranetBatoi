<?php

declare(strict_types=1);

namespace Intranet\Infrastructure\Persistence\Eloquent\Expediente;

use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Http\Request;
use Intranet\Domain\Expediente\ExpedienteRepositoryInterface;
use Intranet\Entities\Expediente;
use Intranet\Entities\TipoExpediente;

/**
 * Implementació Eloquent del repositori d'expedients.
 */
class EloquentExpedienteRepository implements ExpedienteRepositoryInterface
{
    public function find(int|string $id): ?Expediente
    {
        return Expediente::find($id);
    }

    public function findOrFail(int|string $id): Expediente
    {
        return Expediente::findOrFail($id);
    }

    public function createFromRequest(Request $request): Expediente
    {
        $expediente = new Expediente();
        $expediente->fillAll($request);

        return $expediente->fresh();
    }

    public function updateFromRequest(int|string $id, Request $request): Expediente
    {
        $expediente = $this->findOrFail($id);
        $expediente->fillAll($request);

        return $expediente->fresh();
    }

    public function pendingAuthorization(): EloquentCollection
    {
        return Expediente::where('estado', 1)->get();
    }

    public function readyToPrint(): EloquentCollection
    {
        return Expediente::listos()->get();
    }

    public function allTypes(): EloquentCollection
    {
        return TipoExpediente::all();
    }
}

