<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Intranet\Entities\Profesor;
use Tests\TestCase;

class CentroControllerFeatureTest extends TestCase
{
    private string $sqlitePath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sqlitePath = storage_path('centro_controller_feature_testing.sqlite');
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
        Schema::connection('sqlite')->dropIfExists('fcts');
        Schema::connection('sqlite')->dropIfExists('colaboraciones');
        Schema::connection('sqlite')->dropIfExists('centros');
        Schema::connection('sqlite')->dropIfExists('profesores');

        if (file_exists($this->sqlitePath)) {
            @unlink($this->sqlitePath);
        }

        parent::tearDown();
    }

    public function test_destroy_com_admin_esborra_centre_i_redirigeix_a_empresa(): void
    {
        $this->insertProfesor('AD01', config('roles.rol.administrador'));
        $centroId = $this->insertCentro(99);

        $usuario = Profesor::on('sqlite')->findOrFail('AD01');
        $response = $this
            ->actingAs($usuario, 'profesor')
            ->get(route('centro.destroy', ['centro' => $centroId]));

        $response->assertStatus(302);
        $this->assertNull(DB::table('centros')->where('id', $centroId)->first());
    }

    public function test_destroy_no_esborra_centre_amb_fct_vinculada(): void
    {
        $this->insertProfesor('AD01', config('roles.rol.administrador'));
        $centroId = $this->insertCentro(99);
        $colaboracionId = $this->insertColaboracion($centroId);
        $this->insertFct($colaboracionId);

        $usuario = Profesor::on('sqlite')->findOrFail('AD01');
        $response = $this
            ->actingAs($usuario, 'profesor')
            ->get(route('centro.destroy', ['centro' => $centroId]));

        $response->assertStatus(302);
        $response->assertRedirect(route('empresa.detalle', ['empresa' => 99]));
        $this->assertNotNull(DB::table('centros')->where('id', $centroId)->first());
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

        if (!Schema::connection('sqlite')->hasTable('centros')) {
            Schema::connection('sqlite')->create('centros', function (Blueprint $table): void {
                $table->id();
                $table->unsignedBigInteger('idEmpresa');
                $table->string('nombre')->nullable();
                $table->string('direccion')->nullable();
                $table->string('localidad')->nullable();
                $table->string('horarios')->nullable();
                $table->text('observaciones')->nullable();
                $table->string('idioma')->nullable();
                $table->string('codiPostal')->nullable();
                $table->unsignedBigInteger('idSao')->nullable();
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

        if (!Schema::connection('sqlite')->hasTable('colaboraciones')) {
            Schema::connection('sqlite')->create('colaboraciones', function (Blueprint $table): void {
                $table->id();
                $table->unsignedBigInteger('idCentro');
                $table->unsignedBigInteger('idCiclo')->nullable();
                $table->string('contacto')->nullable();
                $table->string('telefono')->nullable();
                $table->string('email')->nullable();
                $table->unsignedInteger('puestos')->default(1);
                $table->string('tutor')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::connection('sqlite')->hasTable('fcts')) {
            Schema::connection('sqlite')->create('fcts', function (Blueprint $table): void {
                $table->id();
                $table->unsignedBigInteger('idColaboracion')->nullable();
                $table->string('idInstructor')->nullable();
                $table->date('desde')->nullable();
                $table->date('hasta')->nullable();
                $table->unsignedInteger('horas')->nullable();
                $table->unsignedTinyInteger('asociacion')->default(1);
                $table->unsignedTinyInteger('autorizacion')->default(0);
                $table->unsignedTinyInteger('erasmus')->default(0);
            });
        }
    }

    private function insertProfesor(string $dni, int $rol): void
    {
        DB::table('profesores')->insert([
            'dni' => $dni,
            'nombre' => 'Admin',
            'apellido1' => 'Test',
            'apellido2' => 'User',
            'email' => strtolower($dni) . '@test.local',
            'rol' => $rol,
            'fecha_baja' => null,
            'activo' => 1,
        ]);
    }

    private function insertCentro(int $empresaId): int
    {
        return (int) DB::table('centros')->insertGetId([
            'idEmpresa' => $empresaId,
            'nombre' => 'Centre Prova',
            'direccion' => 'Carrer Prova 1',
            'localidad' => 'Alcoi',
            'horarios' => null,
            'observaciones' => null,
            'idioma' => null,
            'codiPostal' => null,
            'idSao' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function insertColaboracion(int $centroId): int
    {
        return (int) DB::table('colaboraciones')->insertGetId([
            'idCentro' => $centroId,
            'idCiclo' => 1,
            'contacto' => 'Contacte',
            'telefono' => '965000000',
            'email' => 'contacte@test.local',
            'puestos' => 1,
            'tutor' => 'AD01',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function insertFct(int $colaboracionId): int
    {
        return (int) DB::table('fcts')->insertGetId([
            'idColaboracion' => $colaboracionId,
            'idInstructor' => null,
            'desde' => now()->toDateString(),
            'hasta' => now()->addMonth()->toDateString(),
            'horas' => 400,
            'asociacion' => 1,
            'autorizacion' => 0,
            'erasmus' => 0,
        ]);
    }
}
