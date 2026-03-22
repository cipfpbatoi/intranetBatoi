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
 * Proves feature de FctController amb autenticació Sanctum.
 */
class ApiFctControllerFeatureTest extends TestCase
{
    private string $sqlitePath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sqlitePath = storage_path('api_fct_controller_feature_testing.sqlite');
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
        Schema::connection('sqlite')->dropIfExists('activities');
        Schema::connection('sqlite')->dropIfExists('alumno_fcts');
        Schema::connection('sqlite')->dropIfExists('profesores');

        if (file_exists($this->sqlitePath)) {
            @unlink($this->sqlitePath);
        }

        parent::tearDown();
    }

    public function test_seguimiento_retorna_401_si_no_autenticat(): void
    {
        $response = $this->postJson('/api/fct/999/alFct', ['explicacion' => 'test']);

        $response->assertStatus(401);
    }

    public function test_seguimiento_amb_sanctum_crea_activity_review(): void
    {
        $this->insertProfesor('PFCT01');
        $this->insertAlumnoFct(123, 'PFCT01');
        $user = Profesor::on('sqlite')->findOrFail('PFCT01');
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/fct/123/alFct', ['explicacion' => 'Seguiment des de test']);

        $response->assertOk();
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('data.action', 'review');
        $response->assertJsonPath('data.model_id', '123');
        $response->assertJsonPath('data.author_id', 'PFCT01');
        $response->assertJsonPath('data.comentari', 'Seguiment des de test');

        $this->assertDatabaseHas('activities', [
            'action' => 'review',
            'model_id' => 123,
            'author_id' => 'PFCT01',
            'comentari' => 'Seguiment des de test',
        ]);
    }

    public function test_telefonico_amb_sanctum_fa_upsert_diari_sobre_fct(): void
    {
        $this->insertProfesor('PFCT02');
        DB::table('fcts')->insert([
            'id' => 40,
            'idColaboracion' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $user = Profesor::on('sqlite')->findOrFail('PFCT02');
        Sanctum::actingAs($user);

        $response1 = $this->postJson('/api/fct/40/telefonico', ['explicacion' => 'Telefonada 1']);

        $response1->assertOk();
        $response1->assertJsonPath('success', true);
        $this->assertSame(1, DB::table('activities')->where('action', 'phone')->where('model_id', 40)->count());

        $response2 = $this->postJson('/api/fct/40/telefonico', ['explicacion' => 'Telefonada 2']);

        $response2->assertOk();
        $response2->assertJsonPath('success', true);
        $this->assertSame(1, DB::table('activities')->where('action', 'phone')->where('model_id', 40)->count());
        $this->assertSame(
            'Telefonada 2',
            DB::table('activities')->where('action', 'phone')->where('model_id', 40)->value('comentari')
        );
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

        if (!Schema::connection('sqlite')->hasTable('activities')) {
            Schema::connection('sqlite')->create('activities', function (Blueprint $table): void {
                $table->increments('id');
                $table->string('action')->nullable();
                $table->string('model_class')->nullable();
                $table->unsignedInteger('model_id')->nullable();
                $table->string('author_id', 10)->nullable();
                $table->text('comentari')->nullable();
                $table->string('document')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::connection('sqlite')->hasTable('alumno_fcts')) {
            Schema::connection('sqlite')->create('alumno_fcts', function (Blueprint $table): void {
                $table->increments('id');
                $table->unsignedInteger('idFct')->nullable();
                $table->string('idAlumno', 20)->nullable();
                $table->string('idProfesor', 10)->nullable();
                $table->unsignedInteger('horas')->default(0);
                $table->unsignedTinyInteger('pg0301')->default(0);
                $table->unsignedTinyInteger('a56')->default(0);
            });
        }

        if (!Schema::connection('sqlite')->hasTable('fcts')) {
            Schema::connection('sqlite')->create('fcts', function (Blueprint $table): void {
                $table->increments('id');
                $table->unsignedInteger('idColaboracion')->nullable();
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

    private function insertAlumnoFct(int $id, string $idProfesor): void
    {
        DB::table('alumno_fcts')->insert([
            'id' => $id,
            'idFct' => 1,
            'idAlumno' => 'A001',
            'idProfesor' => $idProfesor,
            'horas' => 0,
            'pg0301' => 0,
            'a56' => 0,
        ]);
    }
}
