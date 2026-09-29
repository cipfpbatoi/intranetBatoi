<?php

declare(strict_types=1);

namespace Tests\Feature;

use DOMDocument;
use DOMXPath;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Blade;
use Intranet\Presentation\Crud\ReunionCrudSchema;
use Intranet\Presentation\Crud\TutoriaCrudSchema;
use Intranet\UI\Panels\Panel;
use Tests\TestCase;

/**
 * Proves de renderitzat de taules de grid compatibles amb DataTables.
 */
class GridTableComponentTest extends TestCase
{
    public function test_taula_buida_no_renderitza_fila_placeholder_en_tbody(): void
    {
        $panel = new Panel('Tutoria', TutoriaCrudSchema::GRID_FIELDS);
        $pestana = $panel->getPestanas()[0];

        $html = Blade::render(
            '<x-grid.table :panel="$panel" :pestana="$pestana" :elementos="$elementos" />',
            [
                'panel' => $panel,
                'pestana' => $pestana,
                'elementos' => new Collection(),
            ]
        );

        $document = new DOMDocument();
        @$document->loadHTML($html);

        $rows = (new DOMXPath($document))->query('//table[@id="datatable"]/tbody/tr');

        $this->assertSame(0, $rows?->length);
    }

    public function test_celles_de_data_inclouen_valor_iso_per_ordenacio(): void
    {
        $panel = new Panel('Task', ['vencimiento']);
        $pestana = $panel->getPestanas()[0];
        $elemento = new class () {
            public string $vencimiento = '10-06-2026';

            public function getKey(): int
            {
                return 1;
            }
        };

        $html = Blade::render(
            '<x-grid.table :panel="$panel" :pestana="$pestana" :elementos="$elementos" />',
            [
                'panel' => $panel,
                'pestana' => $pestana,
                'elementos' => new Collection([$elemento]),
            ]
        );

        $document = new DOMDocument();
        @$document->loadHTML($html);

        $dateCell = (new DOMXPath($document))->query('//table[@id="datatable"]/tbody/tr/td[1]')->item(0);

        $this->assertSame('2026-06-10', $dateCell?->getAttribute('data-order'));
        $this->assertStringContainsString('10-06-2026', $dateCell?->textContent ?? '');
    }

    public function test_taula_de_task_ordena_per_id_descendent_per_defecte(): void
    {
        $panel = new Panel('Task', ['id', 'vencimiento']);
        $pestana = $panel->getPestanas()[0];

        $html = Blade::render(
            '<x-grid.table :panel="$panel" :pestana="$pestana" :elementos="$elementos" />',
            [
                'panel' => $panel,
                'pestana' => $pestana,
                'elementos' => new Collection(),
            ]
        );

        $document = new DOMDocument();
        @$document->loadHTML($html);

        $table = (new DOMXPath($document))->query('//table[@id="datatable"]')->item(0);

        $this->assertSame('[[0,"desc"]]', $table?->getAttribute('data-order'));
    }

    public function test_taula_de_lote_ordena_per_data_i_registre_descendent_per_defecte(): void
    {
        $panel = new Panel('Lote', ['registre', 'proveedor', 'factura', 'procedencia', 'estado', 'fechaAlta', 'departament']);
        $pestana = $panel->getPestanas()[0];

        $html = Blade::render(
            '<x-grid.table :panel="$panel" :pestana="$pestana" :elementos="$elementos" />',
            [
                'panel' => $panel,
                'pestana' => $pestana,
                'elementos' => new Collection(),
            ]
        );

        $document = new DOMDocument();
        @$document->loadHTML($html);

        $table = (new DOMXPath($document))->query('//table[@id="datatable"]')->item(0);

        $this->assertSame('[[5,"desc"],[0,"desc"]]', $table?->getAttribute('data-order'));
    }

    public function test_graella_de_reunions_mostra_el_numero_visible_de_l_acta(): void
    {
        app()->setLocale('ca');
        $panel = new Panel('Reunion', ReunionCrudSchema::GRID_FIELDS);
        $pestana = $panel->getPestanas()[0];
        $elemento = new class () {
            public string $XGrupo = '1r DAM';
            public string $XTipo = 'Avaluació';
            public int $numero_acta = 4;
            public string $descripcion = 'Segona avaluació';
            public string $fecha = '10-06-2026';
            public string $curso = '2026-2027';
            public int $id = 1;

            public function getKey(): int
            {
                return $this->id;
            }
        };

        $html = Blade::render(
            '<x-grid.table :panel="$panel" :pestana="$pestana" :elementos="$elementos" />',
            [
                'panel' => $panel,
                'pestana' => $pestana,
                'elementos' => new Collection([$elemento]),
            ]
        );

        $document = new DOMDocument();
        @$document->loadHTML($html);
        $number = (new DOMXPath($document))->query('//span[@name="numero_acta"]')->item(0);

        $this->assertStringContainsString("Número d'acta", html_entity_decode($html));
        $this->assertSame('4', trim($number?->textContent ?? ''));
        $this->assertStringNotContainsString('name="Xnumero"', $html);
    }

    public function test_nom_edat_d_alumno_fct_renderitza_icona_controlada_sense_escapar_html(): void
    {
        $panel = new Panel('AlumnoFct', ['NomEdat']);
        $pestana = $panel->getPestanas()[0];
        $elemento = new class () {
            public string $class = '';
            public string $NomEdat = 'Alba &lt;script&gt; <em class=\'fa fa-child\' aria-hidden=\'true\'></em>';

            public function getKey(): int
            {
                return 1;
            }
        };

        $html = Blade::render(
            '<x-grid.table :panel="$panel" :pestana="$pestana" :elementos="$elementos" />',
            [
                'panel' => $panel,
                'pestana' => $pestana,
                'elementos' => new Collection([$elemento]),
            ]
        );

        $this->assertStringContainsString("<em class='fa fa-child' aria-hidden='true'></em>", $html);
        $this->assertStringNotContainsString('&lt;em class=', $html);
        $this->assertStringContainsString('Alba &lt;script&gt;', $html);
    }
}
