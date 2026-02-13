<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Intranet\Entities\Profesor;
use Tests\TestCase;

class FaltaControllerFeatureTest extends TestCase
{
    private string $sqlitePath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sqlitePath = storage_path('falta_controller_feature_testing.sqlite');
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
        Schema::connection('sqlite')->dropIfExists('faltas');
        Schema::connection('sqlite')->dropIfExists('profesores');

        if (file_exists($this->sqlitePath)) {
            @unlink($this->sqlitePath);
        }

        parent::tearDown();
    }

    public function test_ruta_alta_redirigeix_a_login_si_no_autenticat(): void
    {
        $faltaId = $this->insertFalta('PF01');

        $response = $this->get(route('falta.alta', ['falta' => $faltaId]));

        $response->assertStatus(302);
        $location = (string) $response->headers->get('Location');
        $this->assertStringContainsString('/login', $location);
    }

    public function test_ruta_alta_denega_rol_no_permes(): void
    {
        $this->insertProfesor('PF02', config('roles.rol.alumno'));
        $faltaId = $this->insertFalta('PF02');

        $usuario = Profesor::on('sqlite')->findOrFail('PF02');
        $response = $this
            ->actingAs($usuario, 'profesor')
            ->get(route('falta.alta', ['falta' => $faltaId]), ['referer' => '/home']);

        $response->assertStatus(403);
        $response->assertSessionHas('error', 'No estàs autoritzat.');
    }

    public function test_ruta_alta_permet_direccio_i_actualitza_estat(): void
    {
        $this->insertProfesor('PF03', config('roles.rol.direccion'));
        $faltaId = $this->insertFalta('PF03');

        $usuario = Profesor::on('sqlite')->findOrFail('PF03');
        $response = $this
            ->actingAs($usuario, 'profesor')
            ->get(route('falta.alta', ['falta' => $faltaId]), ['referer' => '/home']);

        $response->assertStatus(302);
        $this->assertSame(3, (int) DB::table('faltas')->where('id', $faltaId)->value('estado'));
        $this->assertSame(0, (int) DB::table('faltas')->where('id', $faltaId)->value('baja'));
        $this->assertNull(DB::table('profesores')->where('dni', 'PF03')->value('fecha_baja'));
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
                $table->boolean('activo')->default(true);
                $table->date('fecha_baja')->nullable();
                $table->string('sustituye_a', 10)->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::connection('sqlite')->hasTable('faltas')) {
            Schema::connection('sqlite')->create('faltas', function (Blueprint $table): void {
                $table->id();
                $table->string('idProfesor', 10);
                $table->boolean('baja')->default(false);
                $table->boolean('dia_completo')->default(true);
                $table->date('desde')->nullable();
                $table->date('hasta')->nullable();
                $table->time('hora_ini')->nullable();
                $table->time('hora_fin')->nullable();
                $table->unsignedInteger('motivos')->nullable();
                $table->string('observaciones', 200)->nullable();
                $table->string('fichero')->nullable();
                $table->tinyInteger('estado')->default(0);
                $table->timestamps();
            });
        }

        if (!Schema::connection('sqlite')->hasTable('activities')) {
            Schema::connection('sqlite')->create('activities', function (Blueprint $table): void {
                $table->increments('id');
                $table->string('action')->nullable();
                $table->text('comentari')->nullable();
                $table->string('document')->nullable();
                $table->string('model_class')->nullable();
                $table->unsignedBigInteger('model_id')->nullable();
                $table->string('author_id')->nullable();
                $table->timestamps();
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
            'activo' => 1,
            'fecha_baja' => '2026-02-10',
            'sustituye_a' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function insertFalta(string $dniProfesor): int
    {
        return (int) DB::table('faltas')->insertGetId([
            'idProfesor' => $dniProfesor,
            'baja' => 1,
            'dia_completo' => 1,
            'desde' => '2026-02-10',
            'hasta' => '2026-02-20',
            'motivos' => 1,
            'estado' => 5,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}

