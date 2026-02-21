<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Intranet\Entities\Profesor;
use Tests\TestCase;

class ApiReservaControllerFeatureTest extends TestCase
{
    private string $sqlitePath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sqlitePath = storage_path('api_reserva_controller_feature_testing.sqlite');
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
        Schema::connection('sqlite')->dropIfExists('reservas');
        Schema::connection('sqlite')->dropIfExists('profesores');

        if (file_exists($this->sqlitePath)) {
            @unlink($this->sqlitePath);
        }

        parent::tearDown();
    }

    public function test_show_legacy_filtra_i_afig_nom_profe(): void
    {
        $this->insertProfesor('PRS01', 'Laura', 'Garcia', 'Perez');
        $user = Profesor::on('sqlite')->findOrFail('PRS01');
        $this->actingAs($user, 'api');

        DB::table('reservas')->insert([
            [
                'id' => 1,
                'idProfesor' => 'PRS01',
                'dia' => '2026-03-01',
                'hora' => 2,
                'idEspacio' => 10,
                'observaciones' => null,
            ],
            [
                'id' => 2,
                'idProfesor' => 'ALT01',
                'dia' => '2026-03-01',
                'hora' => 2,
                'idEspacio' => 11,
                'observaciones' => null,
            ],
        ]);

        $response = $this->getJson('/api/reserva/idProfesor=PRS01');

        $response->assertOk();
        $response->assertJsonPath('success', true);
        $response->assertJsonCount(1, 'data');
        $response->assertJsonPath('data.0.idProfesor', 'PRS01');
        $response->assertJsonPath('data.0.nomProfe', 'Laura Garcia');
    }

    public function test_index_modern_filtra_per_query_i_afig_nom_profe(): void
    {
        $this->insertProfesor('PRS11', 'Anna', 'Marti', 'Soler');
        $user = Profesor::on('sqlite')->findOrFail('PRS11');
        $this->actingAs($user, 'api');

        DB::table('reservas')->insert([
            [
                'id' => 11,
                'idProfesor' => 'PRS11',
                'dia' => '2026-03-02',
                'hora' => 1,
                'idEspacio' => 20,
                'observaciones' => null,
            ],
            [
                'id' => 12,
                'idProfesor' => 'ALT11',
                'dia' => '2026-03-02',
                'hora' => 1,
                'idEspacio' => 20,
                'observaciones' => null,
            ],
        ]);

        $response = $this->getJson('/api/reserva?idProfesor=PRS11');

        $response->assertOk();
        $response->assertHeaderMissing('Deprecation');
        $response->assertJsonPath('success', true);
        $response->assertJsonCount(1, 'data');
        $response->assertJsonPath('data.0.idProfesor', 'PRS11');
        $response->assertJsonPath('data.0.nomProfe', 'Anna Marti');
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

        if (!Schema::connection('sqlite')->hasTable('reservas')) {
            Schema::connection('sqlite')->create('reservas', function (Blueprint $table): void {
                $table->increments('id');
                $table->string('idProfesor', 10)->nullable();
                $table->date('dia')->nullable();
                $table->unsignedInteger('hora')->nullable();
                $table->unsignedInteger('idEspacio')->nullable();
                $table->text('observaciones')->nullable();
            });
        }
    }

    private function insertProfesor(string $dni, string $nombre, string $apellido1, string $apellido2): void
    {
        DB::table('profesores')->insert([
            'dni' => $dni,
            'codigo' => random_int(1000, 9999),
            'nombre' => $nombre,
            'apellido1' => $apellido1,
            'apellido2' => $apellido2,
            'email' => strtolower($dni) . '@test.local',
            'rol' => config('roles.rol.profesor'),
            'api_token' => bin2hex(random_bytes(20)),
            'fecha_baja' => null,
            'activo' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
