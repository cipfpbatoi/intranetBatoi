<?php

declare(strict_types=1);

namespace Intranet\Http\Controllers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Intranet\Entities\AssumpteParticular;
use Intranet\Exceptions\NotFoundDomainException;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Descàrrega protegida de la resolució firmada d'un assumpte particular.
 */
class AssumpteParticularDocumentController extends Controller
{
    /**
     * Retorna el PDF firmat al professor propietari, Direcció o Administració.
     */
    public function __invoke(AssumpteParticular $assumpteParticular): StreamedResponse
    {
        Gate::authorize('view', $assumpteParticular);

        $document = $assumpteParticular->resolucio_document;
        if (blank($document) || !Storage::disk('local')->exists($document)) {
            throw new NotFoundDomainException('La resolució firmada no està disponible.', [
                'assumpte_particular_id' => $assumpteParticular->getKey(),
                'path' => $document,
            ]);
        }

        return Storage::disk('local')->download(
            $document,
            sprintf('resolucio-assumpte-particular-%d.pdf', $assumpteParticular->getKey())
        );
    }
}
