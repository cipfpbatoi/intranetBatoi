<?php

declare(strict_types=1);

namespace Tests\Unit\Entities;

use Intranet\Entities\Incidencia;
use Tests\TestCase;

class IncidenciaTest extends TestCase
{
    public function test_get_fechasolucion_attribute_retourna_buit_quan_es_null(): void
    {
        $incidencia = new Incidencia();
        $incidencia->setRawAttributes(['fechasolucion' => null]);

        $this->assertSame('', $incidencia->fechasolucion);
    }

    public function test_get_fechasolucion_attribute_formateja_data_valida(): void
    {
        $incidencia = new Incidencia();
        $incidencia->setRawAttributes(['fechasolucion' => '2026-02-21']);

        $this->assertSame('21-02-2026', $incidencia->fechasolucion);
    }
}
