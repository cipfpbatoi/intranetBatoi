<?php

declare(strict_types=1);

namespace Intranet\Application\Documento;

use Intranet\Entities\Documento;

/**
 * Servei de cicle de vida per a Documento.
 */
class DocumentoLifecycleService
{
    /**
     * Esborra un document i, si aplica, també el fitxer físic associat.
     */
    public function delete(Documento $documento): bool
    {
        if ($this->mustDeleteFile($documento)) {
            $path = storage_path('app/' . $documento->fichero);
            if (is_file($path)) {
                @unlink($path);
            }
        }

        return (bool) $documento->delete();
    }

    private function mustDeleteFile(Documento $documento): bool
    {
        return $documento->link && !$documento->exist;
    }
}

