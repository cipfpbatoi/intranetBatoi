<?php

namespace Tests\Unit\Support\Fct;

use Intranet\Support\Fct\DocumentoFctConfig;
use Tests\TestCase;

class DocumentoFctConfigTest extends TestCase
{
    public function testUsesEmailConfigWhenAvailable()
    {
        config()->set('fctEmails.testDoc', [
            'email' => [
                'subject' => 'Test',
            ],
            'modelo' => 'Foo',
            'finder' => 'Bar',
            'resource' => 'Baz',
            'view' => 'email.test',
        ]);

        $doc = new DocumentoFctConfig('testDoc');

        $this->assertSame('Foo', $doc->modelo);
        $this->assertSame('email.test', $doc->view);
        $this->assertSame('Intranet\\Finders\\BarFinder', $doc->getFinder());
        $this->assertSame('Intranet\\Http\\Resources\\SelectBazResource', $doc->getResource());
    }

    public function testUsesPdfConfigWhenEmailMissing()
    {
        config()->set('fctPdfs.pdfDoc', [
            'pdf' => [
                'descripcion' => 'Test',
                'orientacion' => 'portrait',
            ],
            'modelo' => 'Qux',
            'view' => 'pdf.test',
        ]);

        $doc = new DocumentoFctConfig('pdfDoc');

        $this->assertSame('Qux', $doc->modelo);
        $this->assertSame('pdf.test', $doc->view);
        $this->assertSame('Intranet\\Finders\\QuxFinder', $doc->getFinder());
        $this->assertSame('Intranet\\Http\\Resources\\SelectQuxResource', $doc->getResource());
    }

    public function testSetWritesEmailFeature()
    {
        config()->set('fctEmails.testSet', [
            'email' => [],
            'modelo' => 'Foo',
        ]);

        $doc = new DocumentoFctConfig('testSet');
        $doc->subject = 'Hola';

        $ref = new \ReflectionClass($doc);
        $features = $ref->getProperty('features');
        $features->setAccessible(true);

        $this->assertSame('Hola', $features->getValue($doc)['email']['subject']);
    }
}
