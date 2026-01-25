<?php

namespace Tests\Unit\Services;

use Intranet\Entities\Falta_itaca;
use Intranet\Exceptions\IntranetException;
use Intranet\Services\ItacaService;
use Mockery;
use Styde\Html\Facades\Alert;
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
        $selenium = Mockery::mock('overload:Intranet\\Services\\SeleniumService');
        $selenium->shouldReceive('getDriver')->andReturn(null);

        $this->expectException(IntranetException::class);

        new ItacaService('00000000A', 'pass');
    }

    public function test_process_falta_retorna_false_quan_hi_ha_excepcio()
    {
        $selenium = Mockery::mock('overload:Intranet\\Services\\SeleniumService');
        $selenium->shouldReceive('getDriver')->andReturn(new \stdClass());
        $selenium->shouldReceive('fill')->andThrow(new \Exception('boom'));

        Alert::shouldReceive('danger')->once();

        $service = new ItacaService('00000000A', 'pass');

        $falta = new Falta_itaca();
        $falta->idProfesor = '11111111A';
        $falta->dia = '2025-03-01';
        $falta->sesion_orden = 1;
        $falta->setRelation('Profesor', (object) ['shortName' => 'Profe Prova']);

        $this->assertFalse($service->processFalta($falta));
    }

    public function test_close_crida_quit()
    {
        $selenium = Mockery::mock('overload:Intranet\\Services\\SeleniumService');
        $selenium->shouldReceive('getDriver')->andReturn(new \stdClass());
        $selenium->shouldReceive('quit')->once();

        $service = new ItacaService('00000000A', 'pass');
        $service->close();
        $this->addToAssertionCount(1);
    }
}
