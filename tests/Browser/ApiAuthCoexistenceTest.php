<?php

namespace Tests\Browser;

use Intranet\Entities\Profesor;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

/**
 * Proves Dusk de coexistència entre autenticació legacy (`api_token`) i Bearer.
 */
class ApiAuthCoexistenceTest extends DuskTestCase
{
    /**
     * Verifica que l'endpoint exchange torna un access token Bearer vàlid.
     */
    public function test_exchange_returns_bearer_token_with_valid_legacy_token(): void
    {
        $profesor = $this->legacyProfesorOrSkip();
        if ($profesor === null) {
            return;
        }

        $this->browse(function (Browser $browser) use ($profesor) {
            $browser->visit('/');

            $result = $this->fetchJson(
                $browser,
                '/api/auth/exchange',
                'POST',
                [
                    'api_token' => (string) $profesor->api_token,
                    'dni' => (string) $profesor->dni,
                    'device_name' => 'dusk-coexistence',
                ]
            );

            $this->assertSame(200, $result['status'] ?? null);
            $this->assertTrue((bool) ($result['json']['success'] ?? false));
            $this->assertSame('Bearer', $result['json']['data']['token_type'] ?? null);
            $this->assertIsString($result['json']['data']['access_token'] ?? null);
            $this->assertNotSame('', (string) ($result['json']['data']['access_token'] ?? ''));
        });
    }

    /**
     * Verifica que el token Bearer emés per exchange autentica correctament /api/auth/me.
     */
    public function test_auth_me_accepts_bearer_from_exchange(): void
    {
        $profesor = $this->legacyProfesorOrSkip();
        if ($profesor === null) {
            return;
        }

        $this->browse(function (Browser $browser) use ($profesor) {
            $browser->visit('/');

            $exchange = $this->fetchJson(
                $browser,
                '/api/auth/exchange',
                'POST',
                [
                    'api_token' => (string) $profesor->api_token,
                    'dni' => (string) $profesor->dni,
                    'device_name' => 'dusk-coexistence-me',
                ]
            );

            $bearer = (string) ($exchange['json']['data']['access_token'] ?? '');
            $this->assertNotSame('', $bearer, 'No s\'ha obtingut access token en exchange.');

            $me = $this->fetchJson(
                $browser,
                '/api/auth/me',
                'GET',
                null,
                ['Authorization' => 'Bearer '.$bearer]
            );

            $this->assertSame(200, $me['status'] ?? null);
            $this->assertTrue((bool) ($me['json']['success'] ?? false));
            $this->assertSame((string) $profesor->dni, (string) ($me['json']['data']['dni'] ?? ''));
        });
    }

    /**
     * Verifica el mode legacy: /api/auth/me continua funcionant amb query `api_token`.
     */
    public function test_auth_me_accepts_legacy_api_token_query_param(): void
    {
        $profesor = $this->legacyProfesorOrSkip();
        if ($profesor === null) {
            return;
        }

        $this->browse(function (Browser $browser) use ($profesor) {
            $browser->visit('/');

            $me = $this->fetchJson(
                $browser,
                '/api/auth/me?api_token='.rawurlencode((string) $profesor->api_token)
            );

            $this->assertSame(200, $me['status'] ?? null);
            $this->assertTrue((bool) ($me['json']['success'] ?? false));
            $this->assertSame((string) $profesor->dni, (string) ($me['json']['data']['dni'] ?? ''));
        });
    }

    /**
     * Recupera un professor amb token legacy; si no existeix, omet la prova.
     */
    private function legacyProfesorOrSkip(): ?Profesor
    {
        $profesor = Profesor::query()
            ->whereNotNull('api_token')
            ->where('api_token', '!=', '')
            ->first();

        if ($profesor === null) {
            $this->markTestSkipped('No hi ha professor amb api_token per executar prova de coexistència.');
            return null;
        }

        return $profesor;
    }

    /**
     * Executa una crida fetch des del navegador i retorna status + JSON parsejat.
     *
     * @param string $url
     * @param string $method
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

