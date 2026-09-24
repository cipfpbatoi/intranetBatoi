<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Schema;
use Intranet\Entities\Profesor;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Proves HTTP del contracte de seguretat de reunions via Sanctum.
 */
class ApiReunionAuthorizationFeatureTest extends TestCase
{
    private string $sqlitePath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sqlitePath = storage_path('api_reunion_authorization_testing.sqlite');
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

    public function test_show_permet_assistent_i_direccio_pero_denega_professor_alie(): void
    {
        $this->authenticate('P2');
        $this->getJson('/api/reunion/1')->assertOk()->assertJsonPath('data.id', 1);

        $this->authenticate('DIR1');
        $this->getJson('/api/reunion/1')->assertOk();

        $this->authenticate('P3');
        $this->getJson('/api/reunion/1')->assertForbidden();
    }

    public function test_index_no_enumera_reunions_alienes(): void
    {
        $this->authenticate('P2');

        $response = $this->getJson('/api/reunion');

        $response->assertOk()->assertJsonCount(1, 'data')->assertJsonPath('data.0.id', 1);

        $this->getJson('/api/ordenreunion')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', 11);
    }

    public function test_update_no_permet_canviar_propietat_estat_o_fitxer(): void
    {
        $this->authenticate('P1');

        $this->putJson('/api/reunion/1', [
            'descripcion' => 'Descripció actualitzada',
            'idProfesor' => 'P3',
            'archivada' => true,
            'fichero' => 'injectat.pdf',
        ])->assertOk();

        $this->assertDatabaseHas('reuniones', [
            'id' => 1,
            'descripcion' => 'Descripció actualitzada',
            'idProfesor' => 'P1',
            'archivada' => false,
            'fichero' => null,
        ]);
    }

    public function test_mutacions_deneguen_aliens_i_actes_arxivades(): void
    {
        $this->authenticate('P3');
        $this->putJson('/api/reunion/1', ['descripcion' => 'Intent alié'])->assertForbidden();
        $this->deleteJson('/api/reunion/1')->assertForbidden();

        $this->authenticate('P1');
        $this->putJson('/api/reunion/3', ['descripcion' => 'Intent arxivat'])->assertForbidden();
        $this->deleteJson('/api/reunion/3')->assertForbidden();
    }

    public function test_update_d_un_punt_no_permet_canviar_la_reunio_pare(): void
    {
        $this->authenticate('P1');

        $this->putJson('/api/ordenreunion/11', [
            'resumen' => 'Resum segur',
            'idReunion' => 2,
        ])->assertOk();

        $this->assertDatabaseHas('ordenes_reuniones', [
            'id' => 11,
            'idReunion' => 1,
            'resumen' => 'Resum segur',
        ]);

        $this->authenticate('P3');
        $this->putJson('/api/ordenreunion/11', ['resumen' => 'Intent alié'])->assertForbidden();
    }

    public function test_assistencia_valida_autoritzacio_i_pertinenca(): void
    {
        $this->authenticate('P1');
        $this->putJson('/api/asistencia/cambiar', [
            'idReunion' => 1,
            'idProfesor' => 'P2',
            'asiste' => false,
        ])->assertOk();
        $this->assertDatabaseHas('asistencias', [
            'idReunion' => 1,
            'idProfesor' => 'P2',
            'asiste' => false,
        ]);

        $this->putJson('/api/asistencia/cambiar', [
            'idReunion' => 1,
            'idProfesor' => 'P3',
            'asiste' => true,
        ])->assertNotFound();

        $this->authenticate('P3');
        $this->putJson('/api/asistencia/cambiar', [
            'idReunion' => 1,
            'idProfesor' => 'P2',
            'asiste' => true,
        ])->assertForbidden();
    }

    public function test_valoracio_alumne_no_actualitza_un_pivot_d_una_altra_reunio(): void
    {
        $this->authenticate('P1');

        $this->putJson('/api/reunion/1/alumno/A2', ['capacitats' => 7])->assertNotFound();
        $this->assertDatabaseHas('alumno_reuniones', [
            'idReunion' => 2,
            'idAlumno' => 'A2',
            'capacitats' => 3,
        ]);
    }

