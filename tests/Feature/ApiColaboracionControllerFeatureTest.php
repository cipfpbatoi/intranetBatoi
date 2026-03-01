<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Intranet\Entities\Profesor;
use Tests\TestCase;

class ApiColaboracionControllerFeatureTest extends TestCase
{
    private string $sqlitePath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sqlitePath = storage_path('api_colaboracion_controller_feature_testing.sqlite');
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
        Schema::connection('sqlite')->dropIfExists('personal_access_tokens');
        Schema::connection('sqlite')->dropIfExists('activities');
        Schema::connection('sqlite')->dropIfExists('fcts');
        Schema::connection('sqlite')->dropIfExists('colaboraciones');
        Schema::connection('sqlite')->dropIfExists('profesores');

        if (file_exists($this->sqlitePath)) {
            @unlink($this->sqlitePath);
        }

        parent::tearDown();
    }

    public function test_resolve_retorna_404_si_no_existix_colaboracio(): void
    {
        $this->insertProfesor('PA01', 'token-a');
        $user = Profesor::on('sqlite')->findOrFail('PA01');
        $this->actingAs($user, 'api');

        $response = $this->getJson('/api/colaboracion/9999/resolve');

        $response->assertStatus(404);
        $response->assertJsonPath('success', false);
    }

    public function test_switch_assigna_tutor_amb_api_token(): void
    {
        $this->insertProfesor('PA02', 'token-auth');
        $this->insertProfesor('PT02', 'token-target');

        DB::table('colaboraciones')->insert([
            'id' => 20,
            'idCentro' => 1,
            'idCiclo' => 1,
            'estado' => 1,
            'tutor' => null,
            'puestos' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->getJson('/api/colaboracion/20/switch?api_token=token-target');

        $response->assertOk();
        $response->assertJsonPath('success', true);
        $this->assertSame('PT02', DB::table('colaboraciones')->where('id', 20)->value('tutor'));
    }

    public function test_book_fa_upsert_diari(): void
    {
        $this->insertProfesor('PA03', 'token-auth');

        DB::table('colaboraciones')->insert([
            'id' => 30,
            'idCentro' => 1,
            'idCiclo' => 1,
            'estado' => 1,
            'tutor' => 'PA03',
            'puestos' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $user = Profesor::on('sqlite')->findOrFail('PA03');
        $this->actingAs($user, 'api');

        $response1 = $this->postJson('/api/colaboracion/30/book', ['explicacion' => 'Primer contacte']);
        $response1->assertOk();
        $response1->assertJsonPath('success', true);
        $this->assertSame(1, DB::table('activities')->where('action', 'book')->where('model_id', 30)->count());

        $response2 = $this->postJson('/api/colaboracion/30/book', ['explicacion' => 'Contacte actualitzat']);
        $response2->assertOk();
        $response2->assertJsonPath('success', true);

        $this->assertSame(1, DB::table('activities')->where('action', 'book')->where('model_id', 30)->count());
        $this->assertSame(
            'Contacte actualitzat',
            DB::table('activities')->where('action', 'book')->where('model_id', 30)->value('comentari')
        );
    }

    public function test_telefon_fa_upsert_diari(): void
    {
        $this->insertProfesor('PA04', 'token-auth');

        DB::table('fcts')->insert([
            'id' => 40,
            'idColaboracion' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $user = Profesor::on('sqlite')->findOrFail('PA04');
        $this->actingAs($user, 'api');

        $response1 = $this->postJson('/api/colaboracion/40/telefonico', ['explicacion' => 'Telefonada 1']);
        $response1->assertOk();
        $response1->assertJsonPath('success', true);
        $this->assertSame(1, DB::table('activities')->where('action', 'phone')->where('model_id', 40)->count());

        $response2 = $this->postJson('/api/colaboracion/40/telefonico', ['explicacion' => 'Telefonada 2']);
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
                $table->string('nombre')->nullable();
                $table->string('apellido1')->nullable();
                $table->string('apellido2')->nullable();
                $table->string('email')->nullable();
                $table->unsignedInteger('rol')->default(3);
                $table->string('api_token', 80)->nullable();
                $table->date('fecha_baja')->nullable();
                $table->boolean('activo')->default(true);
            });
        }

        if (!Schema::connection('sqlite')->hasTable('colaboraciones')) {
            Schema::connection('sqlite')->create('colaboraciones', function (Blueprint $table): void {
                $table->increments('id');
                $table->unsignedInteger('idCentro');
                $table->unsignedInteger('idCiclo');
                $table->string('contacto')->nullable();
                $table->string('telefono')->nullable();
                $table->string('email')->nullable();
                $table->unsignedInteger('puestos')->default(1);
                $table->string('tutor')->nullable();
                $table->unsignedTinyInteger('estado')->default(1);
                $table->timestamps();
            });
        }

        if (!Schema::connection('sqlite')->hasTable('fcts')) {
            Schema::connection('sqlite')->create('fcts', function (Blueprint $table): void {
                $table->increments('id');
                $table->unsignedInteger('idColaboracion')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::connection('sqlite')->hasTable('activities')) {
            Schema::connection('sqlite')->create('activities', function (Blueprint $table): void {
                $table->increments('id');
                $table->string('action')->nullable();
                $table->string('model_class')->nullable();
                $table->unsignedInteger('model_id')->nullable();
                $table->text('comentari')->nullable();
                $table->string('document')->nullable();
                $table->string('author_id')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::connection('sqlite')->hasTable('personal_access_tokens')) {
            Schema::connection('sqlite')->create('personal_access_tokens', function (Blueprint $table): void {
                $table->id();
                $table->string('tokenable_type');
                $table->string('tokenable_id');
                $table->string('name');
                $table->string('token', 64)->unique();
                $table->text('abilities')->nullable();
                $table->timestamp('last_used_at')->nullable();
                $table->timestamp('expires_at')->nullable();
                $table->timestamps();
            });
        }
    }

    private function insertProfesor(string $dni, string $apiToken): void
    {
        DB::table('profesores')->insert([
            'dni' => $dni,
            'nombre' => 'Nom',
            'apellido1' => 'Cognom1',
            'apellido2' => 'Cognom2',
            'email' => strtolower($dni) . '@test.local',
            'rol' => config('roles.rol.profesor'),
            'api_token' => $apiToken,
            'fecha_baja' => null,
            'activo' => 1,
        ]);
    }
}
