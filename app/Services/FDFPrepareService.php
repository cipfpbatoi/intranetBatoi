<?php

namespace Intranet\Services;

use Intranet\Http\PrintResources\PrintResource;
use mikehaertl\pdftk\Pdf;
use Exception;
use Illuminate\Support\Facades\Log;

class FDFPrepareService
{
    public static function exec(PrintResource $resource, $id=null)
    {
        $id = $id??str_shuffle('abcdeft12');
        $nameFile = storage_path("tmp/{$id}_{$resource->getFile()}");
        $tmpDir = dirname($nameFile);

        if (!is_dir($tmpDir)) {
            mkdir($tmpDir, 0775, true);
        }

        $pdf = new Pdf(public_path("fdf/".$resource->getFile()));
        $pdf->fillForm($resource->toArray());
        if ($resource->getFlatten()) {
            $pdf->flatten();
        }
        if (file_exists($nameFile)) {
            unlink($nameFile);
        }
        try {
            if ($resource->getStamp()) {
                $stamped = self::stampPDF($pdf, $nameFile, $resource->getStamp());
                if ($stamped === null || !file_exists($nameFile)) {
                    Log::error('Stamp no ha generat fitxer', [
                        'file' => $nameFile,
                        'stamp' => $resource->getStamp(),
                    ]);
                    return null;
                }
            } else {
                if (!$pdf->dropXfa()->dropXmp()->needAppearances()->saveAs($nameFile)) {
                    Log::error('Error guardant PDF amb pdftk', [
                        'file' => $nameFile,
                        'error' => $pdf->getError(),
                    ]);
                    return null;
                }
            }
            return $nameFile;
        }  catch (Exception $e) {
            Log::error('Excepció generant PDF', [
                'message' => $e->getMessage(),
                'file' => $nameFile,
                'resource' => $resource->getFile(),
            ]);
            return null;
        }
    }

    private static function stampPDF($pdf, $nameFile, $stamp)
    {

        $tmpFileName = storage_path("tmp/".str_shuffle('abcdef12').'.pdf');
        $pdf->dropXfa()->dropXmp()->needAppearances();
        if (!$pdf->saveAs($tmpFileName)) {
            Log::error('Error temporal guardant PDF abans de stamp', [
                'tmp' => $tmpFileName,
                'error' => $pdf->getError(),
            ]);
            return null;
        }
        $tmp = new Pdf($tmpFileName);
        if (!$tmp->stamp(public_path("fdf/$stamp"))->saveAs($nameFile)) {
            Log::error('Error afegint stamp al PDF', [
                'tmp' => $tmpFileName,
                'dest' => $nameFile,
                'error' => $tmp->getError(),
            ]);
            unlink($tmpFileName);
            return null;
        }
        unlink($tmpFileName);
        return $nameFile;
    }

    public static function joinPDFs($pdfs, $nameFile)
    {
        $tmpFileName = storage_path("tmp/$nameFile.pdf");
        $pdf = new Pdf();
        foreach ($pdfs as $file) {
            $pdf->addFile($file);
        }
        $pdf->saveAs($tmpFileName);
        return "tmp/$nameFile.pdf";
    }
}
