<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use Intranet\Entities\Alumno;
use Intranet\Entities\Profesor;
use Intranet\Services\Auth\NmSsoTokenService;
use Tests\TestCase;

/**
 * Proves HTTP del SSO de professorat cap a NM.
 */
class NmSsoControllerTest extends TestCase
{
    private string $state;

    /**
     * Configura el contracte fix de la integració.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->state = str_repeat('a', 43);
        config([
            'sso.nm.callback' => 'https://nm.cipfpbatoi.es/login',
        ]);
    }

    public function test_professor_actiu_rep_un_formulari_post_amb_token_i_state(): void
    {
        $profesor = $this->profesor();
        $this->actingAs($profesor, 'profesor');
        $this->mock(NmSsoTokenService::class, function ($mock) use ($profesor): void {
            $mock->shouldReceive('issueFor')->once()->with($profesor)->andReturn('jwt-de-prova');
        });

        $response = $this->get(route('sso.nm.login', ['state' => $this->state]));

        $response->assertOk();
        $response->assertViewIs('auth.sso.post');
        $response->assertViewHas('callback', 'https://nm.cipfpbatoi.es/login');
        $response->assertViewHas('state', $this->state);
        $response->assertSee('method="POST"', false);
        $response->assertSee('action="https://nm.cipfpbatoi.es/login"', false);
        $response->assertSee('name="token" value="jwt-de-prova"', false);
        $response->assertSee('name="state" value="' . $this->state . '"', false);
        $this->assertStringContainsString('no-store', (string) $response->headers->get('Cache-Control'));
        $this->assertSame('no-referrer', $response->headers->get('Referrer-Policy'));
        $this->assertStringContainsString(
            'form-action https://nm.cipfpbatoi.es/login',
            (string) $response->headers->get('Content-Security-Policy')
        );
    }

    public function test_la_destinacio_no_es_pot_canviar_amb_redirect_uri(): void
    {
        $this->actingAs($this->profesor(), 'profesor');
        $this->mock(NmSsoTokenService::class, function ($mock): void {
            $mock->shouldReceive('issueFor')->once()->andReturn('jwt-de-prova');
        });

        $response = $this->get(route('sso.nm.login', [
            'state' => $this->state,
            'redirect_uri' => 'https://evil.example/login',
        ]));

        $response->assertOk();
        $response->assertSee('action="https://nm.cipfpbatoi.es/login"', false);
        $response->assertDontSee('action="https://evil.example/login"', false);
    }

    public function test_convidat_es_redirigit_al_login_i_conserva_state(): void
    {
        $response = $this->get(route('sso.nm.login', ['state' => $this->state]));

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('url.intended', function (string $url): bool {
            return str_contains($url, '/sso/nm/login') && str_contains($url, 'state='.$this->state);
        });
    }

    public function test_state_invalid_no_emet_cap_token(): void
    {
        $this->actingAs($this->profesor(), 'profesor');
        $this->mock(NmSsoTokenService::class, function ($mock): void {
            $mock->shouldNotReceive('issueFor');
        });

        $response = $this->from('/home')->get(route('sso.nm.login', ['state' => 'massa-curt']));

        $response->assertRedirect('/home');
        $response->assertSessionHasErrors('state');
    }

    public function test_professor_inactiu_no_pot_obtindre_token(): void
    {
        $this->actingAs($this->profesor(['activo' => 0]), 'profesor');
        $this->mock(NmSsoTokenService::class, function ($mock): void {
            $mock->shouldNotReceive('issueFor');
        });

        $this->get(route('sso.nm.login', ['state' => $this->state]))->assertForbidden();
    }

    public function test_administrador_sense_factor_professor_no_pot_obtindre_token(): void
    {
        $this->actingAs($this->profesor([
            'rol' => (int) config('roles.rol.administrador'),
        ]), 'profesor');
        $this->mock(NmSsoTokenService::class, function ($mock): void {
            $mock->shouldNotReceive('issueFor');
        });

        $this->get(route('sso.nm.login', ['state' => $this->state]))->assertForbidden();
    }

    public function test_alumne_no_pot_obtindre_token(): void
    {
        $alumne = new Alumno();
        $alumne->setRawAttributes([
            'nia' => '12345678',
            'rol' => config('roles.rol.alumno'),
        ]);
        $this->actingAs($alumne, 'alumno');
        $this->mock(NmSsoTokenService::class, function ($mock): void {
            $mock->shouldNotReceive('issueFor');
        });

        $this->get(route('sso.nm.login', ['state' => $this->state]))->assertStatus(403);
    }

    /**
     * Crea una identitat de professor sense persistència per a la petició actual.
     *
     * @param array<string, mixed> $attributes
     */
    private function profesor(array $attributes = []): Profesor
    {
        $profesor = new Profesor();
        $profesor->setRawAttributes(array_merge([
            'dni' => '12345678Z',
            'codigo' => 2042,
            'nombre' => 'Joan',
            'apellido1' => 'Pérez',
            'apellido2' => 'Ferri',
            'email' => 'joan@example.test',
            'rol' => config('roles.rol.profesor'),
            'activo' => 1,
            'fecha_baja' => null,
        ], $attributes));
        $profesor->exists = true;

        return $profesor;
    }
}
