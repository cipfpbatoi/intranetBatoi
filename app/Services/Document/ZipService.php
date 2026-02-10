<?php
namespace Intranet\Services\Document;

class ZipService
{
    /**
     * Servei per empaquetar un conjunt de fitxers en un ZIP temporal.
     */
    /**
     * Crea un fitxer ZIP amb els paths indicats i retorna el path relatiu dins de storage/tmp.
     *
     * @param iterable $files    Llistat de rutes completes a fitxers existents.
     * @param string   $nameFile Nom base del fitxer zip (sense extensió).
     *
     * @return string Ruta relativa dins de storage (ej. tmp/nom.zip).
     *
     * @throws \InvalidArgumentException Si la llista de fitxers està buida o conté rutes inexistents.
     * @throws \RuntimeException         Si no es pot crear el directori temporal o el fitxer ZIP.
     */
    public static function exec(iterable $files, string $nameFile): string
    {
        $fileList = is_array($files) ? $files : iterator_to_array($files, false);
        if (count($fileList) === 0) {
            throw new \InvalidArgumentException('Cal indicar almenys un fitxer per comprimir');
        }

        $tmpDir = storage_path('tmp');
        if (!is_dir($tmpDir) && !mkdir($tmpDir, 0755, true)) {
            throw new \RuntimeException('No s\'ha pogut crear el directori temporal');
        }

        $zip = new \ZipArchive();
        $tmpFileName = $tmpDir . DIRECTORY_SEPARATOR . "$nameFile.zip";

        if ($zip->open($tmpFileName, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            throw new \RuntimeException('No s\'ha pogut obrir el fitxer ZIP per escriure');
        }

        foreach ($fileList as $file) {
            if (!is_string($file) || !file_exists($file)) {
                throw new \InvalidArgumentException("Fitxer inexistent: {$file}");
            }
            $zip->addFile($file, basename($file));
        }

        $zip->close();

        return "tmp/$nameFile.zip";
    }
}
