<?php

namespace Intranet\Services;

use Intranet\Entities\Profesor;
use mikehaertl\pdftk\Pdf;
use Intranet\Entities\Grupo;
use Exception;

class FDFPrepareService
{
    const RESOURCE = 'Intranet\\Http\\Resources\\';

    public static function exec($pdf, $elements, $stamp=null)
    {
        $resource= self::RESOURCE.$pdf['resource'];
        $id = str_shuffle('abcdeft12');
        $fdf = $pdf['fdf'];
        $nameFile = storage_path("tmp/{$id}_{$fdf}");
        $flatten = $pdf['flatten']??true;
        $pdf = new Pdf("fdf/".$fdf);
        $pdf->fillForm($resource::toArray($elements));

        if ($flatten) {
            $pdf->flatten();
        }

        if (file_exists($nameFile)) {
            unlink($nameFile);
        }
        try {
            if ($stamp) {
                self::stampPDF($pdf, $nameFile, $stamp);
            } else {
                $pdf->saveAs($nameFile);
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
        $tmp->stamp("fdf/$stamp")
            ->saveAs($nameFile);
        unlink($tmpFileName);
    }



}
