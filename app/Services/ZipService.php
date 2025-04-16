<?php
namespace Intranet\Services;

class ZipService
{
    public static function exec($files, $nameFile)
    {
        $zip = new \ZipArchive();
        $tmpFileName = storage_path("tmp/$nameFile.zip");
        if ($zip->open($tmpFileName, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === true) {
            foreach ($files as $file) {
                $zip->addFile($file, basename($file));
            }
            $zip->close();
        }
        return "tmp/$nameFile.zip";
    }
}
