<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Intranet\Entities\Profesor;
use Tests\TestCase;

class ExpedienteControllerFeatureTest extends TestCase
{
    private string $sqlitePath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sqlitePath = storage_path('expediente_controller_feature_testing.sqlite');
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
        Schema::connection('sqlite')->dropIfExists('expedientes');
        Schema::connection('sqlite')->dropIfExists('profesores');

        if (file_exists($this->sqlitePath)) {
            @unlink($this->sqlitePath);
        }

        parent::tearDown();
    }

    public function test_ruta_autorizar_redirigeix_a_login_si_no_autenticat(): void
    {
        $response = $this->get(route('expediente.autorizar'));

        $response->assertStatus(302);
        $location = (string) $response->headers->get('Location');
        $this->assertStringContainsString('/login', $location);
    }

    public function test_ruta_autorizar_denega_rol_no_permes(): void
    {
        $this->insertProfesor('PE01', config('roles.rol.profesor'));

        $usuario = Profesor::on('sqlite')->findOrFail('PE01');
        $response = $this
            ->actingAs($usuario, 'profesor')
            ->get(route('expediente.autorizar'), ['referer' => '/home']);

        $response->assertStatus(403);
        $response->assertSessionHas('error', 'No estàs autoritzat.');
    }

    public function test_ruta_autorizar_permet_direccio_i_actualitza_pendents(): void
    {
        $this->insertProfesor('PE02', config('roles.rol.direccion'));
        $idPendiente = $this->insertExpediente(1);
        $idAltre = $this->insertExpediente(4);

        $usuario = Profesor::on('sqlite')->findOrFail('PE02');
        $response = $this
            ->actingAs($usuario, 'profesor')
            ->get(route('expediente.autorizar'), ['referer' => '/direccion/expediente']);

        $response->assertStatus(302);
        $this->assertSame(2, (int) DB::table('expedientes')->where('id', $idPendiente)->value('estado'));
        $this->assertSame(4, (int) DB::table('expedientes')->where('id', $idAltre)->value('estado'));
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
                $table->date('fecha_baja')->nullable();
                $table->boolean('activo')->default(true);
            });
        }

        if (!Schema::connection('sqlite')->hasTable('expedientes')) {
            Schema::connection('sqlite')->create('expedientes', function (Blueprint $table): void {
                $table->id();
                $table->unsignedInteger('tipo')->nullable();
                $table->string('idModulo')->nullable();
                $table->string('idAlumno')->nullable();
                $table->string('idProfesor')->nullable();
                $table->text('explicacion')->nullable();
                $table->date('fecha')->nullable();
                $table->date('fechatramite')->nullable();
                $table->tinyInteger('estado')->default(0);
            });
        }
    }

    private function insertProfesor(string $dni, int $rol): void
    {
        DB::table('profesores')->insert([
            'dni' => $dni,
            'nombre' => 'Test',
            'apellido1' => 'User',
            'apellido2' => 'Feature',
            'email' => strtolower($dni) . '@test.local',
            'rol' => $rol,
            'fecha_baja' => null,
            'activo' => 1,
        ]);
    }

    private function insertExpediente(int $estado): int
    {
        return (int) DB::table('expedientes')->insertGetId([
            'tipo' => 1,
            'idAlumno' => 'A001',
            'idProfesor' => 'PE02',
            'fecha' => '2026-02-12',
            'estado' => $estado,
        ]);
    }
}