    public function test_store_forca_el_convocant_autenticat(): void
    {
        $this->authenticate('P1');

        $response = $this->postJson('/api/reunion', [
            'tipo' => 2,
            'curso' => '2026-2027',
            'fecha' => '2026-10-01 10:00:00',
            'descripcion' => 'Reunió API',
            'idEspacio' => 'A101',
            'idProfesor' => 'P3',
            'archivada' => true,
            'fichero' => 'injectat.pdf',
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('reuniones', [
            'id' => $response->json('data.id'),
            'idProfesor' => 'P1',
            'archivada' => false,
            'fichero' => null,
        ]);
    }

    private function authenticate(string $dni): void
    {
        Sanctum::actingAs(Profesor::query()->findOrFail($dni));
    }

    private function createSchema(): void
    {
        Schema::create('profesores', function (Blueprint $table): void {
            $table->string('dni', 10)->primary();
            $table->unsignedInteger('rol')->default(3);
            $table->string('sustituye_a', 10)->nullable();
            $table->string('api_token', 80)->nullable();
            $table->timestamps();
        });
        Schema::create('grupos', function (Blueprint $table): void {
            $table->string('codigo', 10)->primary();
            $table->string('tutor', 10)->nullable();
            $table->timestamps();
        });
        Schema::create('reuniones', function (Blueprint $table): void {
            $table->increments('id');
            $table->unsignedTinyInteger('tipo')->default(2);
            $table->string('grupo')->nullable();
            $table->string('idGrupo', 10)->nullable();
            $table->string('curso')->nullable();
            $table->unsignedTinyInteger('numero')->nullable();
            $table->dateTime('fecha')->nullable();
            $table->string('descripcion', 120);
            $table->text('objetivos')->nullable();
            $table->string('idProfesor', 10);
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
            $table->string('descripcion', 120);
            $table->text('resumen')->nullable();
        });
        Schema::create('alumnos', function (Blueprint $table): void {
            $table->string('nia', 15)->primary();
        });
        Schema::create('alumno_reuniones', function (Blueprint $table): void {
            $table->unsignedInteger('idReunion');
            $table->string('idAlumno', 15);
            $table->unsignedTinyInteger('capacitats')->default(0);
        });
    }

    private function seedData(): void
    {
        DB::table('profesores')->insert([
            ['dni' => 'P1', 'rol' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['dni' => 'P2', 'rol' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['dni' => 'P3', 'rol' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['dni' => 'DIR1', 'rol' => 6, 'created_at' => now(), 'updated_at' => now()],
        ]);
        DB::table('reuniones')->insert([
            [
                'id' => 1,
                'tipo' => 2,
                'curso' => '2026-2027',
                'fecha' => '2026-09-01 10:00:00',
                'descripcion' => 'Reunió pròpia',
                'idProfesor' => 'P1',
                'idEspacio' => 'A101',
                'archivada' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'tipo' => 2,
                'curso' => '2026-2027',
                'fecha' => '2026-09-02 10:00:00',
                'descripcion' => 'Reunió aliena',
                'idProfesor' => 'P3',
                'idEspacio' => 'A102',
                'archivada' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'tipo' => 2,
                'curso' => '2026-2027',
                'fecha' => '2026-09-03 10:00:00',
                'descripcion' => 'Reunió arxivada',
                'idProfesor' => 'P1',
                'idEspacio' => 'A103',
                'archivada' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
        DB::table('asistencias')->insert([
            'idReunion' => 1,
            'idProfesor' => 'P2',
            'asiste' => true,
        ]);
        DB::table('ordenes_reuniones')->insert([
            ['id' => 11, 'idReunion' => 1, 'orden' => 1, 'descripcion' => 'Punt propi'],
            ['id' => 22, 'idReunion' => 2, 'orden' => 1, 'descripcion' => 'Punt alié'],
        ]);
        DB::table('alumnos')->insert([['nia' => 'A1'], ['nia' => 'A2']]);
        DB::table('alumno_reuniones')->insert([
            ['idReunion' => 1, 'idAlumno' => 'A1', 'capacitats' => 3],
            ['idReunion' => 2, 'idAlumno' => 'A2', 'capacitats' => 3],
        ]);
    }
}
