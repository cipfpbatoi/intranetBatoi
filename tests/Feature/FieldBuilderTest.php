<?php

declare(strict_types=1);

namespace Tests\Feature;

use Intranet\Services\UI\FieldBuilder;
use Tests\TestCase;

/**
 * Proves de compatibilitat dels camps de formulari compartits.
 */
class FieldBuilderTest extends TestCase
{
    public function test_radios_accepta_atributs_com_a_tercer_argument_del_component_generic(): void
    {
        $html = app(FieldBuilder::class)->radios(
            'activo',
            [1 => 'Actiu', 0 => 'Inactiu'],
            ['id' => 'activo_id', 'inline' => 'inline']
        );

        $this->assertStringContainsString('id="activo_id_1"', $html);
        $this->assertStringContainsString('id="activo_id_0"', $html);
        $this->assertStringContainsString('Actiu', $html);
        $this->assertStringContainsString('Inactiu', $html);
    }

    public function test_radios_marca_el_valor_escalar_seleccionat(): void
    {
        $html = app(FieldBuilder::class)->radios(
            'activo',
            [1 => 'Actiu', 0 => 'Inactiu'],
            0,
            ['id' => 'activo_id']
        );

        $this->assertMatchesRegularExpression('/value="0"[^>]*checked/u', $html);
        $this->assertDoesNotMatchRegularExpression('/value="1"[^>]*checked/u', $html);
    }
}
