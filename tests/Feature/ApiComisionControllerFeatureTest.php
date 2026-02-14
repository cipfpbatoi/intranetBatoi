<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Intranet\Entities\Profesor;
use Tests\TestCase;

class ApiComisionControllerFeatureTest extends TestCase
{
    private string $sqlitePath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sqlitePath = storage_path('api_comision_controller_feature_testing.sqlite');
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
        Schema::connection('sqlite')->dropIfExists('comisiones');
        Schema::connection('sqlite')->dropIfExists('profesores');

        if (file_exists($this->sqlitePath)) {
            @unlink($this->sqlitePath);
        }

        parent::tearDown();
    }

    public function test_autorizar_retorna_401_si_no_autenticat(): void
    {
        $response = $this->getJson('/api/autorizar/comision');

        $response->assertStatus(401);
    }

    public function test_autorizar_filtra_estats_i_soft_delete(): void
    {
        $this->insertProfesor('PC01', 'Nom', 'Cognom1', 'Cognom2');

        $usuario = Profesor::on('sqlite')->findOrFail('PC01');
        $this->actingAs($usuario, 'api');

        DB::table('comisiones')->insert([
            [
                'idProfesor' => 'PC01',
                'servicio' => 'Comissio 1',
                'estado' => 1,
                'desde' => '2026-02-14 09:00:00',
                'hasta' => '2026-02-14 12:00:00',
                'deleted_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'idProfesor' => 'PC01',
                'servicio' => 'Comissio 2',
                'estado' => 2,
                'desde' => '2026-02-15 09:00:00',
                'hasta' => '2026-02-15 12:00:00',
                'deleted_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'idProfesor' => 'PC01',
                'servicio' => 'Exclosa estat',
                'estado' => 3,
                'desde' => '2026-02-16 09:00:00',
                'hasta' => '2026-02-16 12:00:00',
                'deleted_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'idProfesor' => 'PC01',
                'servicio' => 'Exclosa soft delete',
                'estado' => 1,
                'desde' => '2026-02-17 09:00:00',
                'hasta' => '2026-02-17 12:00:00',
                'deleted_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $response = $this->getJson('/api/autorizar/comision');

        $response->assertOk();
        $response->assertJsonPath('success', true);
        $response->assertJsonCount(2, 'data');
        $response->assertJsonFragment(['servicio' => 'Comissio 1']);
        $response->assertJsonFragment(['servicio' => 'Comissio 2']);
    }

    public function test_prepay_actualitza_a_estat_6_nom_esborranys_del_professor(): void
    {
        $this->insertProfesor('PC02', 'Usuari', 'Test', 'Api');

        $usuario = Profesor::on('sqlite')->findOrFail('PC02');
        $this->actingAs($usuario, 'api');

        DB::table('comisiones')->insert([
            [
                'idProfesor' => 'PC02',
                'servicio' => 'Canvia',
                'estado' => 4,
                'desde' => '2026-02-14 09:00:00',
                'hasta' => '2026-02-14 12:00:00',
                'deleted_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'idProfesor' => 'PC02',
                'servicio' => 'No canvia',
                'estado' => 3,
                'desde' => '2026-02-15 09:00:00',
                'hasta' => '2026-02-15 12:00:00',
                'deleted_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'idProfesor' => 'PC99',
                'servicio' => 'Altre professor',
                'estado' => 4,
                'desde' => '2026-02-16 09:00:00',
                'hasta' => '2026-02-16 12:00:00',
                'deleted_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $response = $this->putJson('/api/comision/PC02/prePay');

        $response->assertOk();
        $response->assertJsonPath('success', true);
        $response->assertJsonCount(1, 'data');

        $this->assertSame(6, (int) DB::table('comisiones')->where('idProfesor', 'PC02')->where('servicio', 'Canvia')->value('estado'));
        $this->assertSame(3, (int) DB::table('comisiones')->where('idProfesor', 'PC02')->where('servicio', 'No canvia')->value('estado'));
        $this->assertSame(4, (int) DB::table('comisiones')->where('idProfesor', 'PC99')->where('servicio', 'Altre professor')->value('estado'));
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
                $table->dateTime('deleted_at')->nullable();
                $table->timestamps();
            });
        }
    }

    private function insertProfesor(string $dni, string $nombre, string $apellido1, string $apellido2): void
    {
        DB::table('profesores')->insert([
            'dni' => $dni,
            'nombre' => $nombre,
            'apellido1' => $apellido1,
            'apellido2' => $apellido2,
            'email' => strtolower($dni) . '@test.local',
            'rol' => config('roles.rol.profesor'),
            'api_token' => bin2hex(random_bytes(20)),
            'fecha_baja' => null,
            'activo' => 1,
        ]);
    }
}
