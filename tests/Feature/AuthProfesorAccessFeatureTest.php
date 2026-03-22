<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class AuthProfesorAccessFeatureTest extends TestCase
{
    private string $sqlitePath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sqlitePath = storage_path('auth_profesor_access_feature_testing.sqlite');
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
        Schema::connection('sqlite')->dropIfExists('profesores');

        if (file_exists($this->sqlitePath)) {
            @unlink($this->sqlitePath);
        }

        parent::tearDown();
    }

    public function test_profesor_login_form_mostra_la_vista(): void
    {
        $response = $this->get(route('profesor.login'));

        $response->assertOk();
        $response->assertViewIs('auth.profesor.login');
    }

    public function test_extern_login_form_retorna_401_si_el_token_no_existix(): void
    {
        $response = $this->get('/login/token-absent');

        $response->assertOk();
        $response->assertViewIs('errors.401');
    }

    public function test_extern_login_form_mostra_vista_quan_change_password_esta_actiu(): void
    {
        DB::table('profesores')->insert([
            'dni' => 'P000000001',
            'codigo' => 1001,
            'nombre' => 'Test',
            'apellido1' => 'Extern',
            'apellido2' => 'Login',
            'email' => 'extern@test.local',
            'api_token' => 'token-valid',
            'changePassword' => '2026-02-14',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->get('/login/token-valid');

        $response->assertOk();
        $response->assertViewIs('auth.profesor.externLogin');
        $response->assertViewHas('professor');
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
            $table->string('api_token')->nullable();
            $table->string('changePassword')->nullable();
            $table->timestamps();
        });
    }
}
