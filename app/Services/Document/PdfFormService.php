<?php

namespace Intranet\Services\Document;

use RuntimeException;
use Symfony\Component\Process\Process;

/**
 * Encapsula les operacions de formularis PDF basades en pdftk via CLI.
 */
class PdfFormService
{
    private const DEFAULT_PDFTK_BINARY = '/usr/local/bin/pdftk';

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
        $this->ensureOutputDirectory($outputPath);
        $fdfPath = $this->createTempFdf($fields);
        try {
            $args = [
                $this->binary(),
                $this->resolveTemplatePath($templatePath),
                'fill_form',
                $fdfPath,
                'output',
                $outputPath,
                'need_appearances',
                'drop_xfa',
            ];

            if ($flatten) {
                $args[] = 'flatten';
            }

            $this->runCommand($args);
        } finally {
            @unlink($fdfPath);
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
        $tmpPath = storage_path('tmp/' . str_shuffle('abcdef123456') . '.pdf');
        $this->fillAndSave($templatePath, $fields, $tmpPath, $flatten);

        if (!file_exists($tmpPath)) {
            throw new RuntimeException('No s\'ha pogut generar el PDF per enviar.');
        }

        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . basename($downloadName) . '"');
        header('Content-Length: ' . (string) filesize($tmpPath));
        readfile($tmpPath);
        @unlink($tmpPath);
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
        if ($stampPath === null) {
            $this->fillAndSave($templatePath, $fields, $outputPath, $flatten);
            return;
        }

        $tmpFile = storage_path('tmp/' . str_shuffle('abcdef123456') . '.pdf');
        try {
            $this->fillAndSave($templatePath, $fields, $tmpFile, $flatten);
            $this->ensureOutputDirectory($outputPath);
            $this->runCommand([
                $this->binary(),
                $tmpFile,
                'stamp',
                $this->resolveTemplatePath($stampPath),
                'output',
                $outputPath,
            ]);
        } finally {
            @unlink($tmpFile);
        }
    }

    /**
     * Executa un comandament de procés i valida l'eixida.
     *
     * @param array<int, string> $args
     * @return void
     */
    private function runCommand(array $args): void
    {
        $process = new Process($args);
        $process->setTimeout((float) env('PDFTK_TIMEOUT', 30));
        $process->run();

        if (!$process->isSuccessful()) {
            $error = trim($process->getErrorOutput() ?: $process->getOutput());
            throw new RuntimeException($error !== '' ? $error : 'Error executant pdftk.');
        }
    }

    /**
     * Crea un fitxer temporal FDF amb les dades del formulari.
     *
     * @param array<mixed> $fields
     * @return string
     */
    private function createTempFdf(array $fields): string
    {
        $tmpPath = storage_path('tmp/' . str_shuffle('abcdef123456') . '.fdf');
        $dir = dirname($tmpPath);
        if (!is_dir($dir)) {
            mkdir($dir, 0775, true);
        }

        $fieldEntries = [];
        foreach ($fields as $name => $value) {
            if (is_array($value) || is_object($value)) {
                continue;
            }

            $fieldName = $this->escapeFdfString((string) $name);
            $fieldValue = $this->escapeFdfString((string) ($value ?? ''));
            $fieldEntries[] = "<< /T ({$fieldName}) /V ({$fieldValue}) >>";
        }

        $fdf = "%FDF-1.2\n";
        $fdf .= "1 0 obj\n";
        $fdf .= "<< /FDF << /Fields [\n";
        $fdf .= implode("\n", $fieldEntries) . "\n";
        $fdf .= "] >> >>\n";
        $fdf .= "endobj\n";
        $fdf .= "trailer\n";
        $fdf .= "<< /Root 1 0 R >>\n";
        $fdf .= "%%EOF\n";

        file_put_contents($tmpPath, $fdf);

        return $tmpPath;
    }

    /**
     * Escapa valors per a cadenes literals en FDF.
     *
     * @param string $value
     * @return string
     */
    private function escapeFdfString(string $value): string
    {
        $value = str_replace('\\', '\\\\', $value);
        $value = str_replace('(', '\\(', $value);
        return str_replace(')', '\\)', $value);
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
        if (file_exists($publicPath)) {
            return $publicPath;
        }

        throw new RuntimeException("Plantilla PDF no trobada: {$templatePath}");
    }

    /**
     * Retorna el binari pdftk configurat.
     *
     * @return string
     */
    private function binary(): string
    {
        return (string) env('PDFTK_BINARY', self::DEFAULT_PDFTK_BINARY);
    }

    /**
     * Crea el directori de destí si no existix.
     *
     * @param string $outputPath
     * @return void
     */
    private function ensureOutputDirectory(string $outputPath): void
    {
        $dir = dirname($outputPath);
        if (!is_dir($dir)) {
            mkdir($dir, 0775, true);
        }
    }
}
