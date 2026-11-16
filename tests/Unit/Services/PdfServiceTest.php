<?php

namespace Tests\Unit\Services;

use Intranet\Services\Document\PdfService;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

/**
 * Verifica els peus documentals i els recursos opcionals de cada centre.
 */
class PdfServiceTest extends TestCase
{
    /** @var string|null Directori públic temporal de la prova. */
    private ?string $temporaryPublicPath = null;

    /**
     * Carrega les vistes de la mateixa còpia de codi que la prova.
     */
    protected function setUp(): void
    {
        parent::setUp();
        view()->getFinder()->prependLocation(__DIR__ . '/../../../resources/views');
    }

    /**
     * Elimina només els recursos temporals creats per la prova.
     */
    protected function tearDown(): void
    {
        if ($this->temporaryPublicPath !== null) {
            File::deleteDirectory($this->temporaryPublicPath);
        }
        parent::tearDown();
    }

    /**
     * Prepara un desplegament de centre amb o sense imatge de peu.
     */
    private function prepareCentreResources(bool $withFooter): void
    {
        $this->temporaryPublicPath = sys_get_temp_dir() . '/pdf-centre-' . uniqid();
        File::makeDirectory($this->temporaryPublicPath . '/img/pdf', 0755, true);
        $this->app->usePublicPath($this->temporaryPublicPath);
        if ($withFooter) {
            File::put(public_path('img/pdf/pie.png'), 'fixture');
        }
    }

    /**
     * El peu natiu reserva espai en totes les pàgines i conserva el codi documental.
     */
    public function testCommonHeaderUsesNativeCentreFooterWithDocumentCode(): void
    {
        $this->prepareCentreResources(true);
        config()->set('footers.resultado', ['codi' => 'ABC', 'edicio' => '02']);
        $pdf = (new PdfService())->hazPdf('pdf.resultado', collect(), 'Grup', 'landscape', 'a4', 15, 'SnappyPdf');
        $options = $pdf->snappy()->getOptions();

        $this->assertStringContainsString(public_path('img/pdf/pie.png'), $options['footer-html']);
        $this->assertStringContainsString('Codi: ABC  - Num. edicio: 02', $options['footer-html']);
        $this->assertSame(28, $options['margin-bottom']);
        $this->assertSame('landscape', $options['orientation']);
        $this->assertFalse($options['footer-line']);
        $this->assertSame('', $options['footer-right']);
    }

    /**
     * Els centres sense imatge mantenen el peu documental anterior.
     */
    public function testMissingCentreImageKeepsExistingFooter(): void
    {
        $this->prepareCentreResources(false);
        config()->set('footers.resultado', ['codi' => 'ABC', 'edicio' => '02']);
        $pdf = (new PdfService())->hazPdf('pdf.resultado', collect(), 'Grup', 'portrait', 'a4', 15, 'SnappyPdf');
        $options = $pdf->snappy()->getOptions();

        $this->assertNull($options['footer-html']);
        $this->assertTrue($options['footer-line']);
        $this->assertSame('Codi: ABC  - Num. edicio: 02', $options['footer-right']);
    }

    /**
     * Les altres capçaleres no activen el peu de la capçalera comuna.
     */
    public function testOtherHeadersDoNotUseCentreFooter(): void
    {
        $this->prepareCentreResources(true);
        $pdf = (new PdfService())->hazPdf('pdf.partials.cabeceraFSE', [], 'Grup', 'portrait', 'a4', 15, 'SnappyPdf');

        $this->assertNull($pdf->snappy()->getOptions()['footer-html']);
    }

    public function testFooterTextReturnsConfiguredValue()
    {
        config()->set('footers.testDoc', [
            'codi' => 'ABC',
            'edicio' => '02',
        ]);

        $service = new PdfService();

        $this->assertSame('Codi: ABC  - Num. edicio: 02', $service->footerText('pdf.fct.testDoc'));
    }

    public function testFooterTextReturnsEmptyWhenMissing()
    {
        $service = new PdfService();

        $this->assertSame('', $service->footerText('pdf.fct.unknownDoc'));
    }
}
