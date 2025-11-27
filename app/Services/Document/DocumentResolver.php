<?php

namespace Intranet\Services\Document;

use Intranet\Entities\Documento;

class DocumentResolver
{
    public function resolve($elemento = null, $documento = null): DocumentContext
    {
        $document = $documento ?? $this->findDocument($elemento);
        $link = null;
        $isFile = false;

        if ($document) {
            if (isset($document->enlace)) {
                $link = $document->enlace;
                $isFile = false;
            }

            if (isset($document->fichero)) {
                $link = storage_path('app/' . $document->fichero);
                $isFile = true;
            }

            return new DocumentContext($document, $link, $isFile);
        }

        [$link, $isFile] = $this->getFileIfExistFromModel($elemento);

        return new DocumentContext(null, $link, $isFile);
    }

    private function findDocument($elemento): ?Documento
    {
        if (!isset($elemento)) {
            return null;
        }

        if ($elemento->idDocumento) {
            return Documento::find($elemento->idDocumento);
        }

        return isset($elemento->fichero)
            ? Documento::where('fichero', $elemento->fichero)->first()
            : null;
    }

    private function getFileIfExistFromModel($elemento): array
    {
        if (isset($elemento->fichero)) {
            return [storage_path('app/' . $elemento->fichero), true];
        }

        return [null, false];
    }
}
