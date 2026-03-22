<?php

declare(strict_types=1);

namespace Tests\Unit\Entities;

use Intranet\Entities\Colaboracion;
use Intranet\Entities\Centro;
use Intranet\Entities\FctDay;
use Tests\TestCase;

class FctDayTest extends TestCase
{
    public function test_colaboracion_id_buit_es_normalitza_a_null(): void
    {
        $day = new FctDay();
        $day->colaboracion_id = '';

        $this->assertNull($day->getAttributes()['colaboracion_id']);
    }

    public function test_colaboracion_id_numeric_es_normalitza_a_enter(): void
    {
        $day = new FctDay();
        $day->colaboracion_id = '17';

        $this->assertSame(17, $day->getAttributes()['colaboracion_id']);
    }

    public function test_horari_accessor_reutilitza_el_contracte_de_colaboracio(): void
    {
        $centro = new Centro();
        $centro->horarios = '08:00-14:00';

        $colaboracion = new Colaboracion();
        $colaboracion->setRelation('Centro', $centro);

        $day = new FctDay();
        $day->setRelation('Colaboracion', $colaboracion);

        $this->assertSame('08:00-14:00', $day->horari);
    }
}
