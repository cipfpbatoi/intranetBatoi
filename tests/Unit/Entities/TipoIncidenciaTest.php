<?php

namespace Tests\Unit\Entities;

use Intranet\Entities\TipoIncidencia;
use Tests\TestCase;

class TipoIncidenciaTest extends TestCase
{
    public function test_literal_accessor_usa_locale_actual(): void
    {
        $tipo = new TipoIncidencia([
            'nombre' => 'Avería',
            'nom' => 'Avaria',
        ]);

        app()->setLocale('es');
        $this->assertSame('Avería', $tipo->literal);

        app()->setLocale('ca');
        $this->assertSame('Avaria', $tipo->literal);
    }

    public function test_tipo_i_profesor_accessors_son_null_safe(): void
    {
        config()->set('auxiliares.tipoIncidencia', [1 => 'Maquinari']);

        $tipo = new TipoIncidencia([
            'tipus' => 1,
        ]);
        $this->assertSame('Maquinari', $tipo->tipo);

        $tipoSenseTipus = new TipoIncidencia([
            'tipus' => 99,
        ]);
        $this->assertSame('', $tipoSenseTipus->tipo);

        $tipo->setRelation('Responsable', null);
        $this->assertSame('', $tipo->profesor);
    }
}

