<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use Intranet\Services\HR\FitxatgeService;
use Intranet\Http\Middleware\ParkingIntegrationMiddleware;
use Intranet\Services\School\CotxeAccessService;
use Mockery;
use Tests\TestCase;

class ParkingIntegrationMiddlewareFeatureTest extends TestCase
{
    /**
     * Configura una ruta de prova amb el middleware d'integració de parking.
     */
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('variables.domotica.integration_token', 'parking-secret');
        config()->set('variables.domotica.integration_allowlist', ['127.0.0.1', '10.0.0.0/24']);

        Route::middleware('parking.integration')->any('/__test__/parking-integration', static function () {
            return response()->json(['ok' => true]);
        });

        Route::middleware('parking.integration:ip')->any('/__test__/parking-integration-ip', static function () {
            return response()->json(['ok' => true]);
        });
    }

    /**
     * Allibera dobles de prova després de cada cas.
     */
    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }

    /**
     * Rebutja peticions sense token compartit.
     */
    public function test_rejects_request_without_integration_token(): void
    {
        $response = $this->postJson('/__test__/parking-integration');

        $response->assertStatus(401)
            ->assertJson(['error' => 'Unauthorized integration request']);
    }

    /**
     * Rebutja peticions amb token incorrecte.
     */
    public function test_rejects_request_with_invalid_integration_token(): void
    {
        $response = $this->withHeader('X-Parking-Token', 'wrong-token')
            ->postJson('/__test__/parking-integration');

        $response->assertStatus(401)
            ->assertJson(['error' => 'Unauthorized integration request']);
    }

    /**
     * Permet l'accés amb el token correcte.
     */
    public function test_allows_request_with_valid_integration_token(): void
    {
        $response = $this->withHeader('X-Parking-Token', 'parking-secret')
            ->postJson('/__test__/parking-integration');

        $response->assertOk()
            ->assertJson(['ok' => true]);
    }

    /**
     * Retorna 503 si la integració no té token configurat.
     */
    public function test_returns_503_when_integration_token_is_not_configured(): void
    {
        config()->set('variables.domotica.integration_token', '');

        $response = $this->postJson('/__test__/parking-integration');

        $response->assertStatus(503)
            ->assertJson(['error' => 'Parking integration not configured']);
    }

    /**
     * Rebutja l'accés si la IP no està dins de l'allowlist dels webhooks.
     */
    public function test_rejects_ip_protected_route_when_ip_is_not_allowed(): void
    {
        $response = $this->withHeader('X-Parking-Token', 'parking-secret')
            ->withServerVariables(['REMOTE_ADDR' => '192.168.1.10'])
            ->postJson('/__test__/parking-integration-ip');

        $response->assertStatus(403)
            ->assertJson(['error' => 'Unauthorized integration source']);
    }

    /**
     * Permet l'accés si la IP encaixa en l'allowlist.
     */
    public function test_allows_ip_protected_route_when_ip_is_allowed(): void
    {
        $response = $this->withHeader('X-Parking-Token', 'parking-secret')
            ->withServerVariables(['REMOTE_ADDR' => '10.0.0.25'])
            ->postJson('/__test__/parking-integration-ip');

        $response->assertOk()
            ->assertJson(['ok' => true]);
    }

    /**
     * El middleware queda registrat al kernel amb l'alias esperat.
     */
    public function test_kernel_registers_expected_alias(): void
    {
        $kernel = app(\Intranet\Http\Kernel::class);

        $aliases = $this->getProtectedProperty($kernel, 'routeMiddleware');

        $this->assertSame(ParkingIntegrationMiddleware::class, $aliases['parking.integration'] ?? null);
    }

    /**
     * Les rutes reals de porta rebutgen peticions si no arriba el token.
     */
    public function test_real_parking_routes_require_integration_token(): void
    {
        $this->getJson('/api/porta/obrir')->assertStatus(401);
        $this->postJson('/api/porta/obrir-automatica')->assertStatus(401);
        $this->postJson('/api/eventPorta')->assertStatus(401);
        $this->postJson('/api/eventPortaSortida')->assertStatus(401);
    }

    /**
     * Els webhooks de càmera també exigeixen origen dins de l'allowlist.
     */
    public function test_real_camera_webhook_routes_require_allowed_ip(): void
    {
        $this->withHeader('X-Parking-Token', 'parking-secret')
            ->withServerVariables(['REMOTE_ADDR' => '192.168.1.10'])
            ->postJson('/api/porta/obrir-automatica')
            ->assertStatus(403);

        $this->withHeader('X-Parking-Token', 'parking-secret')
            ->withServerVariables(['REMOTE_ADDR' => '192.168.1.10'])
            ->postJson('/api/eventPorta')
            ->assertStatus(403);
    }

    /**
     * Amb token vàlid, la ruta de prova manual de porta continua operativa.
     */
    public function test_real_manual_open_route_allows_valid_integration_token(): void
    {
        $access = Mockery::mock(CotxeAccessService::class);
        $access->shouldReceive('obrirIPorta')->once()->andReturn(true);
        $this->app->instance(CotxeAccessService::class, $access);

        $fitxatge = Mockery::mock(FitxatgeService::class);
        $this->app->instance(FitxatgeService::class, $fitxatge);

        $response = $this->withHeader('X-Parking-Token', 'parking-secret')
            ->getJson('/api/porta/obrir');

        $response->assertOk()
            ->assertJson(['status' => 'Porta oberta (test)']);
    }

    /**
     * Amb token vàlid, el webhook automàtic arriba al controlador real.
     */
    public function test_real_automatic_open_route_reaches_controller_with_valid_token(): void
    {
        $access = Mockery::mock(CotxeAccessService::class);
        $this->app->instance(CotxeAccessService::class, $access);

        $fitxatge = Mockery::mock(FitxatgeService::class);
        $this->app->instance(FitxatgeService::class, $fitxatge);

        $response = $this->withHeader('X-Parking-Token', 'parking-secret')
            ->withServerVariables(['REMOTE_ADDR' => '10.0.0.25'])
            ->postJson('/api/porta/obrir-automatica', []);

        $response->assertStatus(422)
            ->assertJson(['error' => 'Sense matrícula']);
    }
}
