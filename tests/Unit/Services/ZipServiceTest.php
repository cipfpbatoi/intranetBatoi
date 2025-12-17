<?php

namespace Tests\Unit\Services;

use Intranet\Services\ZipService;
use Tests\TestCase;

class ZipServiceTest extends TestCase
{
    private string $tmpDir;
    private array $files = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->tmpDir = storage_path('app/testing-zip');
        if (!is_dir($this->tmpDir)) {
            mkdir($this->tmpDir, 0755, true);
        }
        if (!is_dir(storage_path('tmp'))) {
            mkdir(storage_path('tmp'), 0755, true);
        }

        $this->files[] = $this->createTempFile('fitxer1.txt', 'Contingut 1');
        $this->files[] = $this->createTempFile('fitxer2.txt', 'Contingut 2');
    }

    public function test_exec_crea_zip_amb_fitxers()
    {
        $relativePath = ZipService::exec($this->files, 'prova_zip');
        $zipPath = storage_path($relativePath);

        $this->assertSame('tmp/prova_zip.zip', $relativePath);
        $this->assertFileExists($zipPath);

        $zip = new \ZipArchive();
        $this->assertTrue($zip->open($zipPath));

        foreach ($this->files as $file) {
            $name = basename($file);
            $this->assertNotFalse($zip->locateName($name), "No s'ha trobat $name dins del ZIP");
            $this->assertSame(file_get_contents($file), $zip->getFromName($name));
        }

        $zip->close();
    }

    public function test_exec_llanca_excepcio_si_no_hi_ha_fitxers()
    {
        $this->expectException(\InvalidArgumentException::class);
        ZipService::exec([], 'sense_fitxers');
    }

    public function test_exec_llanca_excepcio_si_un_fitxer_no_existeix()
    {
        $this->expectException(\InvalidArgumentException::class);
        ZipService::exec([$this->files[0], '/ruta/inexistent.pdf'], 'fitxer_fallant');
    }

    private function createTempFile(string $name, string $content): string
    {
        $path = $this->tmpDir . DIRECTORY_SEPARATOR . $name;
        file_put_contents($path, $content);

        return $path;
    }

    protected function tearDown(): void
    {
        array_map('unlink', array_filter($this->files, 'file_exists'));
        if (file_exists(storage_path('tmp/prova_zip.zip'))) {
            unlink(storage_path('tmp/prova_zip.zip'));
        }
        if (is_dir($this->tmpDir)) {
            array_map('unlink', glob($this->tmpDir.'/*'));
            rmdir($this->tmpDir);
        }

        parent::tearDown();
    }
}
