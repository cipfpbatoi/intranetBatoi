<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Intranet\Entities\Profesor;
use Tests\TestCase;

class ApiGuardiaControllerFeatureTest extends TestCase
{
    private string $sqlitePath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sqlitePath = storage_path('api_guardia_controller_feature_testing.sqlite');
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
        Schema::connection('sqlite')->dropIfExists('guardias');
        Schema::connection('sqlite')->dropIfExists('profesores');

        if (file_exists($this->sqlitePath)) {
            @unlink($this->sqlitePath);
        }

        parent::tearDown();
    }

    public function test_show_legacy_dia_range_funciona(): void
    {
        $this->insertProfesor('PGU01');
        $user = Profesor::on('sqlite')->findOrFail('PGU01');
        $this->actingAs($user, 'api');

        DB::table('guardias')->insert([
            [
                'idProfesor' => 'PGU01',
                'dia' => '2026-02-10',
                'hora' => 1,
                'realizada' => 1,
                'observaciones' => null,
                'obs_personal' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'idProfesor' => 'PGU01',
                'dia' => '2026-02-12',
                'hora' => 2,
                'realizada' => 0,
                'observaciones' => null,
                'obs_personal' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'idProfesor' => 'PGU01',
                'dia' => '2026-02-20',
                'hora' => 3,
                'realizada' => 1,
                'observaciones' => null,
                'obs_personal' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $response = $this->getJson('/api/guardia/dia]2026-02-10&dia[2026-02-15');

        $response->assertOk();
        $response->assertJsonPath('success', true);
        $response->assertJsonCount(2, 'data');
    }

    public function test_range_endpoint_retorna_422_si_falten_params(): void
    {
        $this->insertProfesor('PGU02');
        $user = Profesor::on('sqlite')->findOrFail('PGU02');
        $this->actingAs($user, 'api');

        $response = $this->getJson('/api/guardia/range?desde=2026-02-10');

        $response->assertStatus(422);
        $response->assertJsonPath('message', 'Falten paràmetres: desde i hasta');
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

        if (!Schema::connection('sqlite')->hasTable('guardias')) {
            Schema::connection('sqlite')->create('guardias', function (Blueprint $table): void {
                $table->increments('id');
                $table->string('idProfesor', 10)->nullable();
                $table->date('dia')->nullable();
                $table->unsignedInteger('hora')->nullable();
                $table->unsignedTinyInteger('realizada')->default(0);
                $table->text('observaciones')->nullable();
                $table->text('obs_personal')->nullable();
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
