<?php

namespace Tests\Unit\Entities;

use Intranet\Entities\Departamento;
use Intranet\Entities\TipoActividad;
use Tests\TestCase;

class TipoActividadTest extends TestCase
{
    public function test_departamento_accessor_es_null_safe_i_te_default(): void
    {
        $tipo = new TipoActividad();
        $tipo->setRelation('departament', null);

        $this->assertSame('CENTRE', $tipo->departamento);
    }

    public function test_departamento_accessor_torna_vliteral_quan_hi_ha_relacio(): void
    {
        $tipo = new TipoActividad();
        $tipo->setRelation('departament', new Departamento(['vliteral' => 'Informàtica']));

        $this->assertSame('Informàtica', $tipo->departamento);
    }
}

