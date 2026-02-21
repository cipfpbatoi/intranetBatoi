<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Intranet\Entities\Profesor;
use Tests\TestCase;

class ApiHorarioControllerFeatureTest extends TestCase
{
    private string $sqlitePath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sqlitePath = storage_path('api_horario_controller_feature_testing.sqlite');
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
        Schema::connection('sqlite')->dropIfExists('horarios');
        Schema::connection('sqlite')->dropIfExists('profesores');

        if (file_exists($this->sqlitePath)) {
            @unlink($this->sqlitePath);
        }

        parent::tearDown();
    }

    public function test_horario_api_retorna_401_si_no_autenticat(): void
    {
        $response = $this->getJson('/api/horario/1');

        $response->assertStatus(401);
    }

    public function test_show_filtra_per_professor_i_sustitucio(): void
    {
        $this->insertProfesor('PBASE1', sustituyeA: ' ');
        $this->insertProfesor('PSUB01', sustituyeA: 'PBASE1');
        $this->insertProfesor('PALTRE', sustituyeA: ' ');

        $user = Profesor::on('sqlite')->findOrFail('PSUB01');
        $this->actingAs($user, 'api');

        DB::table('horarios')->insert([
            [
                'idProfesor' => 'PSUB01',
                'dia_semana' => 'L',
                'sesion_orden' => 1,
                'modulo' => 'M1',
                'idGrupo' => 'G1',
                'ocupacion' => null,
                'aula' => 'A1',
                'plantilla' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'idProfesor' => 'PBASE1',
                'dia_semana' => 'M',
                'sesion_orden' => 2,
                'modulo' => 'M2',
                'idGrupo' => 'G1',
                'ocupacion' => null,
                'aula' => 'A2',
                'plantilla' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'idProfesor' => 'PALTRE',
                'dia_semana' => 'X',
                'sesion_orden' => 3,
                'modulo' => 'M3',
                'idGrupo' => 'G2',
                'ocupacion' => null,
                'aula' => 'A3',
                'plantilla' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $response = $this->getJson('/api/horario/idProfesor=PSUB01');

        $response->assertOk();
        $response->assertJsonPath('success', true);
        $response->assertJsonCount(2, 'data');
        $response->assertJsonFragment(['idProfesor' => 'PSUB01']);
        $response->assertJsonFragment(['idProfesor' => 'PBASE1']);
    }

    public function test_index_modern_filtra_per_query_i_sustitucio(): void
    {
        $this->insertProfesor('PBASE2', sustituyeA: ' ');
        $this->insertProfesor('PSUB02', sustituyeA: 'PBASE2');
        $this->insertProfesor('PALT02', sustituyeA: ' ');

        $user = Profesor::on('sqlite')->findOrFail('PSUB02');
        $this->actingAs($user, 'api');

        DB::table('horarios')->insert([
            [
                'idProfesor' => 'PSUB02',
                'dia_semana' => 'L',
                'sesion_orden' => 1,
                'modulo' => 'M1',
                'idGrupo' => 'G1',
                'ocupacion' => null,
                'aula' => 'A1',
                'plantilla' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'idProfesor' => 'PBASE2',
                'dia_semana' => 'M',
                'sesion_orden' => 2,
                'modulo' => 'M2',
                'idGrupo' => 'G1',
                'ocupacion' => null,
                'aula' => 'A2',
                'plantilla' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'idProfesor' => 'PALT02',
                'dia_semana' => 'X',
                'sesion_orden' => 3,
                'modulo' => 'M3',
                'idGrupo' => 'G2',
                'ocupacion' => null,
                'aula' => 'A3',
                'plantilla' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $response = $this->getJson('/api/horario?idProfesor=PSUB02');

        $response->assertOk();
        $response->assertHeaderMissing('Deprecation');
        $response->assertJsonPath('success', true);
        $response->assertJsonCount(2, 'data');
        $response->assertJsonFragment(['idProfesor' => 'PSUB02']);
        $response->assertJsonFragment(['idProfesor' => 'PBASE2']);
    }

    public function test_horario_change_guarda_i_getchange_ho_retorna(): void
    {
        Storage::fake('local');

        $this->insertProfesor('PCHG01', sustituyeA: ' ');
        $user = Profesor::on('sqlite')->findOrFail('PCHG01');
        $this->actingAs($user, 'api');

        $payload = '{"estat":"pendent","canvis":[{"de":"1-L","a":"2-M"}]}';

        $post = $this->postJson('/api/horarioChange/PCHG01', [
            'data' => $payload,
        ]);

        $post->assertOk();
        $post->assertJsonPath('success', true);

        $get = $this->getJson('/api/horarioChange/PCHG01');

        $get->assertOk();
        $get->assertJsonPath('success', true);
        $this->assertSame($payload, $get->json('data'));
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
                $table->string('sustituye_a', 10)->nullable();
                $table->date('fecha_baja')->nullable();
                $table->boolean('activo')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::connection('sqlite')->hasTable('horarios')) {
            Schema::connection('sqlite')->create('horarios', function (Blueprint $table): void {
                $table->increments('id');
                $table->string('idProfesor', 10);
                $table->string('modulo')->nullable();
                $table->string('idGrupo')->nullable();
                $table->string('ocupacion')->nullable();
                $table->string('aula')->nullable();
                $table->string('dia_semana', 1)->nullable();
                $table->unsignedInteger('sesion_orden')->nullable();
                $table->unsignedTinyInteger('plantilla')->default(0);
                $table->timestamps();
            });
        }
    }

    private function insertProfesor(string $dni, string $sustituyeA): void
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
            'sustituye_a' => $sustituyeA,
            'fecha_baja' => null,
            'activo' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
