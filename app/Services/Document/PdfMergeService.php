<?php

namespace Intranet\Services\Document;

use InvalidArgumentException;
use setasign\Fpdi\Fpdi;

/**
 * Servei per concatenar múltiples PDFs en un únic document amb FPDI.
 */
class PdfMergeService
{
    /**
     * Concatena els fitxers PDF indicats i guarda el resultat en la ruta de destí.
     *
     * @param array<int, string> $pdfs
     * @param string $outputPath
     * @return void
     */
    public function merge(array $pdfs, string $outputPath): void
    {
        if ($pdfs === []) {
            throw new InvalidArgumentException('Cal indicar almenys un PDF per concatenar.');
        }

        $pdf = new Fpdi();
        foreach ($pdfs as $file) {
            if (!is_string($file) || $file === '' || !file_exists($file)) {
                throw new InvalidArgumentException("Fitxer PDF invàlid o inexistent: {$file}");
            }

            $pageCount = $pdf->setSourceFile($file);
            for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                $template = $pdf->importPage($pageNo);
                $size = $pdf->getTemplateSize($template);

                $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
                $pdf->useTemplate($template);
            }
        }

        $outputDir = dirname($outputPath);
        if (!is_dir($outputDir)) {
            mkdir($outputDir, 0775, true);
        }

        $pdf->Output('F', $outputPath);
    }
}
