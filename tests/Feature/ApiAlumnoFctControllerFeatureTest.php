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
 * Proves feature d'AlumnoFct amb autenticació Sanctum.
 */
class ApiAlumnoFctControllerFeatureTest extends TestCase
{
    private string $sqlitePath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sqlitePath = storage_path('api_alumnofct_controller_feature_testing.sqlite');
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
        Schema::connection('sqlite')->dropIfExists('alumno_fcts');
        Schema::connection('sqlite')->dropIfExists('profesores');

        if (file_exists($this->sqlitePath)) {
            @unlink($this->sqlitePath);
        }

        parent::tearDown();
    }

    public function test_update_canvia_pg0301_i_a56(): void
    {
        $this->insertProfesor('PAF01');
        $user = Profesor::on('sqlite')->findOrFail('PAF01');
        Sanctum::actingAs($user);

        DB::table('alumno_fcts')->insert([
            'id' => 101,
            'idFct' => 1,
            'idAlumno' => '10802710',
            'idProfesor' => 'PAF01',
            'horas' => 380,
            'pg0301' => 0,
            'a56' => 0,
        ]);

        $response = $this->putJson('/api/alumnofct/101', [
            'pg0301' => 'true',
            'a56' => 'true',
        ]);

        $response->assertOk();
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('data.updated', true);

        $this->assertSame(1, (int) DB::table('alumno_fcts')->where('id', 101)->value('pg0301'));
        $this->assertSame(1, (int) DB::table('alumno_fcts')->where('id', 101)->value('a56'));
    }

    public function test_show_retorna_detall_basic_sense_dependre_de_relacions_fragils(): void
    {
        $this->insertProfesor('PAF02');
        $user = Profesor::on('sqlite')->findOrFail('PAF02');
        Sanctum::actingAs($user);

        DB::table('alumno_fcts')->insert([
            'id' => 102,
            'idFct' => 7,
            'idAlumno' => '10802711',
            'idProfesor' => 'PAF02',
            'horas' => 400,
            'pg0301' => 1,
            'a56' => 0,
        ]);

        $response = $this->getJson('/api/alumnofct/102');

        $response->assertOk();
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('data.id', 102);
        $response->assertJsonPath('data.idFct', 7);
        $response->assertJsonPath('data.idAlumno', '10802711');
        $response->assertJsonPath('data.profesor', 'PAF02');
        $response->assertJsonPath('data.horas', 400);
    }

    public function test_edit_retorna_els_camps_actuals_del_modal(): void
    {
        $this->insertProfesor('PAF03');
        $user = Profesor::on('sqlite')->findOrFail('PAF03');
        Sanctum::actingAs($user);

        DB::table('alumno_fcts')->insert([
            'id' => 103,
            'idFct' => 9,
            'idAlumno' => '10802712',
            'idProfesor' => 'PAF03',
            'desde' => '2026-03-01',
            'hasta' => '2026-06-30',
            'beca' => 1,
            'autorizacion' => 1,
            'flexible' => 0,
            'valoracio' => 'Text de prova',
            'horas' => 220,
            'pg0301' => 0,
            'a56' => 0,
        ]);

        $response = $this->getJson('/api/alumnofct/103/edit');

        $response->assertOk();
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('data.id', 103);
        $response->assertJsonPath('data.desde', '01-03-2026');
        $response->assertJsonPath('data.hasta', '30-06-2026');
        $response->assertJsonPath('data.beca', 1);
        $response->assertJsonPath('data.autorizacion', 1);
        $response->assertJsonPath('data.flexible', 0);
        $response->assertJsonPath('data.valoracio', 'Text de prova');
    }

    public function test_edit_admet_api_token_legacy_per_compatibilitat_frontend(): void
    {
        $legacyToken = bin2hex(random_bytes(20));
        $this->insertProfesor('PAF04', $legacyToken);

        DB::table('alumno_fcts')->insert([
            'id' => 104,
            'idFct' => 10,
            'idAlumno' => '10802713',
            'idProfesor' => 'PAF04',
            'desde' => '2026-04-01',
            'hasta' => '2026-06-15',
            'horas' => 180,
            'pg0301' => 0,
            'a56' => 0,
        ]);

        $response = $this->getJson('/api/alumnofct/104/edit?api_token=' . $legacyToken);

        $response->assertOk();
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('data.id', 104);
        $response->assertJsonPath('data.desde', '01-04-2026');
    }

    public function test_edit_accepta_sessio_web_stateful_sense_token_api(): void
    {
        $this->insertProfesor('PAF05');
        $user = Profesor::on('sqlite')->findOrFail('PAF05');
        $this->actingAs($user, 'profesor');

        DB::table('alumno_fcts')->insert([
            'id' => 105,
            'idFct' => 11,
            'idAlumno' => '10802714',
            'idProfesor' => 'PAF05',
            'desde' => '2026-04-10',
            'hasta' => '2026-06-10',
            'horas' => 200,
            'pg0301' => 0,
            'a56' => 0,
        ]);

        $response = $this
            ->withHeader('Origin', 'http://localhost')
            ->withHeader('Referer', 'http://localhost/alumnofct')
            ->getJson('/api/alumnofct/105/edit');

        $response->assertOk();
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('data.id', 105);
        $response->assertJsonPath('data.desde', '10-04-2026');
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

        if (!Schema::connection('sqlite')->hasTable('alumno_fcts')) {
            Schema::connection('sqlite')->create('alumno_fcts', function (Blueprint $table): void {
                $table->increments('id');
                $table->unsignedInteger('idFct')->nullable();
                $table->string('idAlumno', 20)->nullable();
                $table->string('idProfesor', 10)->nullable();
                $table->date('desde')->nullable();
                $table->date('hasta')->nullable();
                $table->unsignedTinyInteger('beca')->default(0);
                $table->unsignedTinyInteger('autorizacion')->default(0);
                $table->unsignedTinyInteger('flexible')->default(0);
                $table->text('valoracio')->nullable();
                $table->unsignedInteger('horas')->default(0);
                $table->unsignedTinyInteger('pg0301')->default(0);
                $table->unsignedTinyInteger('a56')->default(0);
            });
        }
    }

    private function insertProfesor(string $dni, ?string $apiToken = null): void
    {
        DB::table('profesores')->insert([
            'dni' => $dni,
            'codigo' => random_int(1000, 9999),
            'nombre' => 'Nom',
            'apellido1' => 'Cognom1',
            'apellido2' => 'Cognom2',
            'email' => strtolower($dni) . '@test.local',
            'rol' => config('roles.rol.profesor'),
            'api_token' => $apiToken ?? bin2hex(random_bytes(20)),
            'fecha_baja' => null,
            'activo' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
