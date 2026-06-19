<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Blade;
use Intranet\Entities\Lote;
use Intranet\UI\Panels\Panel;
use Tests\TestCase;

/**
 * Proves de regressió de la graella de lots de direcció.
 */
class LoteIndexViewTest extends TestCase
{
    public function test_index_de_lots_renderitza_files_del_panell(): void
    {
        $panel = new Panel(
            'Lote',
            ['registre', 'proveedor', 'factura', 'procedencia', 'estado', 'fechaAlta', 'departamento']
        );
        $pestana = $panel->getPestanas()[0];

        $lote = new Lote([
            'registre' => 'LOT-001',
            'proveedor' => 'Proveïdor test',
            'factura' => 'FAC-001',
            'procedencia' => 0,
            'fechaAlta' => '2026-06-19',
        ]);
        $lote->setAttribute('articulo_lote_count', 0);
        $lote->setAttribute('materiales_count', 0);
        $lote->setAttribute('materiales_invent_count', 0);

        $panel->setElementos(new Collection([$lote]));

        $view = file_get_contents(resource_path('views/lote/index.blade.php'));
        $html = Blade::render(
            '<x-grid.table :panel="$panel" :pestana="$pestana" :elementos="$panel->getElementos($pestana)" />',
            compact('panel', 'pestana')
        );

        $this->assertStringContainsString(':elementos="$panel->getElementos($pestana)"', (string) $view);
        $this->assertStringNotContainsString(':mostrarBody="false"', (string) $view);
        $this->assertStringContainsString('<tbody>', $html);
        $this->assertStringContainsString('LOT-001', $html);
        $this->assertStringContainsString('FAC-001', $html);
    }
}
