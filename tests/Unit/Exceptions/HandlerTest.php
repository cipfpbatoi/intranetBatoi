<?php

namespace Tests\Unit\Exceptions;

use Intranet\Exceptions\Handler;
use RuntimeException;
use Tests\TestCase;

/**
 * Proves unitàries del gestor global d'excepcions.
 */
class HandlerTest extends TestCase
{
    /**
     * Verifica que el resum intern d'error siga curt i sense traça completa.
     *
     * @return void
     */
    public function testBuildNotificationSummaryReturnsShortMessage(): void
    {
        $handler = $this->app->make(Handler::class);
        $exception = new RuntimeException(str_repeat("Línia d'error amb massa detall ", 80));

        $summary = $this->callProtectedMethod($handler, 'buildNotificationSummary', [$exception]);

        $this->assertStringStartsWith('RuntimeException: ', $summary);
        $this->assertStringContainsString('[HandlerTest.php:', $summary);
        $this->assertLessThanOrEqual(1000, mb_strlen($summary));
        $this->assertStringNotContainsString('#0 ', $summary);
    }
}
