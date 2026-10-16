<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

/**
 * Proves de la llista compacta de documents agrupats.
 */
class DocumentoGroupedListViewTest extends TestCase
{
    /** Comprova la informació principal, el color i les accions de Direcció. */
    public function test_mostra_una_fila_compacta_amb_accions_de_direccio(): void
    {
        $html = view('components.note', [
            'name' => 'FCT',
            'title' => '2026-2027',
            'message' => 'Document de seguiment',
            'color' => 'green',
            'linkShow' => '/documento/12/show',
            'linkEdit' => '/documento/12/edit',
        ])->render();

        $this->assertStringContainsString('class="documento-ticket" data-color="green"', $html);
        $this->assertStringContainsString('Document de seguiment', $html);
        $this->assertStringContainsString('2026-2027', $html);
        $this->assertStringContainsString('/documento/12/show', $html);
        $this->assertStringContainsString('/documento/12/edit', $html);
    }

    /** Comprova que els docents no reben l'acció d'edició. */
    public function test_no_mostra_l_edicio_quan_no_esta_autoritzada(): void
    {
        $html = view('components.note', [
            'name' => 'FCT',
            'title' => null,
            'message' => 'Document de seguiment',
            'color' => 'green',
            'linkShow' => '/documento/12/show',
            'linkEdit' => '#',
        ])->render();

        $this->assertStringContainsString('/documento/12/show', $html);
        $this->assertStringNotContainsString('Editar document', $html);
    }
}
