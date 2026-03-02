<?php

namespace Tests\Browser;

use Intranet\Entities\Profesor;
use Illuminate\Support\Str;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

/**
 * Proves Dusk de fluxos d'autenticació pendents en migració legacy -> Bearer.
 */
class ApiPendingAuthFlowTest extends DuskTestCase
{
    /**
     * Dropzone ha d'acceptar Bearer de sessió web (meta token) en getAttached.
     */
    public function test_dropzone_get_attached_accepta_bearer_despres_login_web(): void
    {
        $profesor = $this->profesorForBrowserAuthOrSkip();
        if ($profesor === null) {
            return;
        }

        $login = $this->prepareProfesorForUiLogin($profesor);

        $this->browse(function (Browser $browser) use ($login, $profesor) {
            $this->loginViaUi($browser, $login['identifier'], $login['password']);

            $token = $this->currentMetaBearer($browser);
            $this->assertNotSame('', $token, 'No s\'ha trobat user-bearer-token en meta després de login.');

            $response = $this->fetchJson(
                $browser,
                '/api/getAttached/profesor/'.rawurlencode((string) $profesor->dni),
                'GET',
                null,
                ['Authorization' => 'Bearer '.$token]
            );

            $this->assertSame(200, $response['status'] ?? null);
            $this->assertTrue((bool) ($response['json']['success'] ?? false));
            $this->assertIsArray($response['json']['data'] ?? null);
        });
    }

    /**
     * Dropzone ja no ha d'acceptar només query api_token (sense Bearer).
     */
    public function test_dropzone_get_attached_rebutja_legacy_api_token_sense_bearer(): void
    {
        $profesor = $this->profesorWithLegacyTokenOrSkip();
        if ($profesor === null) {
            return;
        }

        $this->browse(function (Browser $browser) use ($profesor) {
            $browser->visit('/');

            $response = $this->fetchJson(
                $browser,
                '/api/getAttached/profesor/'.rawurlencode((string) $profesor->dni)
                .'?api_token='.rawurlencode((string) $profesor->api_token)
            );

            $this->assertSame(401, $response['status'] ?? null);
        });
    }

    /**
     * En endpoints mixtos, Bearer ha de passar autenticació i arribar al controlador.
     */
    public function test_colaboracion_switch_amb_bearer_arriba_a_controlador_i_retorna_404(): void
    {
        $profesor = $this->profesorForBrowserAuthOrSkip();
        if ($profesor === null) {
            return;
        }

        $login = $this->prepareProfesorForUiLogin($profesor);

        $this->browse(function (Browser $browser) use ($login) {
            $this->loginViaUi($browser, $login['identifier'], $login['password']);

            $token = $this->currentMetaBearer($browser);
            $this->assertNotSame('', $token, 'No s\'ha trobat user-bearer-token en meta després de login.');

            $response = $this->fetchJson(
                $browser,
                '/api/colaboracion/999999/switch',
                'GET',
                null,
                ['Authorization' => 'Bearer '.$token]
            );

            $this->assertSame(404, $response['status'] ?? null);
            $this->assertFalse((bool) ($response['json']['success'] ?? true));
        });
    }

    /**
     * En colaboracion/switch (strict Sanctum), legacy api_token sense Bearer es rebutja.
     */
    public function test_colaboracion_switch_rebutja_legacy_api_token_sense_bearer(): void
    {
        $profesor = $this->profesorWithLegacyTokenOrSkip();
        if ($profesor === null) {
            return;
        }

        $this->browse(function (Browser $browser) use ($profesor) {
            $browser->visit('/');

            $response = $this->fetchJson(
                $browser,
                '/api/colaboracion/999999/switch?api_token='.rawurlencode((string) $profesor->api_token)
            );

            $this->assertSame(401, $response['status'] ?? null);
        });
    }

