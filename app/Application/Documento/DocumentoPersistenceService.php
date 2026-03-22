<?php

declare(strict_types=1);

namespace Intranet\Application\Documento;

use Illuminate\Http\Request;
use Intranet\Entities\Documento;
use Intranet\Services\Document\CreateOrUpdateDocumentAction;
use Intranet\Services\Document\TipoDocumentoService;

/**
 * Encapsula la persistència documental comuna fora dels controladors de domini.
 */
class DocumentoPersistenceService
{
    /**
     * Crea un document a partir d'un request HTTP.
     */
    public function storeFromRequest(Request $request): Documento
    {
        return $this->persist($request);
    }

    /**
     * Actualitza un document existent a partir d'un request HTTP.
     */
    public function updateFromRequest(Request $request, Documento $document): Documento
    {
        return $this->persist($request, $document);
    }

    /**
     * Normalitza i persistix un document, resolent defaults comuns.
     */
    private function persist(Request $request, ?Documento $document = null): Documento
    {
        $rol = TipoDocumentoService::rol($request->input('tipoDocumento'));
        $cursoRequest = $request->input('curso') ?? curso();
        $cleanRequest = $request->duplicate(
            $request->except(['nota']),
            $request->files->all()
        );

        return (new CreateOrUpdateDocumentAction())->fromRequest(
            $cleanRequest,
            [
                'rol' => $rol,
                'curso' => $cursoRequest,
            ],
            $document
        );
    }
}
