<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ApiAuthTokenExchangeFeatureTest extends TestCase
{
    private string $sqlitePath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sqlitePath = storage_path('api_auth_token_exchange_feature_testing.sqlite');
        if (file_exists($this->sqlitePath)) {
            @unlink($this->sqlitePath);
        }

        touch($this->sqlitePath);
        config(['database.default' => 'sqlite']);
        config(['database.connections.sqlite.database' => $this->sqlitePath]);

        DB::setDefaultConnection('sqlite');
        DB::purge('sqlite');
        DB::reconnect('sqlite');

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

    public function test_exchange_crea_token_sanctum_amb_api_token_legacy_valid(): void
    {
        $this->insertProfesor('PAX001', 'legacy-token-001');

        $response = $this->postJson('/api/auth/exchange', [
            'api_token' => 'legacy-token-001',
            'device_name' => 'test-suite',
        ]);

        $response->assertOk();
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('data.token_type', 'Bearer');

        $accessToken = (string) $response->json('data.access_token');
        $this->assertNotSame('', $accessToken);
        $this->assertStringContainsString('|', $accessToken);

        $this->assertSame(
            1,
            DB::table('personal_access_tokens')
                ->where('tokenable_type', 'Intranet\\Entities\\Profesor')
                ->where('tokenable_id', 'PAX001')
                ->count()
        );
    }

    public function test_exchange_rebutja_api_token_invalid(): void
    {
        $response = $this->postJson('/api/auth/exchange', [
            'api_token' => 'invalid-token-999',
        ]);

        $response->assertStatus(401);
        $response->assertJsonPath('success', false);
        $response->assertJsonPath('message', 'Unauthorized');
    }

    public function test_auth_me_accepta_token_sanctum(): void
    {
        $this->insertProfesor('PAX010', 'legacy-token-010');

        $exchange = $this->postJson('/api/auth/exchange', [
            'api_token' => 'legacy-token-010',
            'device_name' => 'test-me',
        ]);
        $exchange->assertOk();
        $token = (string) $exchange->json('data.access_token');

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->getJson('/api/auth/me');

        $response->assertOk();
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('data.dni', 'PAX010');
    }

    public function test_auth_me_rebutja_api_token_legacy_directe_sense_bearer(): void
    {
        $this->insertProfesor('PAX011', 'legacy-token-011');

        $response = $this->getJson('/api/auth/me?api_token=legacy-token-011');

        $response->assertStatus(401);
    }

    public function test_auth_logout_revoca_token_actual(): void
    {
        $this->insertProfesor('PAX020', 'legacy-token-020');

        $exchange = $this->postJson('/api/auth/exchange', [
            'api_token' => 'legacy-token-020',
            'device_name' => 'test-logout',
        ]);
        $exchange->assertOk();
        $token = (string) $exchange->json('data.access_token');

        $this->assertSame(1, DB::table('personal_access_tokens')->count());

        $logout = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->postJson('/api/auth/logout');

        $logout->assertOk();
        $logout->assertJsonPath('success', true);
        $logout->assertJsonPath('data.revoked', true);
        $this->assertSame(0, DB::table('personal_access_tokens')->count());
    }

    private function createSchema(): void
    {
        if (!Schema::connection('sqlite')->hasTable('profesores')) {
            Schema::connection('sqlite')->create('profesores', function (Blueprint $table): void {
                $table->string('dni', 10)->primary();
                $table->string('nombre')->nullable();
                $table->string('apellido1')->nullable();
                $table->string('apellido2')->nullable();
                $table->string('api_token', 80)->nullable();
                $table->unsignedInteger('rol')->default(config('roles.rol.profesor'));
                $table->unsignedInteger('departamento')->nullable();
                $table->boolean('activo')->default(true);
            });
        }

        if (!Schema::connection('sqlite')->hasTable('personal_access_tokens')) {
            Schema::connection('sqlite')->create('personal_access_tokens', function (Blueprint $table): void {
                $table->id();
                $table->string('tokenable_type');
                $table->string('tokenable_id');
                $table->text('name');
                $table->string('token', 64)->unique();
                $table->text('abilities')->nullable();
                $table->timestamp('last_used_at')->nullable();
                $table->timestamp('expires_at')->nullable();
                $table->timestamps();
            });
        }
    }

    private function insertProfesor(string $dni, string $apiToken): void
    {
        DB::table('profesores')->insert([
            'dni' => $dni,
            'nombre' => 'Nom',
            'apellido1' => 'Cognom1',
            'apellido2' => 'Cognom2',
            'api_token' => $apiToken,
            'rol' => config('roles.rol.profesor'),
            'departamento' => 1,
            'activo' => 1,
        ]);
    }
}