    /**
     * En reserva (mode mixt), Bearer ha d'autenticar i arribar al controlador.
     */
    public function test_reserva_show_amb_bearer_retorna_404_i_no_401(): void
    {
        $profesor = $this->profesorForBrowserAuthOrSkip();
        if ($profesor === null) {
            return;
        }

        $login = $this->prepareProfesorForUiLogin($profesor);

        $this->browse(function (Browser $browser) use ($login) {
            $this->loginViaUi($browser, $login['identifier'], $login['password']);

            $token = $this->currentMetaBearer($browser);
            $this->assertNotSame('', $token, 'No s\'ha trobat user-bearer-token en meta després de login.');

            $response = $this->fetchJson(
                $browser,
                '/api/reserva/999999',
                'GET',
                null,
                ['Authorization' => 'Bearer '.$token]
            );

            $this->assertSame(404, $response['status'] ?? null);
            $this->assertNotSame(401, $response['status'] ?? null);
        });
    }

    /**
     * En reserva (mode mixt), legacy api_token encara s'accepta temporalment.
     */
    public function test_reserva_show_accepta_legacy_api_token_en_mode_mixt(): void
    {
        $profesor = $this->profesorWithLegacyTokenOrSkip();
        if ($profesor === null) {
            return;
        }

        $this->browse(function (Browser $browser) use ($profesor) {
            $browser->visit('/');

            $response = $this->fetchJson(
                $browser,
                '/api/reserva/999999?api_token='.rawurlencode((string) $profesor->api_token)
            );

            $this->assertSame(404, $response['status'] ?? null);
            $this->assertNotSame(401, $response['status'] ?? null);
        });
    }

    /**
     * En actividad/edit (ara strict Sanctum), legacy api_token sense Bearer es rebutja.
     */
    public function test_actividad_edit_rebutja_legacy_api_token_sense_bearer(): void
    {
        $profesor = $this->profesorWithLegacyTokenOrSkip();
        if ($profesor === null) {
            return;
        }

        $this->browse(function (Browser $browser) use ($profesor) {
            $browser->visit('/');

            $response = $this->fetchJson(
                $browser,
                '/api/actividad/999999/edit?api_token='.rawurlencode((string) $profesor->api_token)
            );

            $this->assertSame(401, $response['status'] ?? null);
        });
    }

    /**
     * En actividad/edit (strict Sanctum), Bearer ha d'arribar al controlador.
     */
    public function test_actividad_edit_amb_bearer_retorna_404_i_no_401(): void
    {
        $profesor = $this->profesorForBrowserAuthOrSkip();
        if ($profesor === null) {
            return;
        }

        $login = $this->prepareProfesorForUiLogin($profesor);

        $this->browse(function (Browser $browser) use ($login) {
            $this->loginViaUi($browser, $login['identifier'], $login['password']);

            $token = $this->currentMetaBearer($browser);
            $this->assertNotSame('', $token, 'No s\'ha trobat user-bearer-token en meta després de login.');

            $response = $this->fetchJson(
                $browser,
                '/api/actividad/999999/edit',
                'GET',
                null,
                ['Authorization' => 'Bearer '.$token]
            );

            $this->assertSame(404, $response['status'] ?? null);
            $this->assertFalse((bool) ($response['json']['success'] ?? true));
        });
    }

    /**
     * Material (ara strict Sanctum) ha d'acceptar Bearer.
     */
    public function test_material_show_amb_bearer_retorna_404_i_no_401(): void
    {
        $profesor = $this->profesorForBrowserAuthOrSkip();
        if ($profesor === null) {
            return;
        }

        $login = $this->prepareProfesorForUiLogin($profesor);

        $this->browse(function (Browser $browser) use ($login) {
            $this->loginViaUi($browser, $login['identifier'], $login['password']);

            $token = $this->currentMetaBearer($browser);
            $this->assertNotSame('', $token, 'No s\'ha trobat user-bearer-token en meta després de login.');

            $response = $this->fetchJson(
                $browser,
                '/api/material/999999',
                'GET',
                null,
                ['Authorization' => 'Bearer '.$token]
            );

            $this->assertSame(404, $response['status'] ?? null);
            $this->assertFalse((bool) ($response['json']['success'] ?? true));
        });
    }

    /**
     * Material (strict Sanctum) rebutja legacy api_token sense Bearer.
     */
    public function test_material_show_rebutja_legacy_api_token_sense_bearer(): void
    {
        $profesor = $this->profesorWithLegacyTokenOrSkip();
        if ($profesor === null) {
            return;
        }

        $this->browse(function (Browser $browser) use ($profesor) {
            $browser->visit('/');

            $response = $this->fetchJson(
                $browser,
                '/api/material/999999?api_token='.rawurlencode((string) $profesor->api_token)
            );

            $this->assertSame(401, $response['status'] ?? null);
        });
    }

