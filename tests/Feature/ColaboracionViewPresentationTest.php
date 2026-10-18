<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Support\Facades\Blade;
use Mockery;
use Tests\TestCase;

/**
 * Proves de presentació dels panells de col·laboracions.
 */
class ColaboracionViewPresentationTest extends TestCase
{
    /** El panell personal exposa un filtre d'empresa combinat amb els filtres existents. */
    public function test_mis_colaboraciones_inclou_el_filtre_d_empresa_combinat(): void
    {
        $pestana = Mockery::mock();
        $pestana->shouldReceive('getNombre')->andReturn('resta');

        $panel = Mockery::mock();
        $panel->shouldReceive('getElementos')
            ->with($pestana)
            ->andReturn(collect());

        $html = Blade::render(
            <<<'BLADE'
                @include('intranet.partials.profile.colaboracion')
                @stack('scripts')
            BLADE,
            compact('panel', 'pestana')
        );

        $this->assertStringContainsString('Filtrar per empresa', $html);
        $this->assertStringContainsString('mis-colaboraciones-company-filter', $html);
        $this->assertStringContainsString('normalizeCompany', $html);
        $this->assertStringContainsString('matchesTown && matchesCompany && matchesQuickFilters', $html);
        $this->assertStringContainsString('[^A-Z0-9\\s]', $html);
    }

    /** El panell departamental separa empreses i agrupa dins les seues col·laboracions. */
    public function test_el_panell_departamental_mostra_blocs_d_empresa_diferenciats(): void
    {
        $pestana = new \stdClass();
        $elements = collect([
            $this->colaboracio(1, 'Empresa Beta', 'Cicle B', 1),
            $this->colaboracio(2, 'Empresa Alfa', 'Cicle A', 2),
            $this->colaboracio(3, 'Empresa Alfa', 'Cicle C', 3),
        ]);

        $panel = Mockery::mock();
        $panel->shouldReceive('getElementos')
            ->with($pestana)
            ->andReturn($elements);

        $html = Blade::render(
            <<<'BLADE'
                @include('intranet.partials.colaboracion.departamento')
                @stack('styles')
            BLADE,
            compact('panel', 'pestana')
        );

        $this->assertSame(2, substr_count($html, 'class="colaboracion-company-block"'));
        $this->assertSame(3, substr_count($html, 'colaboracion-company-item text-decoration-none'));
        $this->assertStringContainsString('data-company="Empresa Alfa"', $html);
        $this->assertStringContainsString('data-company="Empresa Beta"', $html);
        $this->assertMatchesRegularExpression('/2\s+col·laboracions/u', $html);
        $this->assertMatchesRegularExpression('/1\s+col·laboració/u', $html);
        $this->assertLessThan(
            strpos($html, 'data-company="Empresa Beta"'),
            strpos($html, 'data-company="Empresa Alfa"')
        );
    }

    /**
     * Crea les dades mínimes d'una col·laboració per a renderitzar el panell.
     */
    private function colaboracio(int $id, string $empresa, string $cicle, int $estat): object
    {
        return (object) [
            'id' => $id,
            'idCiclo' => $id,
            'localidad' => 'ALCOI',
            'empresa' => $empresa,
            'estado' => $estat,
            'profesor' => 'Tutor ' . $id,
            'tutor' => 'TUTOR' . $id,
            'ultimaActividad' => null,
            'estatPreparacioKey' => 'preparada',
            'fctsAssociadesCount' => 0,
            'Ciclo' => (object) ['literal' => $cicle],
        ];
    }
}
