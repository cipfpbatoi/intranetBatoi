<?php

declare(strict_types=1);

namespace Tests\Unit;

use Illuminate\Support\Facades\Cache;
use Jenssegers\Date\Date;
use Tests\TestCase;

class DateHelpersTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['curso.fct.2.inici' => '2026-03-01']);
    }

    public function test_fechasao_transforma_dd_mm_yyyy_a_yyyy_mm_dd(): void
    {
        $this->assertSame('2026-02-11', fechaSao('11/02/2026'));
    }

    public function test_fechasao_retorna_original_si_format_invalid(): void
    {
        $this->assertSame('2026-02-11', fechaSao('2026-02-11'));
    }

    public function test_fechainglesacurta_transforma_dd_mm_yy(): void
    {
        $this->assertSame('2026-02-11', fechaInglesaCurta('11/02/26', '/'));
    }

    public function test_manana_i_mananadate_son_consistents(): void
    {
        $this->assertSame(date('Y-m-d', strtotime('+1 day')), manana());
        $this->assertInstanceOf(Date::class, mananaDate());
        $this->assertSame(manana(), mananaDate()->toDateString());
    }

    public function test_havencido_funciona_per_passat_i_futur(): void
    {
        $this->assertTrue(haVencido('2000-01-01'));
        $this->assertFalse(haVencido('2999-12-31'));
    }

    public function test_esmismodia_i_esmayor(): void
    {
        $this->assertTrue(esMismoDia('2026-02-11 10:00:00', '2026-02-11 23:59:59'));
        $this->assertFalse(esMismoDia('2026-02-11', '2026-02-12'));

        $this->assertTrue(esMayor('2026-02-12', '2026-02-11'));
        $this->assertFalse(esMayor('2026-02-11', '2026-02-12'));
    }

    public function test_sesion_usa_clau_de_cache_amb_hora(): void
    {
        Cache::shouldReceive('remember')
            ->once()
            ->withArgs(function ($key, $ttl, $callback) {
                $this->assertSame('HoraSes:10:30', $key);
                $this->assertNotNull($ttl);
                $this->assertIsCallable($callback);
                return true;
            })
            ->andReturn(3);

        $this->assertSame(3, sesion('10:30'));
    }

    public function test_periode_practiques_compara_amb_data_limit(): void
    {
        $this->assertSame(1, periodePractiques('2026-02-15'));
        $this->assertSame(1, periodePractiques('2026-03-01'));
        $this->assertSame(2, periodePractiques('2026-03-02'));
    }

    public function test_sumar_i_restar_hores(): void
    {
        $this->assertSame('11:45:00', sumarHoras('10:30:00', '01:15:00'));
        $this->assertSame('01:15:00', restarHoras('10:30:00', '11:45:00'));
    }

    public function test_hores_torna_format_decimal(): void
    {
        $this->assertSame(10.5, horas('10:30:00'));
        $this->assertEquals(8.0, horas('08:00:00'));
    }
}