    /**
     * Dropzone remove ja no ha d'acceptar query api_token sense Bearer.
     */
    public function test_dropzone_remove_rebutja_legacy_api_token_sense_bearer(): void
    {
        $profesor = $this->profesorWithLegacyTokenOrSkip();
        if ($profesor === null) {
            return;
        }

        $this->browse(function (Browser $browser) use ($profesor) {
            $browser->visit('/');

            $response = $this->fetchJson(
                $browser,
                '/api/removeAttached/profesor/'.rawurlencode((string) $profesor->dni).'/fitxer-test'
                .'?api_token='.rawurlencode((string) $profesor->api_token)
            );

            $this->assertSame(401, $response['status'] ?? null);
        });
    }

    /**
     * Dropzone remove amb Bearer ha d'arribar al controlador (no 401).
     */
    public function test_dropzone_remove_amb_bearer_no_retorna_401(): void
    {
        $profesor = $this->profesorForBrowserAuthOrSkip();
        if ($profesor === null) {
            return;
        }

        $login = $this->prepareProfesorForUiLogin($profesor);

        $this->browse(function (Browser $browser) use ($login, $profesor) {
            $this->loginViaUi($browser, $login['identifier'], $login['password']);

            $token = $this->currentMetaBearer($browser);
            $this->assertNotSame('', $token, 'No s\'ha trobat user-bearer-token en meta després de login.');

            $response = $this->fetchJson(
                $browser,
                '/api/removeAttached/profesor/'.rawurlencode((string) $profesor->dni).'/fitxer-inexistent',
                'GET',
                null,
                ['Authorization' => 'Bearer '.$token]
            );

            $this->assertNotSame(401, $response['status'] ?? null);
        });
    }

    /**
     * auth/me (strict Sanctum) ha d'acceptar Bearer de sessió web.
     */
    public function test_auth_me_amb_bearer_despres_login_web_retorna_200(): void
    {
        $profesor = $this->profesorForBrowserAuthOrSkip();
        if ($profesor === null) {
            return;
        }

        $login = $this->prepareProfesorForUiLogin($profesor);

        $this->browse(function (Browser $browser) use ($login, $profesor) {
            $this->loginViaUi($browser, $login['identifier'], $login['password']);

            $token = $this->currentMetaBearer($browser);
            $this->assertNotSame('', $token, 'No s\'ha trobat user-bearer-token en meta després de login.');

            $response = $this->fetchJson(
                $browser,
                '/api/auth/me',
                'GET',
                null,
                ['Authorization' => 'Bearer '.$token]
            );

            $this->assertSame(200, $response['status'] ?? null);
            $this->assertTrue((bool) ($response['json']['success'] ?? false));
            $this->assertSame((string) $profesor->dni, (string) ($response['json']['data']['dni'] ?? ''));
        });
    }

    /**
     * auth/me (strict Sanctum) ha de rebutjar query api_token sense Bearer.
     */
    public function test_auth_me_rebutja_legacy_api_token_sense_bearer(): void
    {
        $profesor = $this->profesorWithLegacyTokenOrSkip();
        if ($profesor === null) {
            return;
        }

        $this->browse(function (Browser $browser) use ($profesor) {
            $browser->visit('/');

            $response = $this->fetchJson(
                $browser,
                '/api/auth/me?api_token='.rawurlencode((string) $profesor->api_token)
            );

            $this->assertSame(401, $response['status'] ?? null);
        });
    }

    /**
     * auth/logout (strict Sanctum) ha de revocar amb Bearer vàlid.
     */
    public function test_auth_logout_amb_bearer_retorna_200(): void
    {
        $profesor = $this->profesorForBrowserAuthOrSkip();
        if ($profesor === null) {
            return;
        }

        $login = $this->prepareProfesorForUiLogin($profesor);

        $this->browse(function (Browser $browser) use ($login) {
            $this->loginViaUi($browser, $login['identifier'], $login['password']);

            $token = $this->currentMetaBearer($browser);
            $this->assertNotSame('', $token, 'No s\'ha trobat user-bearer-token en meta després de login.');

            $response = $this->fetchJson(
                $browser,
                '/api/auth/logout',
                'POST',
                null,
                ['Authorization' => 'Bearer '.$token]
            );

            $this->assertSame(200, $response['status'] ?? null);
            $this->assertTrue((bool) ($response['json']['success'] ?? false));
        });
    }

