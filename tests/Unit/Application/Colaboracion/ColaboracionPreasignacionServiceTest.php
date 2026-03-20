<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Colaboracion;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Intranet\Application\Colaboracion\ColaboracionPreasignacionService;
use Intranet\Entities\ColaboracionPreasignacion;
use RuntimeException;
use Tests\TestCase;

class ColaboracionPreasignacionServiceTest extends TestCase
{
    private string $sqlitePath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sqlitePath = storage_path('colaboracion_preasignacion_service_testing.sqlite');
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
        $this->seedBaseData();
    }

    protected function tearDown(): void
    {
        Schema::connection('sqlite')->dropIfExists('colaboracion_preasignaciones');
        Schema::connection('sqlite')->dropIfExists('alumnos_grupos');
        Schema::connection('sqlite')->dropIfExists('grupos');
        Schema::connection('sqlite')->dropIfExists('colaboraciones');
        Schema::connection('sqlite')->dropIfExists('alumnos');
        Schema::connection('sqlite')->dropIfExists('profesores');

        if (file_exists($this->sqlitePath)) {
            @unlink($this->sqlitePath);
        }

        parent::tearDown();
    }

    /**
     * Crea una proposta vàlida i la deixa vinculada a alumne, professor i col·laboració.
     */
    public function test_create_crea_una_preasignacio_valida(): void
    {
        $service = new ColaboracionPreasignacionService();

        $preasignacion = $service->create(1, 'ALU001', 'PROF001', 'proposta', 'Primera proposta');

        $this->assertInstanceOf(ColaboracionPreasignacion::class, $preasignacion);
        $this->assertSame('ALU001', $preasignacion->idAlumno);
        $this->assertSame('PROF001', $preasignacion->idProfesor);
        $this->assertSame('proposta', $preasignacion->estado);
        $this->assertSame(1, DB::connection('sqlite')->table('colaboracion_preasignaciones')->count());
    }

    /**
     * Evita repetir una reserva activa del mateix alumne en la mateixa col·laboració.
     */
    public function test_create_rebutja_duplicat_en_la_mateixa_colaboracio(): void
    {
        $service = new ColaboracionPreasignacionService();

        $service->create(1, 'ALU001', 'PROF001');

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('L\'alumne ja està preassignat en esta col·laboració.');

        $service->create(1, 'ALU001', 'PROF001');
    }

    /**
     * Evita ocupar més places de les que oferix la col·laboració.
     */
    public function test_create_rebutja_quan_no_queden_places_lliures(): void
    {
        $service = new ColaboracionPreasignacionService();

        $service->create(1, 'ALU001', 'PROF001');

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('La col·laboració ja no té places lliures per a noves preassignacions.');

        $service->create(1, 'ALU002', 'PROF001');
    }

    /**
     * Evita reservar simultàniament el mateix alumne en un altre centre del mateix cicle.
     */
    public function test_create_rebutja_conflicte_del_mateix_alumne_en_el_mateix_cicle(): void
    {
        $service = new ColaboracionPreasignacionService();

        $service->create(1, 'ALU001', 'PROF001');

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('L\'alumne ja té una altra preassignació activa en este cicle.');

        $service->create(2, 'ALU001', 'PROF001');
    }

    /**
     * Eliminar una reserva allibera la plaça i permet tornar a crear-la.
     */
    public function test_descartar_elimina_la_reserva_i_permet_tornar_a_crear(): void
    {
        $service = new ColaboracionPreasignacionService();

        $preasignacion = $service->create(1, 'ALU001', 'PROF001');
        $service->descartar($preasignacion->id);

        $nova = $service->create(1, 'ALU001', 'PROF001', 'proposta', 'Nova proposta');

        $this->assertSame('proposta', $nova->estado);
        $this->assertSame(1, DB::connection('sqlite')->table('colaboracion_preasignaciones')->count());
    }

    /**
     * Hidrata el panell amb reserves existents i opcions d'alumnat del cicle.
     */
    public function test_hydrate_for_panel_adjunta_preasignaciones_i_opcions_per_cicle(): void
    {
        $service = new ColaboracionPreasignacionService();

        $service->create(1, 'ALU001', 'PROF001', 'proposta', 'Primera proposta');

        $panel = $service->hydrateForPanel(collect([
            \Intranet\Entities\Colaboracion::query()->findOrFail(1),
        ]));

        /** @var \Intranet\Entities\Colaboracion $colaboracion */
        $colaboracion = $panel->first();

        $this->assertCount(1, $colaboracion->preasignacionesPanel);
        $this->assertSame('ALU001', $colaboracion->preasignacionesPanel->first()->idAlumno);
        $this->assertSame(
            ['ALU002'],
            $colaboracion->preasignacionAlumnoOptions->keys()->all()
        );
    }

    /**
     * Crea l'esquema mínim per a provar el servici sobre sqlite.
     */
    private function createSchema(): void
    {
        Schema::connection('sqlite')->create('profesores', function (Blueprint $table): void {
            $table->string('dni')->primary();
            $table->string('nombre')->nullable();
            $table->string('apellido1')->nullable();
            $table->string('apellido2')->nullable();
            $table->timestamps();
        });

        Schema::connection('sqlite')->create('alumnos', function (Blueprint $table): void {
            $table->string('nia')->primary();
            $table->string('nombre')->nullable();
            $table->string('apellido1')->nullable();
            $table->string('apellido2')->nullable();
            $table->timestamps();
        });

        Schema::connection('sqlite')->create('grupos', function (Blueprint $table): void {
            $table->string('codigo')->primary();
            $table->unsignedInteger('idCiclo');
            $table->timestamps();
        });

        Schema::connection('sqlite')->create('alumnos_grupos', function (Blueprint $table): void {
            $table->string('idAlumno');
            $table->string('idGrupo');
        });

        Schema::connection('sqlite')->create('colaboraciones', function (Blueprint $table): void {
            $table->increments('id');
            $table->unsignedInteger('idCentro')->nullable();
            $table->unsignedInteger('idCiclo');
            $table->string('contacto')->nullable();
            $table->string('telefono')->nullable();
            $table->string('email')->nullable();
            $table->unsignedInteger('puestos')->default(1);
            $table->string('tutor')->nullable();
            $table->unsignedInteger('estado')->default(1);
            $table->timestamps();
        });

        Schema::connection('sqlite')->create('colaboracion_preasignaciones', function (Blueprint $table): void {
            $table->increments('id');
            $table->unsignedInteger('idColaboracion');
            $table->string('idAlumno');
            $table->string('idProfesor');
            $table->string('estado', 20)->default('proposta');
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Carrega dades mínimes per a les proves de regles de preassignació.
     */
    private function seedBaseData(): void
    {
        DB::connection('sqlite')->table('profesores')->insert([
            'dni' => 'PROF001',
            'nombre' => 'Tutor',
            'apellido1' => 'Prova',
            'apellido2' => 'Un',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::connection('sqlite')->table('alumnos')->insert([
            [
                'nia' => 'ALU001',
                'nombre' => 'Alumne',
                'apellido1' => 'Primer',
                'apellido2' => 'Prova',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nia' => 'ALU002',
                'nombre' => 'Alumne',
                'apellido1' => 'Segon',
                'apellido2' => 'Prova',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        DB::connection('sqlite')->table('grupos')->insert([
            'codigo' => 'GRP100',
            'idCiclo' => 100,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::connection('sqlite')->table('alumnos_grupos')->insert([
            ['idAlumno' => 'ALU001', 'idGrupo' => 'GRP100'],
            ['idAlumno' => 'ALU002', 'idGrupo' => 'GRP100'],
        ]);

        DB::connection('sqlite')->table('colaboraciones')->insert([
            [
                'id' => 1,
                'idCentro' => 10,
                'idCiclo' => 100,
                'puestos' => 1,
                'tutor' => 'PROF001',
                'estado' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'idCentro' => 11,
                'idCiclo' => 100,
                'puestos' => 2,
                'tutor' => 'PROF001',
                'estado' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
