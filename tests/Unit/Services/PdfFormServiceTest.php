<?php

namespace Tests\Unit\Services;

use Intranet\Services\Document\PdfFormService;
use ReflectionClass;
use Symfony\Component\Process\Process;
use Tests\TestCase;

/**
 * Verifica la generació i l'emplenament dels formularis PDF.
 */
class PdfFormServiceTest extends TestCase
{
    /**
     * Verifica que els camps FDF amb accents s'escriuen com a Unicode PDF.
     */
    public function test_create_temp_fdf_codifica_accents_com_unicode(): void
    {
        $service = new PdfFormService();
        $reflection = new ReflectionClass($service);
        $method = $reflection->getMethod('createTempFdf');
        $method->setAccessible(true);

        $path = $method->invoke($service, [
            'INSTRUCT' => 'José Pérez',
            'POBLACIÓN' => 'Alcoi',
        ]);

        try {
            $content = file_get_contents($path);

            $this->assertIsString($content);
            $this->assertStringContainsString('(INSTRUCT)', $content);
            $this->assertStringContainsString('<FEFF004A006F007300E90020005000E900720065007A>', $content);
            $this->assertStringContainsString('<FEFF0050004F0042004C00410043004900D3004E>', $content);
            $this->assertStringNotContainsString('José Pérez', $content);
        } finally {
            @unlink($path);
        }
    }

    /**
     * Verifica que la plantilla d'exempció accepta les dades automàtiques i
     * conserva buits els camps que el tutor ha d'emplenar manualment.
     */
    public function test_informe_exempcio_fe_accepta_els_camps_automatics(): void
    {
        $binary = (string) env('PDFTK_BINARY', '/usr/local/bin/pdftk');
        if (!is_executable($binary)) {
            $this->markTestSkipped('pdftk no està disponible en este entorn.');
        }

        $outputPath = storage_path('tmp/informe-exempcio-fe-test.pdf');
        $fields = [
            'Text1' => 'NIA: 12345678 - Alumne de prova - 600000000 - alumne@example.com',
            'Text2' => 'CIP FP Batoi 03012165',
            'Text3' => 'Desenvolupament Aplicacions Web',
            'Text4' => 'DNI: 12345678A - Tutora de prova - 600000001 - tutora@example.com',
            'Tutora del centre educatiu    Tutora del centro educativo' => 'Alcoi',
            'undefined' => '06',
            'de' => 'novembre',
            'Text20' => '26',
        ];

        try {
            (new PdfFormService())->fillAndSave(
                'fdf/InformeExencionFCT.pdf',
                $fields,
                $outputPath
            );

            $this->assertFileExists($outputPath);
            $dumpedFields = $this->dumpPdfFields($binary, $outputPath);

            foreach ($fields as $name => $value) {
                $this->assertArrayHasKey($name, $dumpedFields);
                $this->assertSame($value, $dumpedFields[$name]);
            }

            $this->assertSame('', $dumpedFields['Signatura  Firma']);
            $this->assertSame('', $dumpedFields['Text15']);
        } finally {
            @unlink($outputPath);
        }
    }

    /**
     * Extrau els valors canònics dels camps d'un formulari amb pdftk.
     *
     * @return array<string, string>
     */
    private function dumpPdfFields(string $binary, string $pdfPath): array
    {
        $process = new Process([$binary, $pdfPath, 'dump_data_fields_utf8']);
        $process->mustRun();

        $fields = [];
        foreach (preg_split('/^---\R/m', $process->getOutput()) ?: [] as $block) {
            if (preg_match('/^FieldName: (.*)$/m', $block, $name) !== 1) {
                continue;
            }

            preg_match('/^FieldValue: (.*)$/m', $block, $value);
            $fields[$name[1]] = $value[1] ?? '';
        }

        return $fields;
    }
}
