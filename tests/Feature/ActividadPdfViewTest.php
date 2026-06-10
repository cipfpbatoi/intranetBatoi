<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Support\Collection;
use Tests\TestCase;

/**
 * Proves de renderitzat dels PDFs d'activitats.
 */
class ActividadPdfViewTest extends TestCase
{
    public function test_llistat_extraescolars_usa_etiqueta_descripcio_sense_justificacio_ra(): void
    {
        config(['signatures.llistats' => []]);

        $profesor = new \stdClass();
        $profesor->nombre = 'Anna';
        $profesor->apellido1 = 'Soler';

        $grupo = new \stdClass();
        $grupo->nombre = '1r CFGM Estètica';

        $actividad = new \stdClass();
        $actividad->id = 1;
        $actividad->name = 'Taller extraescolar';
        $actividad->descripcion = 'Descripció general';
        $actividad->objetivos = 'Objectius generals';
        $actividad->desde = '10-06-2026 09:00';
        $actividad->hasta = '10-06-2026 12:00';
        $actividad->fueraCentro = 1;
        $actividad->transport = 0;
        $actividad->comentarios = '';
        $actividad->profesores = new Collection([$profesor]);
        $actividad->grupos = new Collection([$grupo]);

        $html = view('pdf.extraescolars', [
            'todos' => new Collection([$actividad]),
            'datosInforme' => '10-06-2026',
        ])->render();

        $this->assertStringContainsString('Descripció</th>', $html);
        $this->assertStringNotContainsString('Descripció/Justificació', $html);
        $this->assertStringNotContainsString('Justificació RA', $html);
    }
}
