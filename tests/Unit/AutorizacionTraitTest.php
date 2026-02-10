<?php

declare(strict_types=1);

namespace Tests\Unit;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Intranet\Http\Traits\Autorizacion;
use Intranet\Services\General\AutorizacionPrintService;
use Intranet\Services\General\AutorizacionStateService;
use Mockery;
use Tests\TestCase;

class AutorizacionTraitTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_init_usa_propietat_init_i_delega_al_servei(): void
    {
        $controller = new DummyAutorizacionController();
        $controller->setInitValue(3);

        $stateService = Mockery::mock(AutorizacionStateService::class);
        $stateService->shouldReceive('init')
            ->once()
            ->with(7, 3)
            ->andReturn(true);

        $this->app->bind(AutorizacionStateService::class, function ($app, $params) use ($stateService) {
            return $stateService;
        });

        $response = $this->callProtectedMethod($controller, 'init', [7]);

        $this->assertInstanceOf(RedirectResponse::class, $response);
    }

    public function test_accept_redirigix_a_la_pestanya_final_quan_notfollow_es_false(): void
    {
        $this->startSession();
        $controller = new DummyAutorizacionController();

        $stateService = Mockery::mock(AutorizacionStateService::class);
        $stateService->shouldReceive('accept')
            ->once()
            ->with(15)
            ->andReturn(['initial' => 2, 'final' => 4]);

        $this->app->bind(AutorizacionStateService::class, function ($app, $params) use ($stateService) {
            return $stateService;
        });

        $response = $this->callProtectedMethod($controller, 'accept', [15, true]);

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertSame(4, $response->getSession()->get('pestana'));
    }

    public function test_accept_manté_pestanya_inicial_quan_notfollow_es_true(): void
    {
        $this->startSession();
        $controller = new DummyAutorizacionController();
        $controller->setNotFollowValue(true);

        $stateService = Mockery::mock(AutorizacionStateService::class);
        $stateService->shouldReceive('accept')
            ->once()
            ->with(19)
            ->andReturn(['initial' => 3, 'final' => 5]);

        $this->app->bind(AutorizacionStateService::class, function ($app, $params) use ($stateService) {
            return $stateService;
        });

        $response = $this->callProtectedMethod($controller, 'accept', [19, true]);

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertSame(3, $response->getSession()->get('pestana'));
    }

    public function test_imprimir_retorna_la_resposta_del_servei_quan_hi_ha_pdf(): void
    {
        $controller = new DummyAutorizacionController();
        $expectedResponse = new Response('ok', 200);

        $printService = Mockery::mock(AutorizacionPrintService::class);
        $printService->shouldReceive('imprimir')
            ->once()
            ->with(DummyAutorizacionController::MODEL_CLASS, 'Profesor', '', null, null, 'portrait', true)
            ->andReturn($expectedResponse);

        $this->app->instance(AutorizacionPrintService::class, $printService);

        $response = $controller->imprimir();

        $this->assertSame($expectedResponse, $response);
    }

    public function test_imprimir_fa_back_quan_no_hi_ha_elements(): void
    {
        $controller = new DummyAutorizacionController();

        $printService = Mockery::mock(AutorizacionPrintService::class);
        $printService->shouldReceive('imprimir')
            ->once()
            ->andReturn(null);

        $this->app->instance(AutorizacionPrintService::class, $printService);

        $response = $controller->imprimir();

        $this->assertInstanceOf(RedirectResponse::class, $response);
    }
}

class DummyAutorizacionController
{
    use Autorizacion;

    public const MODEL_CLASS = 'Intranet\\Entities\\Profesor';

    public string $class = self::MODEL_CLASS;
    public string $model = 'Profesor';

    public function setInitValue(int $value): void
    {
        $this->init = $value;
    }

    public function setNotFollowValue(bool $value): void
    {
        $this->notFollow = $value;
    }
}
