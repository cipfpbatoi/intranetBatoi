<?php

namespace Intranet\Services\Document;

use mikehaertl\pdftk\Pdf;
use RuntimeException;

/**
 * Encapsula les operacions de formularis PDF basades en pdftk.
 */
class PdfFormService
{
    /**
     * Emplena una plantilla PDF i desa el resultat en un fitxer.
     *
     * @param string $templatePath Ruta de la plantilla (absoluta o relativa).
     * @param array<mixed> $fields Camps del formulari.
     * @param string $outputPath Ruta absoluta de destí.
     * @param bool $flatten Indica si cal aplanar el formulari.
     * @return void
     */
    public function fillAndSave(string $templatePath, array $fields, string $outputPath, bool $flatten = false): void
    {
        $pdf = $this->createFilledPdf($templatePath, $fields, $flatten);
        if (!$pdf->saveAs($outputPath)) {
            throw new RuntimeException($pdf->getError() ?: 'Error guardant formulari PDF.');
        }
    }

    /**
     * Emplena una plantilla PDF i l'envia al navegador.
     *
     * @param string $templatePath Ruta de la plantilla (absoluta o relativa).
     * @param array<mixed> $fields Camps del formulari.
     * @param string $downloadName Nom del fitxer de descàrrega.
     * @param bool $flatten Indica si cal aplanar el formulari.
     * @return void
     */
    public function fillAndSend(string $templatePath, array $fields, string $downloadName, bool $flatten = false): void
    {
        $pdf = $this->createFilledPdf($templatePath, $fields, $flatten);
        if (!$pdf->send($downloadName)) {
            throw new RuntimeException($pdf->getError() ?: 'Error enviant formulari PDF.');
        }
    }

    /**
     * Emplena una plantilla i aplica el flux de preparació utilitzat pels recursos FDF.
     *
     * @param string $templatePath Ruta de la plantilla.
     * @param array<mixed> $fields Camps a emplenar.
     * @param string $outputPath Ruta absoluta de destí.
     * @param bool $flatten Indica si cal aplanar el formulari.
     * @param string|null $stampPath Ruta del segell PDF a aplicar.
     * @return void
     */
    public function fillForResource(
        string $templatePath,
        array $fields,
        string $outputPath,
        bool $flatten = false,
        ?string $stampPath = null
    ): void {
        $pdf = $this->createFilledPdf($templatePath, $fields, $flatten);
        $pdf->dropXfa()->dropXmp()->needAppearances();

        if ($stampPath === null) {
            if (!$pdf->saveAs($outputPath)) {
                throw new RuntimeException($pdf->getError() ?: 'Error guardant PDF preparat.');
            }
            return;
        }

        $tmpFile = storage_path('tmp/' . str_shuffle('abcdef12') . '.pdf');
        if (!$pdf->saveAs($tmpFile)) {
            throw new RuntimeException($pdf->getError() ?: 'Error guardant PDF temporal.');
        }

        $tmpPdf = new Pdf($tmpFile);
        if (!$tmpPdf->stamp($stampPath)->saveAs($outputPath)) {
            @unlink($tmpFile);
            throw new RuntimeException($tmpPdf->getError() ?: 'Error aplicant segell al PDF.');
        }

        @unlink($tmpFile);
    }

    /**
     * Crea una instància PDF amb els camps emplenats.
     *
     * @param string $templatePath
     * @param array<mixed> $fields
     * @param bool $flatten
     * @return Pdf
     */
    private function createFilledPdf(string $templatePath, array $fields, bool $flatten): Pdf
    {
        $pdf = new Pdf($this->resolveTemplatePath($templatePath));
        $pdf->fillForm($fields);
        if ($flatten) {
            $pdf->flatten();
        }

        return $pdf;
    }

    /**
     * Resol una ruta de plantilla relativa o absoluta.
     *
     * @param string $templatePath
     * @return string
     */
    private function resolveTemplatePath(string $templatePath): string
    {
        if (file_exists($templatePath)) {
            return $templatePath;
        }

        $publicPath = public_path($templatePath);
        return file_exists($publicPath) ? $publicPath : $templatePath;
    }
}
