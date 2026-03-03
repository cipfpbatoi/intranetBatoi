<?php

namespace Tests\Browser;

use Intranet\Entities\Profesor;
use Illuminate\Support\Str;
use Laravel\Dusk\Browser;
use Throwable;
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
            $this->loginViaUiWithRetry($browser, $login['identifier'], $login['password']);

            $browser->script("window.location.href = '/comision';");
            $browser->pause(2000);

            $path = $browser->driver->executeScript('return window.location.pathname;');
            $this->assertSame('/comision', $path);
        });
    }

    /**
     * Login robust amb reintents per a errors transitòris de Selenium/xarxa.
     */
    private function loginViaUiWithRetry(Browser $browser, string $identifier, string $password): void
    {
        $lastError = null;
        for ($attempt = 1; $attempt <= 3; $attempt++) {
            try {
                $browser->visit('/profesor/login')
                    ->waitFor('input[name="codigo"]', 10)
                    ->type('codigo', $identifier)
                    ->type('password', $password)
                    ->press('Entra')
                    ->pause(1400)
                    ->visit('/home')
                    ->waitUsing(10, 200, function () use ($browser): bool {
                        $path = (string) ($browser->driver->executeScript('return window.location.pathname;') ?? '');
                        return $path === '/home';
                    });

                return;
            } catch (Throwable $exception) {
                $lastError = $exception;
                $browser->pause(1200);
            }
        }

        if ($lastError instanceof Throwable) {
            throw $lastError;
        }

        $this->fail('No s\'ha pogut completar el login UI en els reintents previstos.');
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
