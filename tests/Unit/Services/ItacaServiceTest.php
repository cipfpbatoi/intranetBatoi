<?php

namespace Tests\Unit\Services;

use Intranet\Entities\Falta_itaca;
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

    public function test_process_falta_retorna_false_quan_hi_ha_excepcio()
    {
        $selenium = Mockery::mock('Intranet\\Services\\Automation\\SeleniumService');
        $selenium->shouldReceive('getDriver')->andReturn(Mockery::mock(RemoteWebDriver::class));
        $selenium->shouldReceive('fill')->andThrow(new \Exception('boom'));

        session()->forget('app_alerts');

        $service = new ItacaService('00000000A', 'pass', $selenium, false);

        $falta = new Falta_itaca();
        $falta->idProfesor = '11111111A';
        $falta->dia = '2025-03-01';
        $falta->sesion_orden = 1;
        $falta->setRelation('Profesor', (object) ['shortName' => 'Profe Prova']);

        $this->assertFalse($service->processFalta($falta));
        $this->assertTrue(
            collect(session('app_alerts', []))->contains(
                static fn (array $alert): bool => ($alert['type'] ?? null) === 'danger'
            )
        );
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
