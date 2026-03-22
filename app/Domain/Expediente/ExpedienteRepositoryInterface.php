<?php

declare(strict_types=1);

namespace Intranet\Domain\Expediente;

use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Http\Request;
use Intranet\Entities\Expediente;
use Intranet\Entities\TipoExpediente;

/**
 * Contracte de persistència per al domini d'expedients.
 */
interface ExpedienteRepositoryInterface
{
    public function find(int|string $id): ?Expediente;

    public function findOrFail(int|string $id): Expediente;

    public function createFromRequest(Request $request): Expediente;

    public function updateFromRequest(int|string $id, Request $request): Expediente;

    /**
     * @return EloquentCollection<int, Expediente>
     */
    public function pendingAuthorization(): EloquentCollection;

    /**
     * @return EloquentCollection<int, Expediente>
     */
    public function readyToPrint(): EloquentCollection;

    /**
     * @return EloquentCollection<int, TipoExpediente>
     */
    public function allTypes(): EloquentCollection;
}