    /**
     * Falta (mode mixt) accepta Bearer i arriba al controlador.
     */
    public function test_falta_show_amb_bearer_retorna_404_i_no_401(): void
    {
        $profesor = $this->profesorForBrowserAuthOrSkip();
        if ($profesor === null) {
            return;
        }

        $login = $this->prepareProfesorForUiLogin($profesor);

        $this->browse(function (Browser $browser) use ($login) {
            $this->loginViaUi($browser, $login['identifier'], $login['password']);

            $token = $this->currentMetaBearer($browser);
            $this->assertNotSame('', $token, 'No s\'ha trobat user-bearer-token en meta després de login.');

            $response = $this->fetchJson(
                $browser,
                '/api/falta/999999',
                'GET',
                null,
                ['Authorization' => 'Bearer '.$token]
            );

            $this->assertSame(404, $response['status'] ?? null);
            $this->assertNotSame(401, $response['status'] ?? null);
        });
    }

    /**
     * Falta (strict Sanctum) rebutja api_token legacy sense Bearer.
     */
    public function test_falta_show_rebutja_legacy_api_token_sense_bearer(): void
    {
        $profesor = $this->profesorWithLegacyTokenOrSkip();
        if ($profesor === null) {
            return;
        }

        $this->browse(function (Browser $browser) use ($profesor) {
            $browser->visit('/');

            $response = $this->fetchJson(
                $browser,
                '/api/falta/999999?api_token='.rawurlencode((string) $profesor->api_token)
            );

            $this->assertSame(401, $response['status'] ?? null);
        });
    }

    /**
     * Expediente (mode mixt) accepta Bearer i arriba al controlador.
     */
    public function test_expediente_show_amb_bearer_retorna_404_i_no_401(): void
    {
        $profesor = $this->profesorForBrowserAuthOrSkip();
        if ($profesor === null) {
            return;
        }

        $login = $this->prepareProfesorForUiLogin($profesor);

        $this->browse(function (Browser $browser) use ($login) {
            $this->loginViaUi($browser, $login['identifier'], $login['password']);

            $token = $this->currentMetaBearer($browser);
            $this->assertNotSame('', $token, 'No s\'ha trobat user-bearer-token en meta després de login.');

            $response = $this->fetchJson(
                $browser,
                '/api/expediente/999999',
                'GET',
                null,
                ['Authorization' => 'Bearer '.$token]
            );

            $this->assertSame(404, $response['status'] ?? null);
            $this->assertNotSame(401, $response['status'] ?? null);
        });
    }

    /**
     * Expediente (strict Sanctum) rebutja api_token legacy sense Bearer.
     */
    public function test_expediente_show_rebutja_legacy_api_token_sense_bearer(): void
    {
        $profesor = $this->profesorWithLegacyTokenOrSkip();
        if ($profesor === null) {
            return;
        }

        $this->browse(function (Browser $browser) use ($profesor) {
            $browser->visit('/');

            $response = $this->fetchJson(
                $browser,
                '/api/expediente/999999?api_token='.rawurlencode((string) $profesor->api_token)
            );

            $this->assertSame(401, $response['status'] ?? null);
        });
    }

    /**
     * Comision/edit (mode mixt) accepta Bearer i arriba al controlador.
     */
    public function test_comision_edit_amb_bearer_retorna_404_i_no_401(): void
    {
        $profesor = $this->profesorForBrowserAuthOrSkip();
        if ($profesor === null) {
            return;
        }

        $login = $this->prepareProfesorForUiLogin($profesor);

        $this->browse(function (Browser $browser) use ($login) {
            $this->loginViaUi($browser, $login['identifier'], $login['password']);

            $token = $this->currentMetaBearer($browser);
            $this->assertNotSame('', $token, 'No s\'ha trobat user-bearer-token en meta després de login.');

            $response = $this->fetchJson(
                $browser,
                '/api/comision/999999/edit',
                'GET',
                null,
                ['Authorization' => 'Bearer '.$token]
            );

            $this->assertSame(404, $response['status'] ?? null);
            $this->assertNotSame(401, $response['status'] ?? null);
        });
    }

