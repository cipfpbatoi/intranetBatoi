<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

/**
 * Smoke tests d'accés públic per validar infraestructura Dusk.
 */
class PublicAccessSmokeTest extends DuskTestCase
{
    /**
     * Comprova que la pàgina inicial carrega i renderitza el cos HTML.
     */
    public function test_home_page_loads_body(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->pause(1000)
                ->assertPathIs('/')
                ->assertTitleContains('Pantalla Login');
        });
    }
}
