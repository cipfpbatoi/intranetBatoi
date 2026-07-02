<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Blade;
use Intranet\Entities\Departamento;
use Intranet\Entities\Lote;
use Intranet\Http\Controllers\LoteController;
use Tests\TestCase;

/**
 * Proves de regressió de la graella de lots de direcció.
 */
class LoteIndexViewTest extends TestCase
{
    public function test_index_de_lots_renderitza_files_del_panell(): void
    {
        $this->autenticaUsuariAmbAccesLote();

        $lote = new Lote([
            'registre' => 'LOT-001',
            'proveedor' => 'Proveïdor test',
            'factura' => 'FAC-001',
            'procedencia' => 0,
            'fechaAlta' => '2026-06-19',
        ]);
        $lote->setRelation('Departamento', new Departamento(['vliteral' => 'Informàtica']));
        $lote->setAttribute('articulo_lote_count', 0);
        $lote->setAttribute('materiales_count', 0);
        $lote->setAttribute('materiales_invent_count', 0);

        $controller = new LoteController();
        $this->callProtectedMethod($controller, 'iniBotones');
        $panel = $this->getProtectedProperty($controller, 'panel');
        $pestana = $panel->getPestanas()[0];
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
        $this->assertStringContainsString('Informàtica', $html);
        $this->assertStringContainsString('/direccion/lote/LOT-001/edit', $html);
        $this->assertStringContainsString('/direccion/lote/LOT-001/capture', $html);
        $this->assertStringContainsString('/direccion/lote/LOT-001/print', $html);
        $this->assertStringContainsString('BUIDA', $html);
        $this->assertStringContainsString('Desconocido', $html);
        $this->assertStringContainsString('class="edit imgButton"', $html);
        $this->assertStringContainsString('class="ver imgButton"', $html);
        $this->assertStringContainsString('class="delete imgButton"', $html);
        $this->assertStringNotContainsString('class="inventary imgButton"', $html);
    }

    public function test_lot_en_alta_mostra_inventariar_i_amaga_capturar(): void
    {
        $this->autenticaUsuariAmbAccesLote();

        $lote = new Lote([
            'registre' => 'LOT-002',
            'proveedor' => 'Proveïdor test',
            'factura' => 'FAC-002',
            'procedencia' => 2,
            'fechaAlta' => '2026-06-19',
        ]);
        $lote->setRelation('Departamento', new Departamento(['vliteral' => 'Informàtica']));
        $lote->setAttribute('articulo_lote_count', 1);
        $lote->setAttribute('materiales_count', 0);
        $lote->setAttribute('materiales_invent_count', 0);

        $controller = new LoteController();
        $this->callProtectedMethod($controller, 'iniBotones');
        $panel = $this->getProtectedProperty($controller, 'panel');
        $pestana = $panel->getPestanas()[0];
        $panel->setElementos(new Collection([$lote]));

        $html = Blade::render(
            '<x-grid.table :panel="$panel" :pestana="$pestana" :elementos="$panel->getElementos($pestana)" />',
            compact('panel', 'pestana')
        );

        $this->assertStringContainsString('ALTA', $html);
        $this->assertStringContainsString('Compra', $html);
        $this->assertStringContainsString('class="inventary imgButton"', $html);
        $this->assertStringContainsString('class="delete imgButton"', $html);
        $this->assertStringNotContainsString('/direccion/lote/LOT-002/capture', $html);
    }

    private function autenticaUsuariAmbAccesLote(): void
    {
        auth('profesor')->setUser(new class () implements Authenticatable {
            public string $dni = '00000000T';
            public int $rol = 22;

            public function getAuthIdentifierName(): string
            {
                return 'dni';
            }

            public function getAuthIdentifier(): string
            {
                return $this->dni;
            }

            public function getAuthPasswordName(): string
            {
                return 'password';
            }

            public function getAuthPassword(): string
            {
                return '';
            }

            public function getRememberToken(): ?string
            {
                return null;
            }

            public function setRememberToken($value): void
            {
            }

            public function getRememberTokenName(): string
            {
                return 'remember_token';
            }
        });
    }
}
