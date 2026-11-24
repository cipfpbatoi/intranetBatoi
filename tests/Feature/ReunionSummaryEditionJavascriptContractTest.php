<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

/**
 * Protegeix el contracte d'edició dels resums d'una acta.
 */
class ReunionSummaryEditionJavascriptContractTest extends TestCase
{
    public function test_la_vista_mostra_els_errors_i_l_editor_se_sincronitza_abans_de_guardar(): void
    {
        $view = file_get_contents(resource_path('views/reunion/asistencia.blade.php'));
        $orders = file_get_contents(resource_path('views/reunion/partials/ordenes.blade.php'));
        $editor = file_get_contents(public_path('js/tabledit.js'));

        $this->assertIsString($view);
        $this->assertIsString($orders);
        $this->assertIsString($editor);
        $this->assertStringContainsString('id="error"', $view);
        $this->assertStringContainsString('role="alert"', $view);
        $this->assertStringContainsString("<div class='textarea' name='resumen'>", $orders);
        $this->assertStringContainsString("source.classList.contains('textarea')", $editor);
        $this->assertStringContainsString('function synchroniseRichEditors(row)', $editor);
        $this->assertStringContainsString('synchroniseRichEditors(row);', $editor);
        $this->assertStringContainsString('validationMessage(json)', $editor);

        $richText = file_get_contents(public_path('js/Reunion/richText.js'));

        $this->assertIsString($richText);
        $this->assertStringContainsString("editor.addEventListener('paste'", $richText);
        $this->assertStringContainsString('syncToTextarea(editor, textarea);', $richText);
    }
}
