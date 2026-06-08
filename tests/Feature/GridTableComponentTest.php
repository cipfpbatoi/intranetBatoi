<?php

declare(strict_types=1);

namespace Tests\Feature;

use DOMDocument;
use DOMXPath;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Blade;
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
}
