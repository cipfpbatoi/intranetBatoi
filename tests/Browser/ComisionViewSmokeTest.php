<?php

namespace Tests\Browser;

use Intranet\Entities\Profesor;
use Illuminate\Support\Str;
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

        $login = $this->prepareProfesorForUiLogin($profesor);

        $this->browse(function (Browser $browser) use ($login) {
            $browser->visit('/profesor/login')
                ->type('codigo', $login['identifier'])
                ->type('password', $login['password'])
                ->press('Entra')
                ->pause(1500)
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

    /**
     * Prepara credencials estables per a login via formulari web en Dusk.
     *
     * @return array{identifier:string,password:string}
     */
    private function prepareProfesorForUiLogin(Profesor $profesor): array
    {
        $plainPassword = 'DuskPass_2026';
        $identifier = trim((string) ($profesor->email ?? ''));

        if ($identifier === '') {
            $identifier = 'dusk.'.strtolower((string) $profesor->dni).'@test.local';
            $profesor->email = $identifier;
        }

        $profesor->password = bcrypt($plainPassword);
        $profesor->changePassword = (string) ($profesor->changePassword ?: now()->toDateString());
        $profesor->activo = 1;

        if (!is_string($profesor->remember_token) || $profesor->remember_token === '') {
            $profesor->remember_token = Str::random(20);
        }

        $profesor->save();

        return [
            'identifier' => $identifier,
            'password' => $plainPassword,
        ];
    }
}
