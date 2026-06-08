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
 * Proves feature del contracte `edit()` API basat en JsonResource.
 */
class ApiEditResourceFeatureTest extends TestCase
{
    private string $sqlitePath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sqlitePath = storage_path('api_edit_resource_feature_testing.sqlite');
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
        $this->authenticateProfesor('PJS01');
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

    public function test_actividad_edit_usa_resource_explicita(): void
    {
        DB::table('actividades')->insert([
            'id' => 201,
            'tipo_actividad_id' => 4,
            'name' => 'Visita a empresa',
            'desde' => '2026-03-20 09:15:00',
            'hasta' => '2026-03-20 14:00:00',
            'poll' => 0,
            'complementaria' => 1,
            'fueraCentro' => 1,
            'transport' => 0,
            'descripcion' => 'Descripcio',
            'objetivos' => 'Objectius',
            'extraescolar' => 0,
            'comentarios' => 'Comentaris',
            'recomanada' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->getJson('/api/actividad/201/edit');

        $response->assertOk();
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('data.name', 'Visita a empresa');
        $response->assertJsonPath('data.desde', '20-03-2026 09:15');
        $response->assertJsonPath('data.hasta', '20-03-2026 14:00');
        $response->assertJsonPath('data.tipus_activitat', 'complementaria');
        $response->assertJsonPath('data.ubicacio_activitat', 'exterior_sense_transport');
        $response->assertJsonPath('data.descripcion', 'Descripcio');
    }

    public function test_comision_edit_usa_resource_explicita(): void
    {
        DB::table('comisiones')->insert([
            'id' => 301,
            'idProfesor' => 'PJS01',
            'desde' => '2026-04-10 08:00:00',
            'hasta' => '2026-04-10 12:30:00',
            'fct' => 1,
            'servicio' => 'Servei de prova',
            'alojamiento' => 10.50,
            'comida' => 12.25,
            'gastos' => 4.10,
            'kilometraje' => 20,
            'medio' => 1,
            'marca' => 'Ford',
            'matricula' => '1234ABC',
            'itinerario' => 'Batoi - Alcoi',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->getJson('/api/comision/301/edit');

        $response->assertOk();
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('data.idProfesor', 'PJS01');
        $response->assertJsonPath('data.servicio', 'Servei de prova');
        $response->assertJsonPath('data.desde', '10-04-2026 08:00');
        $response->assertJsonPath('data.hasta', '10-04-2026 12:30');
    }

    public function test_expediente_edit_usa_resource_explicita(): void
    {
        DB::table('expedientes')->insert([
            'id' => 401,
            'tipo' => 7,
            'idModulo' => 'M001',
            'idAlumno' => '10800001',
            'idProfesor' => 'PJS01',
            'explicacion' => 'Explicacio expedient',
            'fecha' => '2026-05-03',
            'fechatramite' => '2026-05-04',
        ]);

        $response = $this->getJson('/api/expediente/401/edit');

        $response->assertOk();
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('data.tipo', 7);
        $response->assertJsonPath('data.explicacion', 'Explicacio expedient');
        $response->assertJsonPath('data.fecha', '03-05-2026');
        $response->assertJsonPath('data.fechatramite', '04-05-2026');
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
                $table->increments('id');
                $table->unsignedInteger('tipo_actividad_id')->nullable();
                $table->string('name')->nullable();
                $table->dateTime('desde')->nullable();
                $table->dateTime('hasta')->nullable();
                $table->unsignedTinyInteger('poll')->default(0);
                $table->unsignedTinyInteger('complementaria')->default(0);
                $table->unsignedTinyInteger('fueraCentro')->default(1);
                $table->unsignedTinyInteger('transport')->default(0);
                $table->text('descripcion')->nullable();
                $table->text('objetivos')->nullable();
                $table->unsignedTinyInteger('extraescolar')->default(0);
                $table->text('comentarios')->nullable();
                $table->unsignedTinyInteger('recomanada')->default(0);
                $table->timestamps();
            });
        }

        if (!Schema::connection('sqlite')->hasTable('comisiones')) {
            Schema::connection('sqlite')->create('comisiones', function (Blueprint $table): void {
                $table->increments('id');
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
                $table->timestamps();
            });
        }

        if (!Schema::connection('sqlite')->hasTable('expedientes')) {
            Schema::connection('sqlite')->create('expedientes', function (Blueprint $table): void {
                $table->increments('id');
                $table->unsignedInteger('tipo')->nullable();
                $table->string('idModulo')->nullable();
                $table->string('idAlumno')->nullable();
                $table->string('idProfesor', 10)->nullable();
                $table->text('explicacion')->nullable();
                $table->date('fecha')->nullable();
                $table->date('fechatramite')->nullable();
            });
        }
    }

    private function authenticateProfesor(string $dni): void
    {
        DB::table('profesores')->insert([
            'dni' => $dni,
            'codigo' => 1001,
            'nombre' => 'Nom',
            'apellido1' => 'Cognom1',
            'apellido2' => 'Cognom2',
            'email' => 'test@example.com',
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
