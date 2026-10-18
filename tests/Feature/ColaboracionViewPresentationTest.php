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

    /** El panell departamental mostra una graella compacta amb classificacions operatives. */
    public function test_el_panell_departamental_permet_classificar_fitxes_compactes(): void
    {
        $pestana = new \stdClass();
        $elements = collect([
            $this->colaboracio(1, 'Empresa Beta', 'CB', 'Cicle B', 0, inactiva: true),
            $this->colaboracio(
                2,
                'Empresa Alfa',
                'CA',
                'Cicle A',
                2,
                'Anna Garcia',
                'Anna Garcia Segon',
                ciclePropi: true,
                documentacioPendent: true
            ),
            $this->colaboracio(
                3,
                'Empresa Alfa',
                'CC',
                'Cicle C',
                3,
                'Pau Ferrer',
                'Pau Ferrer Tercer',
                inactiva: true,
                ultimaFct: '2023-06-20'
            ),
        ]);

        $panel = Mockery::mock();
        $panel->shouldReceive('getElementos')
            ->with($pestana)
            ->andReturn($elements);

        $html = Blade::render(
            <<<'BLADE'
                @include('intranet.partials.colaboracion.departamento')
                @stack('styles')
                @stack('scripts')
            BLADE,
            compact('panel', 'pestana')
        );

        $this->assertSame(3, substr_count($html, 'js-colaboracion-show colaboracion-department-card'));
        $this->assertStringContainsString('data-company="Empresa Alfa"', $html);
        $this->assertStringContainsString('data-company="Empresa Beta"', $html);
        $this->assertStringContainsString('grid-template-columns: repeat(3, minmax(0, 1fr))', $html);
        $this->assertStringContainsString('data-view="town"', $html);
        $this->assertStringContainsString('data-view="cycle"', $html);
        $this->assertStringContainsString('data-view="contacted"', $html);
        $this->assertStringContainsString('data-view="collaborates"', $html);
        $this->assertStringContainsString('data-view="documentation"', $html);
        $this->assertStringContainsString('data-view="inactive"', $html);
        $this->assertStringContainsString('data-documentation-old="1"', $html);
        $this->assertStringContainsString('data-inactive="1"', $html);
        $this->assertStringContainsString('data-contacted="0"', $html);
        $this->assertStringContainsString('data-collaborates="1"', $html);
        $this->assertStringContainsString('El meu cicle', $html);
        $this->assertMatchesRegularExpression('/>\s*CA\s*<\/span>/u', $html);
        $this->assertMatchesRegularExpression('/>\s*CC\s*<\/span>/u', $html);
        $this->assertStringContainsString('Anna Garcia', $html);
        $this->assertStringNotContainsString('Anna Garcia Segon', $html);
        $this->assertStringContainsString('fa-user-times', $html);
        $this->assertStringContainsString('aria-label="Sense assignar"', $html);
        $this->assertStringNotContainsString('No contactada', $html);
        $this->assertStringContainsString('Col·labora', $html);
        $this->assertStringContainsString('No col·labora', $html);
        $this->assertStringContainsString('Documentació antiga o pendent', $html);
        $this->assertStringContainsString('Última FCT: 20/06/2023', $html);
        $this->assertStringContainsString("renderView('town')", $html);
        $this->assertStringContainsString("card.getAttribute('data-cycle')", $html);
    }

    /**
     * Crea les dades mínimes d'una col·laboració per a renderitzar el panell.
     */
    private function colaboracio(
        int $id,
        string $empresa,
        string $cicle,
        string $literal,
        int $estat,
        ?string $nomCurt = null,
        ?string $nomComplet = null,
        bool $ciclePropi = false,
        bool $documentacioPendent = false,
        bool $inactiva = false,
        ?string $ultimaFct = null
    ): object {
        return (object) [
            'id' => $id,
            'idCiclo' => $id,
            'localidad' => 'ALCOI',
            'empresa' => $empresa,
            'estado' => $estat,
            'profesor' => $nomComplet ?? '',
            'tutor' => $nomCurt === null ? null : 'TUTOR' . $id,
            'ultimaActividad' => null,
            'estatPreparacioKey' => 'preparada',
            'fctsAssociadesCount' => 0,
            'esCiclePropi' => $ciclePropi,
            'conveniPendent' => $documentacioPendent,
            'senseActivitatRecent' => $inactiva,
            'ultimaFctData' => $ultimaFct === null ? null : \Illuminate\Support\Carbon::parse($ultimaFct),
            'Ciclo' => (object) [
                'ciclo' => $cicle,
                'literal' => $literal,
            ],
            'Propietario' => $nomCurt === null ? null : (object) ['shortName' => $nomCurt],
        ];
    }
}
