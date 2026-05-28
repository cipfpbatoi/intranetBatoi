<?php

namespace Intranet\Sao\Support;

/**
 * Operacions comunes de fitxers temporals en processos SAO.
 */
class SaoDownloadManager
{
    /**
     * Retorna el directori temporal compartit per SAO.
     *
     * @return string
     */
    public function tempDirectory(): string
    {
        return config('variables.shareDirectory')
            ?? config('sao.download.directory')
            ?? storage_path('tmp/');
    }

    /**
     * Espera a l'existència d'un fitxer dins del timeout indicat.
     *
     * @param string $filePath
     * @param int $timeoutSeconds
     * @return void
     */
    public function waitForFile(string $filePath, int $timeoutSeconds): void
    {
        $startTime = time();
        while (!file_exists($filePath)) {
            if (time() - $startTime > $timeoutSeconds) {
                throw new \RuntimeException("Timeout waiting for file: $filePath");
            }
            sleep(1);
        }
    }

    /**
     * Esborra un fitxer si existeix.
     *
     * @param string $filePath
     * @return void
     */
    public function unlinkIfExists(string $filePath): void
    {
        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }
}
