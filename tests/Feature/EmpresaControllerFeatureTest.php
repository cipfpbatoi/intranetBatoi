<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Intranet\Entities\Profesor;
use Tests\TestCase;

/**
 * Proves de regressió del controlador d'empreses.
 */
class EmpresaControllerFeatureTest extends TestCase
{
    private string $sqlitePath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sqlitePath = storage_path('empresa_controller_feature_testing.sqlite');
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
        Schema::connection('sqlite')->dropIfExists('alumno_fcts');
        Schema::connection('sqlite')->dropIfExists('fcts');
        Schema::connection('sqlite')->dropIfExists('colaboraciones');
        Schema::connection('sqlite')->dropIfExists('centros');
        Schema::connection('sqlite')->dropIfExists('empresas');
        Schema::connection('sqlite')->dropIfExists('profesores');

        if (file_exists($this->sqlitePath)) {
            @unlink($this->sqlitePath);
        }

        parent::tearDown();
    }

    public function test_destroy_denega_usuari_que_no_es_cap_de_practiques(): void
    {
        $this->insertProfesor('EMP01', (int) config('roles.rol.tutor'));
        $empresaId = $this->insertEmpresa('Empresa Tutor');

        $usuario = Profesor::on('sqlite')->findOrFail('EMP01');
        $response = $this
            ->actingAs($usuario, 'profesor')
            ->get(route('empresa.destroy', ['empresa' => $empresaId]), ['referer' => '/home']);

        $response->assertStatus(403);
        $response->assertSessionHas('error', 'No estàs autoritzat.');
        $this->assertNotNull(DB::table('empresas')->where('id', $empresaId)->first());
    }

    public function test_destroy_bloca_empresa_amb_fct_vinculades_i_mostra_alerta(): void
    {
        $this->insertProfesor('EMP02', (int) config('roles.rol.jefe_practicas'));
        $empresaId = $this->insertEmpresa('Empresa Bloquejada');
        $centroId = $this->insertCentro($empresaId);
        $colaboracionId = $this->insertColaboracion($centroId);
        $fctId = $this->insertFct($colaboracionId);
        $this->insertAlumnoFct($fctId);

        $usuario = Profesor::on('sqlite')->findOrFail('EMP02');
        $response = $this
            ->actingAs($usuario, 'profesor')
            ->get(route('empresa.destroy', ['empresa' => $empresaId]), ['referer' => '/home']);

        $response->assertStatus(302);
        $response->assertRedirect(route('empresa.detalle', ['empresa' => $empresaId]));
        $response->assertSessionHas('styde/alerts.0.message', "No es pot esborrar l'empresa perquè té FCT i alumnat vinculats. Elimina abans les FCT relacionades.");
        $this->assertNotNull(DB::table('empresas')->where('id', $empresaId)->first());
    }

    public function test_destroy_esborra_empresa_si_no_te_fct_vinculades(): void
    {
        $this->insertProfesor('EMP03', (int) config('roles.rol.jefe_practicas'));
        $empresaId = $this->insertEmpresa('Empresa Lliure');

        $usuario = Profesor::on('sqlite')->findOrFail('EMP03');
        $response = $this
            ->actingAs($usuario, 'profesor')
            ->get(route('empresa.destroy', ['empresa' => $empresaId]), ['referer' => '/home']);

        $response->assertStatus(302);
        $this->assertNull(DB::table('empresas')->where('id', $empresaId)->first());
    }

    /**
     * Crea l'esquema mínim necessari per a provar l'esborrat d'empreses.
     *
     * @return void
     */
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
                $table->timestamps();
            });
        }

        if (!Schema::connection('sqlite')->hasTable('empresas')) {
            Schema::connection('sqlite')->create('empresas', function (Blueprint $table): void {
                $table->id();
                $table->string('cif')->nullable();
                $table->string('nombre')->nullable();
                $table->string('email')->nullable();
                $table->string('direccion')->nullable();
                $table->string('localidad')->nullable();
                $table->string('telefono')->nullable();
                $table->string('fichero')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::connection('sqlite')->hasTable('centros')) {
            Schema::connection('sqlite')->create('centros', function (Blueprint $table): void {
                $table->id();
                $table->unsignedBigInteger('idEmpresa');
                $table->string('nombre')->nullable();
                $table->string('direccion')->nullable();
                $table->string('localidad')->nullable();
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
                $table->unsignedBigInteger('idColaboracion');
                $table->string('idInstructor')->nullable();
                $table->date('desde')->nullable();
                $table->date('hasta')->nullable();
                $table->unsignedInteger('horas')->default(0);
                $table->unsignedTinyInteger('asociacion')->default(1);
                $table->unsignedTinyInteger('autorizacion')->default(0);
                $table->unsignedTinyInteger('erasmus')->default(0);
            });
        }

        if (!Schema::connection('sqlite')->hasTable('alumno_fcts')) {
            Schema::connection('sqlite')->create('alumno_fcts', function (Blueprint $table): void {
                $table->id();
                $table->unsignedBigInteger('idFct');
                $table->string('idAlumno')->nullable();
                $table->string('idProfesor', 10)->nullable();
                $table->unsignedInteger('horas')->default(0);
                $table->unsignedInteger('realizadas')->default(0);
                $table->unsignedTinyInteger('correoAlumno')->default(0);
                $table->float('calificacion')->nullable();
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

    /**
     * Inserix un professor de prova.
     *
     * @param string $dni
     * @param int $rol
     * @return void
     */
    private function insertProfesor(string $dni, int $rol): void
    {
        DB::table('profesores')->insert([
            'dni' => $dni,
            'nombre' => 'Test',
            'apellido1' => 'Empresa',
            'apellido2' => 'Controller',
            'email' => strtolower($dni) . '@test.local',
            'rol' => $rol,
            'fecha_baja' => null,
            'activo' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Inserix una empresa de prova.
     *
     * @param string $nombre
     * @return int
     */
    private function insertEmpresa(string $nombre): int
    {
        return (int) DB::table('empresas')->insertGetId([
            'cif' => 'B' . random_int(1000000, 9999999),
            'nombre' => $nombre,
            'email' => 'empresa@test.local',
            'direccion' => 'Carrer Prova 1',
            'localidad' => 'Alcoi',
            'telefono' => '600000000',
            'fichero' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Inserix un centre vinculat a una empresa.
     *
     * @param int $empresaId
     * @return int
     */
    private function insertCentro(int $empresaId): int
    {
        return (int) DB::table('centros')->insertGetId([
            'idEmpresa' => $empresaId,
            'nombre' => 'Centre Prova',
            'direccion' => 'Carrer Prova 2',
            'localidad' => 'Alcoi',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Inserix una col·laboració vinculada a un centre.
     *
     * @param int $centroId
     * @return int
     */
    private function insertColaboracion(int $centroId): int
    {
        return (int) DB::table('colaboraciones')->insertGetId([
            'idCentro' => $centroId,
            'idCiclo' => 1,
            'contacto' => 'Persona Prova',
            'telefono' => '600000001',
            'email' => 'colab@test.local',
            'puestos' => 1,
            'tutor' => 'EMP02',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Inserix una FCT vinculada a una col·laboració.
     *
     * @param int $colaboracionId
     * @return int
     */
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

    /**
     * Inserix una assignació d'alumnat a una FCT.
     *
     * @param int $fctId
     * @return void
     */
    private function insertAlumnoFct(int $fctId): void
    {
        DB::table('alumno_fcts')->insert([
            'idFct' => $fctId,
            'idAlumno' => '00000001A',
            'idProfesor' => 'EMP02',
            'horas' => 400,
            'realizadas' => 0,
            'correoAlumno' => 0,
            'calificacion' => null,
        ]);
    }
}
