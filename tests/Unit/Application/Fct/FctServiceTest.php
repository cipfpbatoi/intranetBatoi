<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Fct;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Schema;
use Intranet\Application\Fct\FctService;
use Intranet\Infrastructure\Persistence\Eloquent\Fct\EloquentFctRepository;
use Intranet\Application\Seguimiento\SeguimientoService as ApplicationSeguimientoService;
use Tests\TestCase;

/**
 * Proves del servei d'aplicació de FCT.
 */
class FctServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('database.default', 'sqlite');
        config()->set('database.connections.sqlite.database', ':memory:');
        DB::purge('sqlite');
        DB::reconnect('sqlite');
        Event::fake();

        $this->createSchema();
        $this->seedBaseData();
    }

    public function test_create_from_request_associa_instructor_orfe_al_centre_de_la_fct(): void
    {
        DB::table('instructores')->insert([
            'dni' => 'INS001',
            'name' => 'Instructor',
            'surnames' => 'Orfe',
        ]);

        $service = $this->fcts();
        $request = Request::create('/fct', 'POST', [
            'idColaboracion' => 10,
            'idInstructor' => 'INS001',
            'asociacion' => 1,
        ]);

        $fct = $service->createFromRequest($request);

        $this->assertSame('INS001', (string) $fct->idInstructor);
        $this->assertDatabaseHas('centros_instructores', [
            'idCentro' => 50,
            'idInstructor' => 'INS001',
        ]);
    }

    public function test_create_from_request_no_associa_instructor_amb_un_altre_centre(): void
    {
        DB::table('instructores')->insert([
            'dni' => 'INS002',
            'name' => 'Instructor',
            'surnames' => 'Amb centre',
        ]);
        DB::table('centros_instructores')->insert([
            'idCentro' => 60,
            'idInstructor' => 'INS002',
        ]);

        $service = $this->fcts();
        $request = Request::create('/fct', 'POST', [
            'idColaboracion' => 10,
            'idInstructor' => 'INS002',
            'asociacion' => 1,
        ]);

        $service->createFromRequest($request);

        $this->assertDatabaseMissing('centros_instructores', [
            'idCentro' => 50,
            'idInstructor' => 'INS002',
        ]);
        $this->assertDatabaseHas('centros_instructores', [
            'idCentro' => 60,
            'idInstructor' => 'INS002',
        ]);
    }

    public function test_set_instructor_associa_instructor_orfe_al_centre_de_la_fct(): void
    {
        DB::table('instructores')->insert([
            'dni' => 'INS003',
            'name' => 'Instructor',
            'surnames' => 'Nou',
        ]);
        DB::table('fcts')->insert([
            'id' => 100,
            'idColaboracion' => 10,
            'idInstructor' => null,
            'asociacion' => 1,
            'autorizacion' => 0,
            'erasmus' => 0,
            'correoInstructor' => 0,
        ]);

        $service = $this->fcts();

        $service->setInstructor(100, 'INS003');

        $this->assertDatabaseHas('centros_instructores', [
            'idCentro' => 50,
            'idInstructor' => 'INS003',
        ]);
    }

    public function test_assign_orphan_instructors_to_fct_centros_regularitza_dades_existents(): void
    {
        DB::table('instructores')->insert([
            [
                'dni' => 'INS004',
                'name' => 'Instructor',
                'surnames' => 'Orfe',
            ],
            [
                'dni' => 'INS005',
                'name' => 'Instructor',
                'surnames' => 'Ja assignat',
            ],
        ]);
        DB::table('centros_instructores')->insert([
            'idCentro' => 60,
            'idInstructor' => 'INS005',
        ]);
        DB::table('fcts')->insert([
            [
                'id' => 200,
                'idColaboracion' => 10,
                'idInstructor' => 'INS004',
                'asociacion' => 1,
                'autorizacion' => 0,
                'erasmus' => 0,
                'correoInstructor' => 0,
            ],
            [
                'id' => 201,
                'idColaboracion' => 10,
                'idInstructor' => 'INS005',
                'asociacion' => 1,
                'autorizacion' => 0,
                'erasmus' => 0,
                'correoInstructor' => 0,
            ],
        ]);

        $result = $this->fcts()->assignOrphanInstructorsToFctCentros();

        $this->assertSame(['instructors' => 1, 'assignments' => 1], $result);
        $this->assertDatabaseHas('centros_instructores', [
            'idCentro' => 50,
            'idInstructor' => 'INS004',
        ]);
        $this->assertDatabaseMissing('centros_instructores', [
            'idCentro' => 50,
            'idInstructor' => 'INS005',
        ]);
    }

    public function test_assign_orphan_instructors_to_fct_centros_dry_run_no_escriu(): void
    {
        DB::table('instructores')->insert([
            'dni' => 'INS006',
            'name' => 'Instructor',
            'surnames' => 'Dry run',
        ]);
        DB::table('fcts')->insert([
            'id' => 300,
            'idColaboracion' => 10,
            'idInstructor' => 'INS006',
            'asociacion' => 1,
            'autorizacion' => 0,
            'erasmus' => 0,
            'correoInstructor' => 0,
        ]);

        $result = $this->fcts()->assignOrphanInstructorsToFctCentros(true);

        $this->assertSame(['instructors' => 1, 'assignments' => 1], $result);
        $this->assertDatabaseMissing('centros_instructores', [
            'idCentro' => 50,
            'idInstructor' => 'INS006',
        ]);
    }

    private function fcts(): FctService
    {
        return new FctService(
            new EloquentFctRepository(),
            app(ApplicationSeguimientoService::class)
        );
    }

    private function createSchema(): void
    {
        $schema = Schema::connection('sqlite');

        $schema->create('fcts', function (Blueprint $table): void {
            $table->increments('id');
            $table->unsignedInteger('idColaboracion')->nullable();
            $table->string('idInstructor')->nullable();
            $table->unsignedTinyInteger('asociacion')->default(1);
            $table->unsignedTinyInteger('autorizacion')->default(0);
            $table->unsignedTinyInteger('erasmus')->default(0);
            $table->string('cotutor')->nullable();
            $table->unsignedTinyInteger('correoInstructor')->default(0);
        });

        $schema->create('empresas', function (Blueprint $table): void {
            $table->unsignedInteger('id')->primary();
            $table->string('nombre')->nullable();
        });

        $schema->create('centros', function (Blueprint $table): void {
            $table->unsignedInteger('id')->primary();
            $table->unsignedInteger('idEmpresa')->nullable();
            $table->string('nombre')->nullable();
            $table->string('direccion')->nullable();
            $table->string('localidad')->nullable();
        });

        $schema->create('colaboraciones', function (Blueprint $table): void {
            $table->unsignedInteger('id')->primary();
            $table->unsignedInteger('idCentro')->nullable();
            $table->unsignedInteger('idCiclo')->nullable();
            $table->string('tutor')->nullable();
        });

        $schema->create('instructores', function (Blueprint $table): void {
            $table->string('dni')->primary();
            $table->string('email')->nullable();
            $table->string('name')->nullable();
            $table->string('surnames')->nullable();
            $table->string('telefono')->nullable();
            $table->string('departamento')->nullable();
        });

        $schema->create('centros_instructores', function (Blueprint $table): void {
            $table->unsignedInteger('idCentro');
            $table->string('idInstructor');
            $table->primary(['idCentro', 'idInstructor']);
        });
    }

    private function seedBaseData(): void
    {
        DB::table('empresas')->insert([
            'id' => 100,
            'nombre' => 'Empresa Test',
        ]);
        DB::table('centros')->insert([
            [
                'id' => 50,
                'idEmpresa' => 100,
                'nombre' => 'Centre FCT',
                'direccion' => 'C/ FCT',
                'localidad' => 'Alcoi',
            ],
            [
                'id' => 60,
                'idEmpresa' => 100,
                'nombre' => 'Centre Previ',
                'direccion' => 'C/ Previ',
                'localidad' => 'Alcoi',
            ],
        ]);
        DB::table('colaboraciones')->insert([
            'id' => 10,
            'idCentro' => 50,
            'idCiclo' => 1,
            'tutor' => 'PROF01',
        ]);
    }
}
