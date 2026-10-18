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

    /** El panell departamental agrupa per empresa i combina tots els filtres. */
    public function test_el_panell_departamental_permet_filtres_acumulatius_per_empresa(): void
    {
        $pestana = new \stdClass();
        $elements = collect([
            $this->colaboracio(
                1,
                10,
                'Empresa Beta',
                'CB',
                'Cicle B',
                0,
                localitat: 'Muro de Alcoy'
            ),
            $this->colaboracio(
                2,
                20,
                'Empresa Alfa',
                'CA',
                'Cicle A',
                2,
                'Anna Garcia',
                'Anna Garcia Segon',
                documentacioPendent: true,
                teContacte: true
            ),
            $this->colaboracio(
                3,
                20,
                'Empresa Alfa',
                'CC',
                'Cicle C',
                3,
                'Pau Ferrer',
                'Pau Ferrer Tercer',
                localitat: 'Alcoy',
                teContacte: true
            ),
            $this->colaboracio(
                4,
                30,
                'Empresa Gamma',
                'CD',
                'Cicle D',
                1,
                'Joan Soler',
                'Joan Soler Quart',
                localitat: 'MURO'
            ),
        ]);

        $panel = Mockery::mock();
        $panel->shouldReceive('getElementos')
            ->with($pestana)
            ->andReturn($elements);

        $html = Blade::render(
            <<<'BLADE'
                @include('intranet.partials.colaboracion.departamento')
                @stack('scripts')
            BLADE,
            compact('panel', 'pestana')
        );

        $this->assertSame(3, substr_count($html, 'class="colaboracion-company-card"'));
        $this->assertSame(4, substr_count($html, 'js-colaboracion-show colaboracion-cycle-row'));
        $this->assertSame(1, substr_count($html, 'data-company-key="20"'));
        $this->assertSame(
            1,
            substr_count($html, '<h4 class="colaboracion-company-name" title="Empresa Alfa">')
        );
        $this->assertStringContainsString('data-colaboracion-department-styles', $html);
        $this->assertStringContainsString('@media (min-width: 992px)', $html);
        $this->assertStringContainsString('grid-template-columns: repeat(3, minmax(0, 1fr))', $html);
        $this->assertStringContainsString('linear-gradient(135deg', $html);
        $this->assertStringContainsString('Població', $html);
        $this->assertStringContainsString('id="colaboracion-town-filter"', $html);
        $this->assertStringContainsString('id="colaboracion-cycle-filter"', $html);
        $this->assertStringContainsString('id="colaboracion-state-filter"', $html);
        $this->assertStringContainsString('id="colaboracion-assignment-filter"', $html);
        $this->assertStringContainsString('id="colaboracion-documentation-filter"', $html);
        $this->assertStringContainsString('id="colaboracion-clear-filters"', $html);
        $this->assertStringContainsString('value="alcoi"', $html);
        $this->assertSame(1, substr_count($html, 'value="alcoi"'));
        $this->assertStringContainsString('>Alcoi</option>', $html);
        $this->assertSame(1, substr_count($html, 'value="muro-d-alcoi"'));
        $this->assertSame(3, substr_count($html, 'Muro d’Alcoi'));
        $this->assertStringContainsString('value="no-contact"', $html);
        $this->assertStringContainsString('value="contacted"', $html);
        $this->assertStringContainsString('value="collaborates"', $html);
        $this->assertStringContainsString('value="assigned"', $html);
        $this->assertStringContainsString('value="unassigned"', $html);
        $this->assertStringContainsString('Conveni actualitzat', $html);
        $this->assertStringNotContainsString('Documentació actualitzada', $html);
        $this->assertStringContainsString('data-documentation-current="0"', $html);
        $this->assertStringContainsString('data-documentation-current="1"', $html);
        $this->assertStringContainsString('data-assigned="0"', $html);
        $this->assertStringContainsString('data-assigned="1"', $html);
        $this->assertStringContainsString('data-contacted="0"', $html);
        $this->assertStringContainsString('data-contacted="1"', $html);
        $this->assertStringNotContainsString('El meu cicle', $html);
        $this->assertMatchesRegularExpression('/>\s*CA\s*<\/span>/u', $html);
        $this->assertMatchesRegularExpression('/>\s*CC\s*<\/span>/u', $html);
        $this->assertSame(4, substr_count($html, 'class="badge colaboracion-cycle-badge"'));
        $this->assertStringContainsString('Anna Garcia', $html);
        $this->assertStringNotContainsString('Anna Garcia Segon', $html);
        $this->assertStringContainsString('fa-user-times', $html);
        $this->assertStringContainsString('aria-label="Sense assignar"', $html);
        $this->assertStringContainsString('No contactada', $html);
        $this->assertStringContainsString('No sé', $html);
        $this->assertMatchesRegularExpression(
            '/data-state="1"\s+data-contacted="0"[\s\S]*?No sé/u',
            $html
        );
        $this->assertStringContainsString('Contactada', $html);
        $this->assertStringContainsString('Col·labora', $html);
        $this->assertStringContainsString('No col·labora', $html);
        $this->assertStringNotContainsString('Pendent', $html);
        $this->assertStringNotContainsString('Sense activitat recent', $html);
        $this->assertStringNotContainsString('Última FCT', $html);
        $this->assertStringNotContainsString('data-inactive', $html);
        $this->assertStringContainsString('var matchesFilters = function (row)', $html);
        $this->assertStringContainsString('&& matchesCycle', $html);
        $this->assertStringContainsString('&& matchesState(row)', $html);
        $this->assertStringContainsString('&& matchesDocumentation', $html);
        $this->assertStringContainsString('&& matchesAssignment(row)', $html);
        $this->assertStringContainsString('card.hidden = visibleInCompany === 0;', $html);
        $this->assertStringContainsString('id="colaboracion-visible-companies"', $html);
        $this->assertStringContainsString('id="colaboracion-visible-collaborations"', $html);
    }

    /**
     * Crea les dades mínimes d'una col·laboració per a renderitzar el panell.
     */
    private function colaboracio(
        int $id,
        int $idCentro,
        string $empresa,
        string $cicle,
        string $literal,
        int $estat,
        ?string $nomCurt = null,
        ?string $nomComplet = null,
        bool $documentacioPendent = false,
        string $localitat = 'ALCOI',
        bool $teContacte = false
    ): object {
        return (object) [
            'id' => $id,
            'idCentro' => $idCentro,
            'idCiclo' => $id,
            'localidad' => $localitat,
            'empresa' => $empresa,
            'estado' => $estat,
            'profesor' => $nomComplet ?? '',
            'tutor' => $nomCurt === null ? null : 'TUTOR' . $id,
            'ultimaActividad' => null,
            'teContacte' => $teContacte,
            'estatPreparacioKey' => 'preparada',
            'fctsAssociadesCount' => 0,
            'conveniPendent' => $documentacioPendent,
            'Ciclo' => (object) [
                'ciclo' => $cicle,
                'literal' => $literal,
            ],
            'Propietario' => $nomCurt === null ? null : (object) ['shortName' => $nomCurt],
        ];
    }
}
