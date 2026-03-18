<?php

namespace Tests\Unit\Services;

use Intranet\Exceptions\IntranetException;
use Intranet\Services\School\ItacaService;
use Mockery;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Tests\TestCase;

class ItacaServiceTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_constructor_llanca_excepcio_si_no_hi_ha_driver()
    {
        $selenium = Mockery::mock('Intranet\\Services\\Automation\\SeleniumService');
        $selenium->shouldReceive('getDriver')->andReturn(null);

        $this->expectException(IntranetException::class);

        new ItacaService('00000000A', 'pass', $selenium, true);
    }

    public function test_close_crida_quit()
    {
        $selenium = Mockery::mock('Intranet\\Services\\Automation\\SeleniumService');
        $selenium->shouldReceive('getDriver')->andReturn(Mockery::mock(RemoteWebDriver::class));
        $selenium->shouldReceive('quit')->once();

        $service = new ItacaService('00000000A', 'pass', $selenium, false);
        $service->close();
        $this->addToAssertionCount(1);
    }
}
