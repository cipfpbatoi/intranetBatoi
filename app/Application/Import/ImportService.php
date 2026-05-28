<?php

declare(strict_types=1);

namespace Intranet\Application\Import;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Intranet\Services\UI\AppAlert as Alert;

/**
 * Servei d'aplicació per a operacions comunes d'importació.
 */
class ImportService
{
    /**
     * Valida i retorna el fitxer XML d'importació.
     */
    public function resolveXmlFile(Request $request, string $field = 'fichero'): ?UploadedFile
    {
        if (!$request->hasFile($field) || !file_exists((string) $request->file($field))) {
            Alert::danger(__('messages.generic.noFile'));
            return null;
        }

        $file = $request->file($field);
        if (!$file instanceof UploadedFile) {
            Alert::danger(__('messages.generic.noFile'));
            return null;
        }

        $extension = strtolower((string) $file->getClientOriginalExtension());
        if (!$file->isValid() || $extension !== 'xml') {
            Alert::danger(__('messages.generic.invalidFormat'));
            return null;
        }

        return $file;
    }

    /**
     * Executa una importació amb timeout ampliat.
     *
     * @param callable(UploadedFile, Request): void $runner
     */
    public function runWithExtendedTimeout(callable $runner, UploadedFile $file, Request $request): void
    {
        ini_set('max_execution_time', '500');
        try {
            $runner($file, $request);
        } finally {
            ini_set('max_execution_time', '30');
        }
    }

    public function isFirstImport(Request $request): bool
    {
        return $request->primera === 'on';
    }
}
