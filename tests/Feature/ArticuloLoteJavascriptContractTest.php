<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

/**
 * Protegeix el contracte entre el diàleg de materials i l'editor compartit.
 */
class ArticuloLoteJavascriptContractTest extends TestCase
{
    public function test_el_dialeg_pot_invocar_l_editor_compartit_de_files(): void
    {
        $editor = file_get_contents(public_path('js/tabledit.js'));
        $dialog = file_get_contents(public_path('js/ArticuloLote/index.js'));

        $this->assertIsString($editor);
        $this->assertIsString($dialog);
        $this->assertStringContainsString('window.editRow = editRow;', $editor);
        $this->assertStringContainsString("startEdition(this.closest('tr'));", $editor);
        $this->assertStringContainsString('window.editRow.call(editButton, event);', $dialog);
        $this->assertStringContainsString('class="objselect" name="espacio"', $dialog);
    }
}
