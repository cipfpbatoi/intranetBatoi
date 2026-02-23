<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Intranet\Application\Grupo\GrupoService;
use Intranet\Entities\Activity;
use Intranet\Entities\Grupo;
use Intranet\Entities\Profesor;
use Intranet\Services\HR\PresenciaResumenService;
use Mockery;
use Tests\TestCase;

class ApiBatch1ModernizationFeatureTest extends TestCase
{
    private string $sqlitePath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sqlitePath = storage_path('api_batch1_modernization_feature_testing.sqlite');
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
        Schema::connection('sqlite')->dropIfExists('profesores');

        if (file_exists($this->sqlitePath)) {
            @unlink($this->sqlitePath);
        }

        Mockery::close();
        parent::tearDown();
    }

    public function test_activity_move_crea_nova_evidencia(): void
    {
        $this->insertProfesor('PAPI01');
        $user = Profesor::on('sqlite')->findOrFail('PAPI01');
        $this->actingAs($user, 'api');

        Activity::on('sqlite')->create([
            'action' => 'phone',
            'model_class' => 'Intranet\\Entities\\Fct',
            'model_id' => 100,
            'author_id' => 'PAPI01',
            'document' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $originId = (int) Activity::on('sqlite')->firstOrFail()->id;

        $response = $this->getJson('/api/activity/'.$originId.'/move/200');

        $response->assertOk();
        $response->assertJsonPath('success', true);
        $this->assertDatabaseHas('activities', [
            'action' => 'phone',
            'model_id' => 200,
            'author_id' => 'PAPI01',
        ]);
    }

    public function test_activity_move_duplicada_torna_contracte_error_unificat(): void
    {
        $this->insertProfesor('PAPI11');
        $user = Profesor::on('sqlite')->findOrFail('PAPI11');
        $this->actingAs($user, 'api');

        Activity::on('sqlite')->create([
            'action' => 'phone',
            'model_class' => 'Intranet\\Entities\\Fct',
            'model_id' => 100,
            'author_id' => 'PAPI11',
            'document' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        Activity::on('sqlite')->create([
            'action' => 'phone',
            'model_class' => 'Intranet\\Entities\\Fct',
            'model_id' => 200,
            'author_id' => 'PAPI11',
            'document' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $originId = (int) Activity::on('sqlite')
            ->where('model_id', 100)
            ->value('id');

        $response = $this->getJson('/api/activity/'.$originId.'/move/200');

        $response->assertStatus(400);
        $response->assertJsonPath('success', false);
        $response->assertJsonPath('message', 'Eixa evidencia ja existeix');
    }

    public function test_grupo_list_torna_alumnes_ordenats(): void
    {
        $this->insertProfesor('PAPI02');
        $user = Profesor::on('sqlite')->findOrFail('PAPI02');
        $this->actingAs($user, 'api');

        $mockGroup = new Grupo();
        $mockGroup->setRelation('Alumnos', collect([
            (object) ['id' => '2', 'nameFull' => 'Zulu B'],
            (object) ['id' => '1', 'nameFull' => 'Alpha A'],
        ]));

        $mockService = Mockery::mock(GrupoService::class);
        $mockService->shouldReceive('find')
            ->once()
            ->with('G1')
            ->andReturn($mockGroup);

        $this->app->instance(GrupoService::class, $mockService);

        $response = $this->getJson('/api/grupo/list/G1');

        $response->assertOk();
        $response->assertJsonPath('data.0.id', '1');
        $response->assertJsonPath('data.0.texto', 'Alpha A');
        $response->assertJsonPath('data.1.id', '2');
        $response->assertJsonPath('data.1.texto', 'Zulu B');
    }

    public function test_presencia_resumen_rango_torna_resum_per_dia_i_professor(): void
    {
        $this->insertProfesor('PAPI03');

        $mockService = Mockery::mock(PresenciaResumenService::class);
        $mockService->shouldReceive('resumenDia')
            ->once()
            ->withArgs(function (string $dia, Collection $profes): bool {
                return $dia === '2026-03-10'
                    && $profes->pluck('dni')->contains('PAPI03');
            })
            ->andReturn([
                [
                    'dni' => 'PAPI03',
                    'status' => 'OK',
                    'planned_docencia_minutes' => 60,
                    'planned_altres_minutes' => 30,
                    'covered_docencia_minutes' => 60,
                    'covered_altres_minutes' => 30,
                    'in_center_minutes' => 90,
                    'has_open_stay' => false,
                    'first_entry' => '08:00:00',
                ],
            ]);
        $this->app->instance(PresenciaResumenService::class, $mockService);

        $response = $this->getJson('/api/presencia/resumen-rango?desde=2026-03-10&hasta=2026-03-10&dni=PAPI03');

        $response->assertOk();
        $response->assertJsonPath('0.dni', 'PAPI03');
        $response->assertJsonPath('0.days.2026-03-10.status', 'OK');
        $response->assertJsonPath('0.days.2026-03-10.in_center_minutes', 90);
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
                $table->string('departamento')->nullable();
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
    }

    private function insertProfesor(string $dni): void
    {
        DB::table('profesores')->insert([
            'dni' => $dni,
            'codigo' => random_int(1000, 9999),
            'nombre' => 'Nom',
            'apellido1' => 'Cognom1',
            'apellido2' => 'Cognom2',
            'departamento' => 'DEP1',
            'email' => strtolower($dni).'@test.local',
            'rol' => config('roles.rol.profesor'),
            'api_token' => bin2hex(random_bytes(20)),
            'fecha_baja' => null,
            'activo' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
