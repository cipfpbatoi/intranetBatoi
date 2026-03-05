<?php

declare(strict_types=1);

namespace Tests\Unit\Exceptions;

use Illuminate\Http\Request;
use Intranet\Exceptions\Handler;
use Intranet\Exceptions\NotFoundDomainException;
use Tests\TestCase;

/**
 * Proves del contracte de renderització per a NotFoundDomainException.
 */
class HandlerNotFoundDomainExceptionTest extends TestCase
{
    /**
     * Comprova que la resposta JSON conserva codi i missatge de domini.
     */
    public function test_render_json_retoma_404_i_missatge_de_domini(): void
    {
        $handler = app(Handler::class);
        $request = Request::create('/dummy', 'GET', [], [], [], [
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $response = $handler->render($request, new NotFoundDomainException('Professor no trobat'));

        $this->assertSame(404, $response->getStatusCode());
        $this->assertSame(
            ['message' => 'Professor no trobat'],
            json_decode((string) $response->getContent(), true, 512, JSON_THROW_ON_ERROR)
        );
    }

    /**
     * Comprova que la resposta HTML renderitza la vista d'error 404.
     */
    public function test_render_html_retoma_vista_404(): void
    {
        $handler = app(Handler::class);
        $request = Request::create('/dummy', 'GET');

        $response = $handler->render($request, new NotFoundDomainException('Professor no trobat'));

        $this->assertSame(404, $response->getStatusCode());
        $this->assertStringContainsString('HO SENTIM, EL QUE CERQUES NO HO TROBE', $response->getContent());
    }
}
