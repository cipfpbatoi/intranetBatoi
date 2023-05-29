<?php

namespace Intranet\Services;

use Intranet\Http\PrintResources\PrintResource;
use mikehaertl\pdftk\Pdf;
use Exception;

class FDFPrepareService
{
    public static function exec(PrintResource $resource, $id=null)
    {
        $id = $id??str_shuffle('abcdeft12');
        $nameFile = storage_path("tmp/{$id}_{$resource->getFile()}");
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
                self::stampPDF($pdf, $nameFile, $resource->getStamp());
            } else {
                $pdf->dropXfa()->dropXmp()->needAppearances()->saveAs($nameFile);
            }
            return $nameFile;
        }  catch (Exception $e) {
                dd($e->getMessage(), $pdf, $nameFile);
        }
    }

    private static function stampPDF($pdf, $nameFile, $stamp)
    {

        $tmpFileName = storage_path("tmp/".str_shuffle('abcdef12').'.pdf');
        $pdf->saveAs($tmpFileName);
        $tmp = new Pdf($tmpFileName);
        $tmp->stamp(public_path("fdf/$stamp"))
            ->saveAs($nameFile);
        unlink($tmpFileName);
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
