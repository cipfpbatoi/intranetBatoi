<?php

declare(strict_types=1);

namespace Tests\Unit\Views;

use Tests\TestCase;

/**
 * Proves de regressió de la vista d'enquestes de l'alumnat.
 */
class PollSurveyViewTest extends TestCase
{
    /**
     * Verifica que la vista incorpora un botó d'enviament de reserva.
     */
    public function test_poll_survey_view_contains_fallback_submit_button(): void
    {
        $contents = file_get_contents(base_path('resources/views/poll/enquesta.blade.php'));

        $this->assertIsString($contents);
        $this->assertStringContainsString('id="poll-submit-fallback"', $contents);
        $this->assertStringContainsString('type="submit"', $contents);
        $this->assertStringContainsString('Enviar enquesta', $contents);
    }

    /**
     * Verifica que el JavaScript mostra el botó només quan no hi ha action bar del wizard.
     */
    public function test_poll_survey_script_contains_fallback_visibility_logic(): void
    {
        $contents = file_get_contents(base_path('public/js/Poll/create.js'));

        $this->assertIsString($contents);
        $this->assertStringContainsString("document.getElementById('poll-submit-fallback')", $contents);
        $this->assertStringContainsString("document.querySelector('#wizard .actionBar')", $contents);
        $this->assertStringContainsString("fallbackSubmit.style.display = ''", $contents);
    }
}
