<?php

namespace Tests\Unit\Middleware;

use Illuminate\Http\Request;
use Intranet\Http\Middleware\ApiTokenToBearer;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

/**
 * Proves de compatibilitat del middleware ApiTokenToBearer.
 */
class ApiTokenToBearerTest extends TestCase
{
    /**
     * Ha de promoure `api_token` a capçalera Bearer quan no existeix Authorization.
     */
    public function test_promou_api_token_a_bearer_si_no_hi_ha_authorization(): void
    {
        $request = Request::create('/api/actividad', 'GET', ['api_token' => 'abc123']);
        $middleware = new ApiTokenToBearer();

        $middleware->handle($request, function (Request $innerRequest) {
            return new Response($innerRequest->headers->get('Authorization', ''));
        });

        $this->assertSame('Bearer abc123', $request->headers->get('Authorization'));
    }

    /**
     * No ha de sobreescriure capçalera Authorization existent.
     */
    public function test_no_sobreescriu_authorization_existint(): void
    {
        $request = Request::create('/api/actividad', 'GET', ['api_token' => 'legacy']);
        $request->headers->set('Authorization', 'Bearer existent');
        $middleware = new ApiTokenToBearer();

        $middleware->handle($request, function (Request $innerRequest) {
            return new Response($innerRequest->headers->get('Authorization', ''));
        });

        $this->assertSame('Bearer existent', $request->headers->get('Authorization'));
    }
}
