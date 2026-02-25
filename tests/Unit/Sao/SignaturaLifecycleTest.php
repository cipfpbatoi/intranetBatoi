<?php

declare(strict_types=1);

namespace Tests\Unit\Sao;

use Facebook\WebDriver\Remote\RemoteWebDriver;
use Illuminate\Auth\GenericUser;
use Illuminate\Http\RedirectResponse;
use Intranet\Sao\SaoSignaturaAction;
use Mockery;
use Tests\TestCase;

/**
 * Tests de caracterització del cicle de vida del driver en Signatura.
 */
class SignaturaLifecycleTest extends TestCase
{
    /**
     * Verifica que en flux correcte es tanca la sessió Selenium.
     */
    public function test_index_quits_driver_on_success_path(): void
    {
        $this->actingAs(new GenericUser([
            'dni' => '12345678A',
        ]), 'profesor');

        $signaturaAlias = Mockery::mock('alias:Intranet\Services\Signature\SignaturaService');
        $signaturaAlias->shouldReceive('exists')->once()->andReturn(true);

        $clickableElement = Mockery::mock();
        $clickableElement->shouldReceive('click')->once();

        $driver = Mockery::mock(RemoteWebDriver::class);
        $driver->shouldReceive('findElements')->twice()->andReturn([]);
        $driver->shouldReceive('findElement')->once()->andReturn($clickableElement);
        $driver->shouldReceive('quit')->once();

        $seleniumAlias = Mockery::mock('alias:Intranet\Services\Automation\SeleniumService');
        $seleniumAlias->shouldReceive('loginSAO')->once()->andReturn($driver);

        $response = (new SaoSignaturaAction())->index('secret');

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertSame(url(route('alumnofct.index', [], false)), $response->getTargetUrl());
    }

    /**
     * Verifica que en error de navegació també es tanca la sessió Selenium.
     */
    public function test_index_quits_driver_when_navigation_throws(): void
    {
        $this->actingAs(new GenericUser([
            'dni' => '12345678A',
        ]), 'profesor');

        $signaturaAlias = Mockery::mock('alias:Intranet\Services\Signature\SignaturaService');
        $signaturaAlias->shouldReceive('exists')->once()->andReturn(true);

        $driver = Mockery::mock(RemoteWebDriver::class);
        $driver->shouldReceive('findElements')->once()->andReturn([]);
        $driver->shouldReceive('findElement')->once()->andThrow(new \RuntimeException('Error simulat'));
        $driver->shouldReceive('quit')->once();

        $seleniumAlias = Mockery::mock('alias:Intranet\Services\Automation\SeleniumService');
        $seleniumAlias->shouldReceive('loginSAO')->once()->andReturn($driver);

        $response = (new SaoSignaturaAction())->index('secret');

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertSame(url(route('alumnofct.index', [], false)), $response->getTargetUrl());
    }
}
