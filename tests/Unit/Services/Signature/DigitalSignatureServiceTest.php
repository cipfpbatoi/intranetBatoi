<?php

declare(strict_types=1);

namespace Tests\Unit\Services\Signature;

use Intranet\Services\Signature\DigitalSignatureService;
use Tests\TestCase;

/**
 * Tests de regressió del càlcul de pàgina visible de la signatura.
 */
class DigitalSignatureServiceTest extends TestCase
{
    /**
     * Comprova que la pàgina configurada té prioritat.
     */
    public function testResolveSignaturePageUsesConfiguredPageWhenProvided(): void
    {
        $service = new DigitalSignatureService();

        $page = $service->resolveSignaturePage('/tmp/non-existent.pdf', 2, 1);

        $this->assertSame(2, $page);
    }

    /**
     * Comprova que, si no es pot llegir el PDF, s'usa el valor de reserva.
     */
    public function testResolveSignaturePageFallsBackWhenPdfCannotBeRead(): void
    {
        $service = new DigitalSignatureService();

        $page = $service->resolveSignaturePage('/tmp/non-existent.pdf', null, 3);

        $this->assertSame(3, $page);
    }

    /**
     * Comprova que la configuració no fixa una pàgina concreta per a A2DUAL.
     */
    public function testA2DualDirectorPageIsNotForcedInConfig(): void
    {
        $this->assertNull(config('signatures.files.A2DUAL.director.page'));
    }
}
