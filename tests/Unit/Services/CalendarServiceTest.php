<?php

namespace Tests\Unit\Services;

use Illuminate\Support\Facades\Config;
use Intranet\Services\Calendar\CalendarService;
use Tests\TestCase;

class CalendarServiceTest extends TestCase
{
    private string $originalTimezone;

    protected function setUp(): void
    {
        parent::setUp();
        $this->originalTimezone = date_default_timezone_get();
        date_default_timezone_set('UTC');
    }

    protected function tearDown(): void
    {
        date_default_timezone_set($this->originalTimezone);
        parent::tearDown();
    }

    public function test_build_amb_rang_dates_inclou_summary_i_description()
    {
        Config::set('contacto.nombre', 'Batoi');

        $element = new CalendarDummy();
        $element->desde = '2025-03-01 10:00:00';
        $element->hasta = '2025-03-01 12:00:00';
        $element->descripcion = 'Reunio';
        $element->objetivos = 'Objectiu';

        $calendar = CalendarService::build($element);
        $render = $calendar->render();

        $this->assertStringContainsString('BEGIN:VEVENT', $render);
        $this->assertStringContainsString('SUMMARY:CalendarDummy : Reunio', $render);
        $this->assertStringContainsString('DESCRIPTION:Objectiu', $render);
        $this->assertStringContainsString('LOCATION:Batoi', $render);
    }

    public function test_build_amb_data_simple_crea_fi_una_hora_despres()
    {
        Config::set('contacto.nombre', 'Batoi');

        $element = new CalendarDummy();
        $element->fecha = '2025-03-02';
        $element->descripcion = 'Visita';
        $element->objetivos = 'Detall';

        $calendar = CalendarService::build($element);
        $render = $calendar->render();

        $this->assertStringContainsString('DTSTART:20250302T000000Z', $render);
        $this->assertStringContainsString('DTEND:20250302T010000Z', $render);
    }
}

class CalendarDummy
{
    public string $descripcion;
    public string $objetivos;
    public ?string $desde = null;
    public ?string $hasta = null;
    public ?string $fecha = null;
}
