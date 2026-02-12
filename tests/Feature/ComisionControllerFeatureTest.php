<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Intranet\Entities\Profesor;
use Tests\TestCase;

class ComisionControllerFeatureTest extends TestCase
{
    private string $sqlitePath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sqlitePath = storage_path('comision_controller_feature_testing.sqlite');
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
        Schema::connection('sqlite')->dropIfExists('comisiones');
        Schema::connection('sqlite')->dropIfExists('profesores');

        if (file_exists($this->sqlitePath)) {
            @unlink($this->sqlitePath);
        }

        parent::tearDown();
    }

    public function test_ruta_unpaid_redirigeix_a_login_si_no_autenticat(): void
    {
        $comisionId = $this->insertComision(3);

        $response = $this->get(route('comision.unpaid', ['comision' => $comisionId]));

        $response->assertStatus(302);
        $location = (string) $response->headers->get('Location');
        $this->assertStringContainsString('/login', $location);
    }

    public function test_ruta_unpaid_denega_rol_no_permes(): void
    {
        $comisionId = $this->insertComision(3);
        $this->insertProfesor('PC01', 'Alumne', config('roles.rol.alumno'));

        $usuario = Profesor::on('sqlite')->findOrFail('PC01');
        $response = $this
            ->actingAs($usuario, 'profesor')
            ->get(route('comision.unpaid', ['comision' => $comisionId]), ['referer' => '/home']);

        $response->assertStatus(403);
        $response->assertSessionHas('error', 'No estàs autoritzat.');
    }

    public function test_ruta_unpaid_permet_professor_i_actualitza_estat(): void
    {
        $comisionId = $this->insertComision(3);
        $this->insertProfesor('PC02', 'Profe', config('roles.rol.profesor'));

        $usuario = Profesor::on('sqlite')->findOrFail('PC02');
        $response = $this
            ->actingAs($usuario, 'profesor')
            ->get(route('comision.unpaid', ['comision' => $comisionId]), ['referer' => '/home']);

        $response->assertStatus(302);
        $this->assertSame(4, (int) DB::table('comisiones')->where('id', $comisionId)->value('estado'));
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

    private function insertProfesor(string $dni, string $nombre, int $rol): void
    {
        DB::table('profesores')->insert([
            'dni' => $dni,
            'nombre' => $nombre,
            'apellido1' => 'Test',
            'apellido2' => 'User',
            'email' => strtolower($dni) . '@test.local',
            'rol' => $rol,
            'fecha_baja' => null,
            'activo' => 1,
        ]);
    }

    private function insertComision(int $estado): int
    {
        return (int) DB::table('comisiones')->insertGetId([
            'idProfesor' => 'PC02',
            'desde' => '2026-02-12 09:00:00',
            'hasta' => '2026-02-12 12:00:00',
            'fct' => 0,
            'servicio' => 'Visita',
            'estado' => $estado,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
