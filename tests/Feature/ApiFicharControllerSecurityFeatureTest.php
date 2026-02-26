<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Intranet\Entities\Profesor;
use Tests\TestCase;

class ApiFicharControllerSecurityFeatureTest extends TestCase
{
    private string $sqlitePath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sqlitePath = storage_path('api_fichar_security_feature_testing.sqlite');
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
        Schema::connection('sqlite')->dropIfExists('faltas_profesores');
        Schema::connection('sqlite')->dropIfExists('profesores');

        if (file_exists($this->sqlitePath)) {
            @unlink($this->sqlitePath);
        }

        parent::tearDown();
    }

    public function test_doficha_accepta_auth_api_sense_api_token_en_query(): void
    {
        $_SERVER['REMOTE_ADDR'] = '192.168.1.20';

        $this->insertProfesor('PFI001', 'token-ficha-a');
        $user = Profesor::on('sqlite')->findOrFail('PFI001');
        $this->actingAs($user, 'api');

        $response = $this->get('/api/doficha?dni=PFI001');

        $response->assertOk();
        $this->assertSame(
            1,
            DB::table('faltas_profesores')
                ->where('idProfesor', 'PFI001')
                ->count()
        );
    }

    public function test_doficha_rebutja_dni_diferent_al_usuari_auth_api(): void
    {
        $_SERVER['REMOTE_ADDR'] = '192.168.1.20';

        $this->insertProfesor('PFI010', 'token-ficha-auth');
        $this->insertProfesor('PFI020', 'token-ficha-target');
        $user = Profesor::on('sqlite')->findOrFail('PFI010');
        $this->actingAs($user, 'api');

        $response = $this->getJson('/api/doficha?dni=PFI020');

        $response->assertOk();
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('data.updated', false);

        $this->assertSame(
            0,
            DB::table('faltas_profesores')
                ->where('idProfesor', 'PFI020')
                ->count()
        );
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

        if (!Schema::connection('sqlite')->hasTable('faltas_profesores')) {
            Schema::connection('sqlite')->create('faltas_profesores', function (Blueprint $table): void {
                $table->increments('id');
                $table->string('idProfesor', 10);
                $table->date('dia');
                $table->time('entrada')->nullable();
                $table->time('salida')->nullable();
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
