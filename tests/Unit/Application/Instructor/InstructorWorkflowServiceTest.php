<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Instructor;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Intranet\Application\Instructor\InstructorWorkflowService;
use Intranet\Entities\Instructor;
use Tests\TestCase;

class InstructorWorkflowServiceTest extends TestCase
{
    use WithoutModelEvents;

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('database.default', 'sqlite');
        config()->set('database.connections.sqlite.database', ':memory:');
        DB::purge('sqlite');
        DB::reconnect('sqlite');

        $schema = Schema::connection('sqlite');

        $schema->create('instructores', function (Blueprint $table): void {
            $table->string('dni')->primary();
            $table->string('email')->nullable();
            $table->string('name')->nullable();
            $table->string('surnames')->nullable();
            $table->string('telefono')->nullable();
            $table->string('departamento')->nullable();
        });

        $schema->create('centros', function (Blueprint $table): void {
            $table->increments('id');
            $table->unsignedInteger('idEmpresa')->nullable();
            $table->string('nombre')->nullable();
            $table->string('direccion')->nullable();
            $table->string('localidad')->nullable();
            $table->timestamps();
        });

        $schema->create('centros_instructores', function (Blueprint $table): void {
            $table->unsignedInteger('idCentro');
            $table->string('idInstructor');
        });
    }

    public function test_upsert_and_attach_to_centro_assigna_dni_eu_i_enllaca(): void
    {
        DB::table('centros')->insert([
            'id' => 10,
            'idEmpresa' => 77,
            'nombre' => 'Centre prova',
            'direccion' => 'C/ Prova',
            'localidad' => 'Alcoi',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Instructor::query()->create([
            'dni' => 'EU0000009',
            'name' => 'Previ',
        ]);

        $request = Request::create('/instructor', 'POST', [
            'dni' => '',
            'name' => 'Nou',
            'email' => 'nou@example.test',
        ]);

        $created = false;
        $service = new InstructorWorkflowService();

        $empresaId = $service->upsertAndAttachToCentro(
            $request,
            10,
            function (Request $req) use (&$created): void {
                Instructor::query()->create([
                    'dni' => (string) $req->dni,
                    'name' => (string) $req->name,
                    'email' => (string) $req->email,
                ]);
                $created = true;
            }
        );

        $this->assertTrue($created);
        $this->assertSame('EU0000010', (string) $request->dni);
        $this->assertSame(77, $empresaId);

        $this->assertDatabaseHas('instructores', ['dni' => 'EU0000010']);
        $this->assertDatabaseHas('centros_instructores', [
            'idCentro' => 10,
            'idInstructor' => 'EU0000010',
        ]);
    }

    public function test_detach_from_centro_i_esborra_si_es_orfe(): void
    {
        DB::table('centros')->insert([
            'id' => 20,
            'idEmpresa' => 99,
            'nombre' => 'Centre 2',
            'direccion' => 'C/ 2',
            'localidad' => 'X',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Instructor::query()->create([
            'dni' => 'INS001',
            'name' => 'Instr',
        ]);

        DB::table('centros_instructores')->insert([
            'idCentro' => 20,
            'idInstructor' => 'INS001',
        ]);

        $deleted = [];
        $service = new InstructorWorkflowService();

        $empresaId = $service->detachFromCentroAndDeleteIfOrphan(
            'INS001',
            20,
            function (string $dni) use (&$deleted): void {
                $deleted[] = $dni;
                Instructor::query()->where('dni', $dni)->delete();
            }
        );

        $this->assertSame(99, $empresaId);
        $this->assertSame(['INS001'], $deleted);
        $this->assertDatabaseMissing('centros_instructores', [
            'idCentro' => 20,
            'idInstructor' => 'INS001',
        ]);
        $this->assertDatabaseMissing('instructores', ['dni' => 'INS001']);
    }

    public function test_ultima_fecha_retorn_null_o_la_mes_posterior(): void
    {
        $service = new InstructorWorkflowService();

        $senseData = [
            (object) ['hasta' => null],
            (object) ['hasta' => ''],
        ];
        $this->assertNull($service->ultimaFecha($senseData));

        $fcts = [
            (object) ['hasta' => '2026-01-10'],
            (object) ['hasta' => '2026-02-03'],
            (object) ['hasta' => '2026-01-25'],
        ];

        $ultima = $service->ultimaFecha($fcts);

        $this->assertNotNull($ultima);
        $this->assertSame('2026-02-03', $ultima->format('Y-m-d'));
    }
}
