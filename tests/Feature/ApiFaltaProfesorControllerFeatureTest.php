<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Intranet\Entities\Profesor;
use Tests\TestCase;

class ApiFaltaProfesorControllerFeatureTest extends TestCase
{
    private string $sqlitePath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sqlitePath = storage_path('api_falta_profesor_controller_feature_testing.sqlite');
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

    public function test_horas_agrega_registres_filtrats_per_dia(): void
    {
        $this->insertProfesor('PFP01');
        $user = Profesor::on('sqlite')->findOrFail('PFP01');
        $this->actingAs($user, 'api');

        DB::table('faltas_profesores')->insert([
            [
                'idProfesor' => 'PFP01',
                'dia' => '2026-02-20',
                'entrada' => '08:00:00',
                'salida' => '10:00:00',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'idProfesor' => 'PFP01',
                'dia' => '2026-02-20',
                'entrada' => '11:00:00',
                'salida' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'idProfesor' => 'PFP01',
                'dia' => '2026-02-21',
                'entrada' => '08:00:00',
                'salida' => '09:00:00',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $response = $this->getJson('/api/faltaProfesor/horas/dia=2026-02-20');

        $response->assertOk();
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('data.PFP01.2026-02-20.horas', '03:00:00');
    }

    public function test_show_legacy_filtre_per_dia_torna_registres(): void
    {
        $this->insertProfesor('PFP02');
        $user = Profesor::on('sqlite')->findOrFail('PFP02');
        $this->actingAs($user, 'api');

        DB::table('faltas_profesores')->insert([
            [
                'idProfesor' => 'PFP02',
                'dia' => '2026-02-22',
                'entrada' => '09:00:00',
                'salida' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'idProfesor' => 'PFP02',
                'dia' => '2026-02-23',
                'entrada' => '09:30:00',
                'salida' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $response = $this->getJson('/api/faltaProfesor/dia=2026-02-22');

        $response->assertOk();
        $response->assertJsonPath('success', true);
        $response->assertJsonCount(1, 'data');
        $response->assertJsonPath('data.0.dia', '2026-02-22');
    }

    public function test_index_modern_filtra_per_query_dia(): void
    {
        $this->insertProfesor('PFP12');
        $user = Profesor::on('sqlite')->findOrFail('PFP12');
        $this->actingAs($user, 'api');

        DB::table('faltas_profesores')->insert([
            [
                'idProfesor' => 'PFP12',
                'dia' => '2026-02-24',
                'entrada' => '09:00:00',
                'salida' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'idProfesor' => 'PFP12',
                'dia' => '2026-02-25',
                'entrada' => '09:30:00',
                'salida' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $response = $this->getJson('/api/faltaProfesor?dia=2026-02-24');

        $response->assertOk();
        $response->assertHeaderMissing('Deprecation');
        $response->assertJsonPath('success', true);
        $response->assertJsonCount(1, 'data');
        $response->assertJsonPath('data.0.dia', '2026-02-24');
    }

    private function createSchema(): void
    {
        if (!Schema::connection('sqlite')->hasTable('profesores')) {
            Schema::connection('sqlite')->create('profesores', function (Blueprint $table): void {
                $table->string('dni', 10)->primary();
                $table->integer('codigo')->nullable();
                $table->string('nombre')->nullable();
                $table->string('apellido1')->nullable();
                $table->string('apellido2')->nullable();
                $table->string('email')->nullable();
                $table->unsignedInteger('rol')->default(3);
                $table->string('api_token', 80)->nullable();
                $table->date('fecha_baja')->nullable();
                $table->boolean('activo')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::connection('sqlite')->hasTable('faltas_profesores')) {
            Schema::connection('sqlite')->create('faltas_profesores', function (Blueprint $table): void {
                $table->increments('id');
                $table->string('idProfesor', 10)->nullable();
                $table->date('dia')->nullable();
                $table->time('entrada')->nullable();
                $table->time('salida')->nullable();
                $table->timestamps();
            });
        }
    }

    private function insertProfesor(string $dni): void
    {
        DB::table('profesores')->insert([
            'dni' => $dni,
            'codigo' => random_int(1000, 9999),
            'nombre' => 'Nom',
            'apellido1' => 'Cognom1',
            'apellido2' => 'Cognom2',
            'email' => strtolower($dni) . '@test.local',
            'rol' => config('roles.rol.profesor'),
            'api_token' => bin2hex(random_bytes(20)),
            'fecha_baja' => null,
            'activo' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
