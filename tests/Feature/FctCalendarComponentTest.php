<?php

namespace Tests\Feature;

use Intranet\Entities\Alumno;
use Intranet\Livewire\FctCalendar;
use Livewire\Livewire;
use Mockery;
use Tests\TestCase;

class FctCalendarComponentTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /** @test */
    public function el_wire_id_es_troba_a_larrel_del_component()
    {
        $this->mockColaboracioBuida();
        $this->mockFctDayExists(false);

        $alumne = new Alumno(['nia' => 'TEST001', 'nombre' => 'Prova', 'apellido1' => 'Usuari']);

        $component = Livewire::test(FctCalendar::class, ['alumno' => $alumne]);

        $html = $component->html();
        $this->assertStringContainsString('<div class="fct-calendar" wire:id="', $html);
    }

    private function mockColaboracioBuida(): void
    {
        $mock = Mockery::mock('alias:Intranet\\Entities\\Colaboracion');
        $mock->shouldReceive('MiColaboracion')->andReturnSelf();
        $mock->shouldReceive('with')->andReturnSelf();
        $mock->shouldReceive('get')->andReturn(collect());
        $mock->shouldReceive('sortBy')->andReturn(collect());
        $mock->shouldReceive('values')->andReturn(collect());
    }

    private function mockFctDayExists(bool $exists): void
    {
        $mock = Mockery::mock('alias:Intranet\\Entities\\FctDay');
        $mock->shouldReceive('where')->andReturnSelf();
        $mock->shouldReceive('exists')->andReturn($exists);
        $mock->shouldReceive('delete')->andReturn(0);
        $mock->shouldReceive('get')->andReturn(collect());
    }
}
