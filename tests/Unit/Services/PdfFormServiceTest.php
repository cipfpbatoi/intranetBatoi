<?php

namespace Tests\Unit\Services;

use Intranet\Services\Document\PdfFormService;
use ReflectionClass;
use Tests\TestCase;

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
}
