<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Intranet\Entities\Profesor;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Verifica que els endpoints `edit` retornen dates canòniques per als modals.
 */
class ApiEditDateNormalizationFeatureTest extends TestCase
{
    private string $sqlitePath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sqlitePath = storage_path('api_edit_date_normalization_feature_testing.sqlite');
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
        Schema::connection('sqlite')->dropIfExists('actividades');
        Schema::connection('sqlite')->dropIfExists('comisiones');
        Schema::connection('sqlite')->dropIfExists('expedientes');
        Schema::connection('sqlite')->dropIfExists('profesores');

        if (file_exists($this->sqlitePath)) {
            @unlink($this->sqlitePath);
        }

        parent::tearDown();
    }

    public function test_actividad_edit_retorna_datetime_canoic_per_als_pickers_legacy(): void
    {
        $this->authenticateApiAsProfesor('PAD01');

        DB::table('actividades')->insert([
            'id' => 301,
            'name' => 'Visita tècnica',
            'tipo_actividad_id' => 2,
            'desde' => '2026-05-10 09:15:00',
            'hasta' => '2026-05-10 13:45:00',
            'complementaria' => 1,
            'fueraCentro' => 0,
            'transport' => 1,
            'descripcion' => 'Descripció',
            'objetivos' => 'Objectius',
            'comentarios' => 'Comentaris',
            'poll' => 0,
            'extraescolar' => 1,
        ]);

        $response = $this->getJson('/api/actividad/301/edit');

        $response->assertOk();
        $response->assertJsonPath('data.id', 301);
        $response->assertJsonPath('data.name', 'Visita tècnica');
        $response->assertJsonPath('data.desde', '2026-05-10 09:15');
        $response->assertJsonPath('data.hasta', '2026-05-10 13:45');
        $response->assertJsonPath('data.descripcion', 'Descripció');
    }

    public function test_comision_edit_retorna_datetime_canoic_i_checkbox_estable(): void
    {
        $this->authenticateApiAsProfesor('PAD02');

        DB::table('comisiones')->insert([
            'id' => 302,
            'idProfesor' => 'PAD02',
            'desde' => '2026-06-01 08:00:00',
            'hasta' => '2026-06-01 14:30:00',
            'fct' => 1,
            'servicio' => 'Visita FCT',
            'alojamiento' => 0,
            'comida' => 12.50,
            'gastos' => 3.25,
            'kilometraje' => 40,
            'medio' => 0,
            'marca' => 'Seat',
            'matricula' => '1234ABC',
            'itinerario' => 'Batoi - empresa',
            'estado' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->getJson('/api/comision/302/edit');

        $response->assertOk();
        $response->assertJsonPath('data.id', 302);
        $response->assertJsonPath('data.servicio', 'Visita FCT');
        $response->assertJsonPath('data.desde', '2026-06-01 08:00');
        $response->assertJsonPath('data.hasta', '2026-06-01 14:30');
        $response->assertJsonPath('data.fct', 1);
    }

    public function test_expediente_edit_retorna_dates_html5_canonics(): void
    {
        $this->authenticateApiAsProfesor('PAD03');

        DB::table('expedientes')->insert([
            'id' => 303,
            'tipo' => 1,
            'idModulo' => 'MOD1',
            'idAlumno' => 'ALU1',
            'idProfesor' => 'PAD03',
            'explicacion' => 'Explicació de prova',
            'fecha' => '2026-04-07',
            'fechatramite' => '2026-04-09',
            'estado' => 0,
        ]);

        $response = $this->getJson('/api/expediente/303/edit');

        $response->assertOk();
        $response->assertJsonPath('data.id', 303);
        $response->assertJsonPath('data.explicacion', 'Explicació de prova');
        $response->assertJsonPath('data.fecha', '2026-04-07');
        $response->assertJsonPath('data.fechatramite', '2026-04-09');
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

        if (!Schema::connection('sqlite')->hasTable('actividades')) {
            Schema::connection('sqlite')->create('actividades', function (Blueprint $table): void {
                $table->id();
                $table->string('name')->nullable();
                $table->unsignedInteger('tipo_actividad_id')->nullable();
                $table->dateTime('desde')->nullable();
                $table->dateTime('hasta')->nullable();
                $table->unsignedTinyInteger('complementaria')->default(0);
                $table->unsignedTinyInteger('fueraCentro')->default(0);
                $table->unsignedTinyInteger('transport')->default(0);
                $table->text('descripcion')->nullable();
                $table->text('objetivos')->nullable();
                $table->text('comentarios')->nullable();
                $table->unsignedTinyInteger('poll')->default(0);
                $table->unsignedTinyInteger('extraescolar')->default(1);
            });
        }

        if (!Schema::connection('sqlite')->hasTable('comisiones')) {
            Schema::connection('sqlite')->create('comisiones', function (Blueprint $table): void {
                $table->id();
                $table->string('idProfesor', 10)->nullable();
                $table->dateTime('desde')->nullable();
                $table->dateTime('hasta')->nullable();
                $table->unsignedTinyInteger('fct')->default(0);
                $table->text('servicio')->nullable();
                $table->decimal('alojamiento', 8, 2)->default(0);
                $table->decimal('comida', 8, 2)->default(0);
                $table->decimal('gastos', 8, 2)->default(0);
                $table->unsignedInteger('kilometraje')->default(0);
                $table->unsignedTinyInteger('medio')->default(0);
                $table->string('marca')->nullable();
                $table->string('matricula')->nullable();
                $table->text('itinerario')->nullable();
                $table->tinyInteger('estado')->default(0);
                $table->timestamps();
            });
        }

        if (!Schema::connection('sqlite')->hasTable('expedientes')) {
            Schema::connection('sqlite')->create('expedientes', function (Blueprint $table): void {
                $table->id();
                $table->unsignedInteger('tipo')->nullable();
                $table->string('idModulo')->nullable();
                $table->string('idAlumno')->nullable();
                $table->string('idProfesor')->nullable();
                $table->text('explicacion')->nullable();
                $table->date('fecha')->nullable();
                $table->date('fechatramite')->nullable();
                $table->tinyInteger('estado')->default(0);
            });
        }
    }

    private function authenticateApiAsProfesor(string $dni): void
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

        Sanctum::actingAs(Profesor::on('sqlite')->findOrFail($dni));
    }
}
