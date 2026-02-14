<?php

namespace Tests\Unit\Services;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Intranet\Finders\Finder;
use Intranet\Services\Document\DocumentService;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Tests\TestCase;

class DocumentServiceTest extends TestCase
{
    private string $tmpDir;
    private string $zipPath;

    protected function setUp(): void
    {
        parent::setUp();
        $this->tmpDir = storage_path('tmp/test-doc-service');
        $this->zipPath = storage_path('tmp/prova.zip');
        if (!is_dir($this->tmpDir)) {
            mkdir($this->tmpDir, 0755, true);
        }
        if (!is_dir(storage_path('tmp'))) {
            mkdir(storage_path('tmp'), 0755, true);
        }
    }

    public function test_generate_zip_retornara_error_si_zipservice_falla()
    {
        $service = new DocumentService($this->fakeFinder());

        $response = $this->callProtectedMethod($service, 'generateZip', [[], 'prova']);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame(400, $response->getStatusCode());
        $this->assertStringContainsString('Cal indicar almenys un fitxer', $response->getContent());
    }

    public function test_generate_zip_retornara_fitxer_si_tot_va_be()
    {
        $pdfPath = $this->createTempFile('docservice.pdf', 'PDF');

        $service = new DocumentService($this->fakeFinder());
        $response = $this->callProtectedMethod($service, 'generateZip', [[$pdfPath], 'prova']);

        $this->assertInstanceOf(BinaryFileResponse::class, $response);
        $this->assertSame($this->zipPath, $response->getFile()->getPathname());
    }

    public function test_normalizePdfPaths_filtra_paths_inexistents()
    {
        $pdfPath = $this->createTempFile('existeix.pdf', 'PDF');
        $missing = $this->tmpDir . DIRECTORY_SEPARATOR . 'falta.pdf';

        $elements = [
            $pdfPath,
            (object)['routeFile' => $pdfPath],
            ['routeFile' => $missing],
            '',
        ];

        $service = new DocumentService($this->fakeFinder());
        $result = $this->callProtectedMethod($service, 'normalizePdfPaths', [$elements]);

        $this->assertEquals([$pdfPath, $pdfPath], $result);
    }

    private function createTempFile(string $name, string $content): string
    {
        $path = $this->tmpDir . DIRECTORY_SEPARATOR . $name;
        file_put_contents($path, $content);
        return $path;
    }

    private function fakeFinder(): Finder
    {
        return new class extends Finder {
            public function __construct($document = null)
            {
                // Evitem la lògica del constructor original
            }

            public function getZip()
            {
                return true;
            }

            public function exec()
            {
                return new Collection();
            }

            public function getDocument()
            {
                return (object)[];
            }

            public function getRequest()
            {
                return (object)[];
            }
        };
    }

    protected function tearDown(): void
    {
        if (is_dir($this->tmpDir)) {
            array_map('unlink', glob($this->tmpDir.'/*'));
            rmdir($this->tmpDir);
        }
        if (file_exists($this->zipPath)) {
            unlink($this->zipPath);
        }
        parent::tearDown();
    }
}
