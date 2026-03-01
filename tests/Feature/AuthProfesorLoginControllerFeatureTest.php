<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Contracts\Validation\UncompromisedVerifier;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Hash;
use Intranet\Http\Middleware\VerifyCsrfToken;
use Tests\TestCase;

class AuthProfesorLoginControllerFeatureTest extends TestCase
{
    private string $sqlitePath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sqlitePath = storage_path('auth_profesor_login_controller_feature_testing.sqlite');
        if (file_exists($this->sqlitePath)) {
            @unlink($this->sqlitePath);
        }

        touch($this->sqlitePath);
        config(['database.default' => 'sqlite']);
        config(['database.connections.sqlite.database' => $this->sqlitePath]);

        DB::setDefaultConnection('sqlite');
        DB::purge('sqlite');
        DB::reconnect('sqlite');

        $this->app->instance(UncompromisedVerifier::class, new class implements UncompromisedVerifier {
            public function verify($data): bool
            {
                return true;
            }
        });

        $this->createSchema();
    }

    protected function tearDown(): void
    {
        Schema::connection('sqlite')->dropIfExists('personal_access_tokens');
        Schema::connection('sqlite')->dropIfExists('profesores');

        if (file_exists($this->sqlitePath)) {
            @unlink($this->sqlitePath);
        }

        parent::tearDown();
    }

    public function test_plogin_permet_mostrar_api_token_en_meta_despres_de_login(): void
    {
        $this->insertProfesor([
            'dni' => '44556677F',
            'codigo' => 2006,
            'email' => 'prof6@test.local',
            'password' => Hash::make('secret-pass'),
            'changePassword' => '2026-01-01',
            'idioma' => 'ca',
            'api_token' => 'legacy-token-meta-2006',
        ]);

        $response = $this->withoutMiddleware([VerifyCsrfToken::class])
            ->post(route('profesor.postlogin'), [
                'codigo' => '2006',
                'password' => 'secret-pass',
            ]);

        $response->assertStatus(302);
        $response->assertRedirect('/home');
        $this->assertAuthenticated('profesor');

        $bearerToken = session('api_access_token');
        $this->assertIsString($bearerToken);
        $this->assertNotSame('', $bearerToken);

        $metaResponse = $this->blade('<x-layouts.meta />');
        $metaResponse->assertSee('name="user-token"', false);
        $metaResponse->assertSee('content="legacy-token-meta-2006"', false);
        $metaResponse->assertSee('name="user-bearer-token"', false);
        $metaResponse->assertSee('content="'.$bearerToken.'"', false);
    }

    public function test_plogin_mostra_first_login_quan_canvi_password_no_establit_i_dni_coincidix(): void
    {
        $this->insertProfesor([
            'dni' => '12345678A',
            'codigo' => 2001,
            'email' => 'prof1@test.local',
            'password' => Hash::make('irrelevant'),
            'changePassword' => null,
        ]);

        $response = $this->withoutMiddleware([VerifyCsrfToken::class])
            ->post(route('profesor.postlogin'), [
                'codigo' => '2001',
                'password' => '12345678A',
            ]);

        $response->assertOk();
        $response->assertViewIs('auth.profesor.firstLogin');
        $response->assertViewHas('profesor', function ($profesor): bool {
            return $profesor->dni === '12345678A';
        });
    }

    public function test_plogin_retorna_error_quan_canvi_password_no_establit_i_password_incorrecta(): void
    {
        $this->insertProfesor([
            'dni' => '87654321B',
            'codigo' => 2002,
            'email' => 'prof2@test.local',
            'password' => Hash::make('irrelevant'),
            'changePassword' => null,
        ]);

        $response = $this->from('/profesor/login')
            ->withoutMiddleware([VerifyCsrfToken::class])
            ->post(route('profesor.postlogin'), [
                'codigo' => '2002',
                'password' => 'WRONG',
            ]);

        $response->assertStatus(302);
        $response->assertRedirect('/profesor/login');
        $response->assertSessionHasErrors('password');
    }

    public function test_plogin_fa_login_normal_quan_change_password_ja_existeix(): void
    {
        $this->insertProfesor([
            'dni' => '11223344C',
            'codigo' => 2003,
            'email' => 'prof3@test.local',
            'password' => Hash::make('secret-pass'),
            'changePassword' => '2026-01-01',
            'idioma' => 'va',
        ]);

        $response = $this->withoutMiddleware([VerifyCsrfToken::class])
            ->post(route('profesor.postlogin'), [
                'codigo' => '2003',
                'password' => 'secret-pass',
            ]);

        $response->assertStatus(302);
        $response->assertRedirect('/home');
        $this->assertAuthenticated('profesor');
        $this->assertSame('va', session('lang'));
    }

    public function test_first_login_retorna_errors_de_validacio_quan_password_no_complix_regles(): void
    {
        $this->insertProfesor([
            'dni' => '22334455D',
            'codigo' => 2004,
            'email' => 'prof4@test.local',
            'password' => Hash::make('old-secret'),
            'changePassword' => null,
        ]);

        $response = $this->from('/profesor/login')
            ->withoutMiddleware([VerifyCsrfToken::class])
            ->post(route('profesor.firstLogin'), [
                'codigo' => '2004',
                'email' => 'new@test.local',
                'password' => 'weak',
                'password_confirmation' => 'weak',
            ]);

        $response->assertStatus(302);
        $response->assertRedirect('/profesor/login');
        $response->assertSessionHasErrors('password');
    }

    public function test_first_login_retorna_error_si_usuari_no_trobat(): void
    {
        $response = $this->from('/profesor/login')
            ->withoutMiddleware([VerifyCsrfToken::class])
            ->post(route('profesor.firstLogin'), [
                'codigo' => '9999',
                'email' => 'new@test.local',
                'password' => 'PassWord1234',
                'password_confirmation' => 'PassWord1234',
            ]);

        $response->assertStatus(302);
        $response->assertRedirect('/profesor/login');
        $response->assertSessionHasErrors('codigo');
    }

    public function test_first_login_actualitza_credencials_i_redirigix_a_home(): void
    {
        $this->insertProfesor([
            'dni' => '33445566E',
            'codigo' => 2005,
            'email' => 'old@test.local',
            'password' => Hash::make('old-secret'),
            'changePassword' => null,
            'idioma' => 'es',
        ]);

        $response = $this->withoutMiddleware([VerifyCsrfToken::class])
            ->post(route('profesor.firstLogin'), [
                'codigo' => '2005',
                'email' => 'updated@test.local',
                'password' => 'PassWord1234',
                'password_confirmation' => 'PassWord1234',
            ]);

        $response->assertStatus(302);
        $response->assertRedirect('/home');
        $this->assertAuthenticated();
        $this->assertSame('es', session('lang'));

        $fresh = DB::table('profesores')->where('codigo', 2005)->first();
        $this->assertNotNull($fresh);
        $this->assertSame('updated@test.local', $fresh->email);
        $this->assertNotNull($fresh->changePassword);
        $this->assertTrue(Hash::check('PassWord1234', (string) $fresh->password));
    }

    /**
     * @param array<string, mixed> $overrides
     */
    private function insertProfesor(array $overrides = []): void
    {
        DB::table('profesores')->insert(array_merge([
            'dni' => '00000000T',
            'codigo' => 1000,
            'nombre' => 'Nom',
            'apellido1' => 'Cognom1',
            'apellido2' => 'Cognom2',
            'email' => 'default@test.local',
            'password' => Hash::make('secret'),
            'idioma' => 'ca',
            'changePassword' => null,
            'rol' => (int) config('roles.rol.profesor'),
            'fecha_baja' => null,
            'activo' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ], $overrides));
    }

    private function createSchema(): void
    {
        if (Schema::connection('sqlite')->hasTable('profesores')) {
            return;
        }

        Schema::connection('sqlite')->create('profesores', function (Blueprint $table): void {
            $table->string('dni', 10)->primary();
            $table->unsignedInteger('codigo')->nullable();
            $table->string('nombre')->nullable();
            $table->string('apellido1')->nullable();
            $table->string('apellido2')->nullable();
            $table->string('email')->nullable();
            $table->string('password')->nullable();
            $table->string('api_token', 80)->nullable();
            $table->string('idioma')->nullable();
            $table->string('changePassword')->nullable();
            $table->unsignedInteger('rol')->default(3);
            $table->date('fecha_baja')->nullable();
            $table->boolean('activo')->default(true);
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::connection('sqlite')->create('personal_access_tokens', function (Blueprint $table): void {
            $table->id();
            $table->string('tokenable_type');
            $table->string('tokenable_id');
            $table->string('name');
            $table->string('token', 64)->unique();
            $table->text('abilities')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });
    }
}
