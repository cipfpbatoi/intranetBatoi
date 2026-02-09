<?php

namespace Tests\Unit\Services;

use Intranet\Services\PdfService;
use Tests\TestCase;

class PdfServiceTest extends TestCase
{
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