    /**
     * Comision/edit (strict Sanctum) rebutja api_token legacy sense Bearer.
     */
    public function test_comision_edit_rebutja_legacy_api_token_sense_bearer(): void
    {
        $profesor = $this->profesorWithLegacyTokenOrSkip();
        if ($profesor === null) {
            return;
        }

        $this->browse(function (Browser $browser) use ($profesor) {
            $browser->visit('/');

            $response = $this->fetchJson(
                $browser,
                '/api/comision/999999/edit?api_token='.rawurlencode((string) $profesor->api_token)
            );

            $this->assertSame(401, $response['status'] ?? null);
        });
    }

    /**
     * Selecciona professor actiu per a login UI i consum API amb Bearer.
     */
    private function profesorForBrowserAuthOrSkip(): ?Profesor
    {
        $profesor = Profesor::query()
            ->where('activo', 1)
            ->where('rol', '>', 0)
            ->first();

        if ($profesor === null) {
            $this->markTestSkipped('No hi ha professor actiu per executar proves browser d\'autenticació.');
            return null;
        }

        return $profesor;
    }

    /**
     * Selecciona professor amb api_token legacy per validar rebutjos sense Bearer.
     */
    private function profesorWithLegacyTokenOrSkip(): ?Profesor
    {
        $profesor = Profesor::query()
            ->whereNotNull('api_token')
            ->where('api_token', '!=', '')
            ->first();

        if ($profesor === null) {
            $this->markTestSkipped('No hi ha professor amb api_token per provar rebutig legacy.');
            return null;
        }

        return $profesor;
    }

    /**
     * Fa login web per formulari professor.
     */
    private function loginViaUi(Browser $browser, string $identifier, string $password): void
    {
        $browser->visit('/profesor/login')
            ->type('codigo', $identifier)
            ->type('password', $password)
            ->press('Entra')
            ->pause(1400)
            ->assertDontSee('Login Profesor')
            ->visit('/home')
            ->pause(800);
    }

    /**
     * Retorna el bearer token actual injectat en meta pel layout.
     */
    private function currentMetaBearer(Browser $browser): string
    {
        $value = $browser->script(
            "return document.querySelector('meta[name=\"user-bearer-token\"]')?.getAttribute('content') || '';"
        )[0] ?? '';

        return is_string($value) ? trim($value) : '';
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

    /**
     * Executa fetch des del navegador i retorna status + JSON.
     *
     * @param array<string, mixed>|null $body
     * @param array<string, string> $headers
     * @return array<string, mixed>
     */
    private function fetchJson(
        Browser $browser,
        string $url,
        string $method = 'GET',
        ?array $body = null,
        array $headers = []
    ): array {
        $methodJson = json_encode($method);
        $urlJson = json_encode($url);
        $bodyJson = json_encode($body);
        $headersJson = json_encode(array_merge([
            'Accept' => 'application/json',
        ], $headers));

        $script = <<<JS
const done = arguments[0];
const method = {$methodJson};
const url = {$urlJson};
const payload = {$bodyJson};
const headers = {$headersJson};

const options = { method, headers, redirect: 'manual' };
if (payload !== null) {
  options.body = JSON.stringify(payload);
  if (!options.headers['Content-Type']) {
    options.headers['Content-Type'] = 'application/json';
  }
}

fetch(url, options)
  .then(async (response) => {
    let text = '';
    try {
      text = await response.text();
    } catch (e) {
      text = '';
    }

    let json = null;
    if (text) {
      try {
        json = JSON.parse(text);
      } catch (e) {
        json = null;
      }
    }

    done({
      status: response.status,
      redirected: response.redirected,
      type: response.type,
      url: response.url,
      text: text,
      json: json
    });
  })
  .catch((error) => done({ error: String(error) }));
JS;

        $result = $browser->driver->executeAsyncScript($script);
        if (!is_array($result)) {
            return ['error' => 'Resposta fetch no vàlida'];
        }

        return $result;
    }
}
