<?php

namespace Tests\Browser;

use Intranet\Entities\Profesor;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

/**
 * Smoke test de la vista de comissió en entorn autenticat.
 */
class ComisionViewSmokeTest extends DuskTestCase
{
    /**
     * Verifica que un professor autenticat pot carregar la vista /comision.
     */
    public function test_profesor_can_open_comision_index_view(): void
    {
        $profesor = $this->profesorForSmoke();
        if ($profesor === null) {
            return;
        }

        $this->browse(function (Browser $browser) use ($profesor) {
            $browser->loginAs($profesor, 'profesor')
                ->visit('/home')
                ->pause(1200)
                ->assertDontSee('Pantalla Login');

            $browser->script("window.location.href = '/comision';");
            $browser->pause(2000);

            $path = $browser->driver->executeScript('return window.location.pathname;');
            $this->assertSame('/comision', $path);
        });
    }

    /**
     * Selecciona un professor actiu per a proves de navegació autenticada.
     */
    private function profesorForSmoke(): ?Profesor
    {
        $profesor = Profesor::query()
            ->where('activo', 1)
            ->where('rol', '>', 0)
            ->first();

        if ($profesor === null) {
            $this->markTestSkipped('No hi ha professor actiu per executar la prova de comissió.');
            return null;
        }

        return $profesor;
    }
}
