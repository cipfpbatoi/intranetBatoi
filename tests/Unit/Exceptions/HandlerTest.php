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
    /** Conserva el temps d'espera de les respostes JSON 429. */
    public function test_throttle_conserva_retry_after(): void
    {
        $request = \Illuminate\Http\Request::create('/api/reserva', 'POST');
        $request->headers->set('Accept', 'application/json');
        $exception = new \Illuminate\Http\Exceptions\ThrottleRequestsException(
            'Too Many Attempts.', null, ['Retry-After' => '42']
        );
        $response = $this->app->make(Handler::class)->render($request, $exception);
        $this->assertSame(429, $response->getStatusCode());
        $this->assertSame('42', $response->headers->get('Retry-After'));
    }

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
