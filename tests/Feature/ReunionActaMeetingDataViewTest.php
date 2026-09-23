<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

/**
 * Proves de les dades comunes impreses en els PDF d'acta.
 */
class ReunionActaMeetingDataViewTest extends TestCase
{
    public function test_mostra_el_lloc_de_la_reunio(): void
    {
        $html = view('pdf.reunion.partials.dades-acta', [
            'datosInforme' => (object) [
                'numero' => 3,
                'curso' => '2026-2027',
                'dia' => '23 de setembre de 2026',
                'hora' => '10:30',
                'llocReunio' => 'Sala de reunions',
            ],
        ])->render();

        $this->assertStringContainsString('Acta número', $html);
        $this->assertStringContainsString('al lloc', $html);
        $this->assertStringContainsString('Sala de reunions', $html);
    }
}
