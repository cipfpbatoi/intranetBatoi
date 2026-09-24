<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Schema;
use Intranet\Entities\Profesor;
use Tests\TestCase;

/**
 * Proves HTTP d'autorització i verbs segurs en les reunions web.
 */
class ReunionWebAuthorizationFeatureTest extends TestCase
{
    private string $sqlitePath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sqlitePath = storage_path('web_reunion_authorization_testing.sqlite');
        if (file_exists($this->sqlitePath)) {
            @unlink($this->sqlitePath);
        }

        touch($this->sqlitePath);
        config(['database.default' => 'sqlite']);
        config(['database.connections.sqlite.database' => $this->sqlitePath]);
        DB::setDefaultConnection('sqlite');
        DB::purge('sqlite');
        DB::reconnect('sqlite');
        Event::fake();

        $this->createSchema();
        $this->seedData();
    }

    protected function tearDown(): void
    {
        DB::disconnect('sqlite');
        if (file_exists($this->sqlitePath)) {
            @unlink($this->sqlitePath);
        }

        parent::tearDown();
    }

    public function test_professor_alie_no_pot_eliminar_reunio(): void
    {
        $this->actingAs(Profesor::query()->findOrFail('P2'), 'profesor');

        $this->delete('/reunion/1/delete')->assertForbidden();

        $this->assertDatabaseHas('reuniones', ['id' => 1]);
    }

    public function test_professor_alie_no_pot_consultar_el_pdf(): void
    {
        $this->actingAs(Profesor::query()->findOrFail('P2'), 'profesor');

        $this->get('/reunion/1/pdf')->assertForbidden();
    }

    public function test_no_es_pot_eliminar_un_punt_d_una_altra_reunio(): void
    {
        $this->actingAs(Profesor::query()->findOrFail('P1'), 'profesor');

        $this->delete('/reunion/1/borrarOrden/22')->assertNotFound();

        $this->assertDatabaseHas('ordenes_reuniones', ['id' => 22, 'idReunion' => 2]);
    }

    public function test_get_no_executa_mutacions_de_reunions(): void
    {
        $this->actingAs(Profesor::query()->findOrFail('P1'), 'profesor');

        $this->get('/reunion/1/delete')->assertMethodNotAllowed();
        $this->get('/reunion/1/saveFile')->assertMethodNotAllowed();
        $this->get('/reunion/1/notification')->assertMethodNotAllowed();

        $this->assertDatabaseHas('reuniones', ['id' => 1, 'archivada' => false]);
    }

    public function test_update_no_permet_transferir_el_convocant(): void
    {
        $this->actingAs(Profesor::query()->findOrFail('P1'), 'profesor');

        $this->put('/reunion/1/edit', [
            'tipo' => 2,
            'curso' => '2026-2027',
            'fecha' => '2026-09-01 11:00:00',
            'descripcion' => 'Reunió actualitzada',
            'idProfesor' => 'P2',
            'idEspacio' => 'A101',
        ])->assertRedirect();

        $this->assertDatabaseHas('reuniones', [
            'id' => 1,
            'idProfesor' => 'P1',
            'descripcion' => 'Reunió actualitzada',
        ]);
    }

    private function createSchema(): void
    {
        Schema::create('profesores', function (Blueprint $table): void {
            $table->string('dni', 10)->primary();
            $table->unsignedInteger('rol')->default(3);
            $table->string('sustituye_a', 10)->nullable();
            $table->timestamps();
        });
        Schema::create('grupos', function (Blueprint $table): void {
            $table->string('codigo', 10)->primary();
            $table->string('tutor', 10)->nullable();
        });
        Schema::create('alumnos', function (Blueprint $table): void {
            $table->string('nia', 15)->primary();
        });
        Schema::create('alumnos_grupos', function (Blueprint $table): void {
            $table->string('idAlumno', 15);
            $table->string('idGrupo', 10);
        });
        Schema::create('reuniones', function (Blueprint $table): void {
            $table->increments('id');
            $table->unsignedTinyInteger('tipo')->default(2);
            $table->string('grupo')->nullable();
            $table->string('idProfesor', 10);
            $table->string('idGrupo', 10)->nullable();
            $table->string('curso', 20);
            $table->unsignedTinyInteger('numero')->nullable();
            $table->dateTime('fecha');
            $table->string('descripcion', 120);
            $table->text('objetivos')->nullable();
            $table->string('idEspacio', 10);
            $table->boolean('archivada')->default(false);
            $table->string('fichero')->nullable();
            $table->timestamps();
        });
        Schema::create('asistencias', function (Blueprint $table): void {
            $table->unsignedInteger('idReunion');
            $table->string('idProfesor', 10);
            $table->boolean('asiste')->default(false);
        });
        Schema::create('ordenes_reuniones', function (Blueprint $table): void {
            $table->increments('id');
            $table->unsignedInteger('idReunion');
            $table->unsignedTinyInteger('orden');
            $table->string('descripcion')->nullable();
            $table->text('resumen')->nullable();
        });
    }

    private function seedData(): void
    {
        DB::table('profesores')->insert([
            ['dni' => 'P1', 'rol' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['dni' => 'P2', 'rol' => 3, 'created_at' => now(), 'updated_at' => now()],
        ]);
        DB::table('reuniones')->insert([
            [
                'id' => 1,
                'idProfesor' => 'P1',
                'curso' => '2026-2027',
                'fecha' => '2026-09-01 10:00:00',
                'descripcion' => 'Reunió pròpia',
                'idEspacio' => 'A101',
                'archivada' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'idProfesor' => 'P2',
                'curso' => '2026-2027',
                'fecha' => '2026-09-02 10:00:00',
                'descripcion' => 'Reunió aliena',
                'idEspacio' => 'A102',
                'archivada' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
        DB::table('ordenes_reuniones')->insert([
            'id' => 22,
            'idReunion' => 2,
            'orden' => 1,
            'descripcion' => 'Punt alié',
        ]);
    }
}
