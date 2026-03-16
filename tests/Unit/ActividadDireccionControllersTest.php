<?php

declare(strict_types=1);

namespace Tests\Unit;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Intranet\Entities\Profesor;
use Intranet\Http\Controllers\Direccion\Actividad\AuthorizeController;
use Intranet\Http\Controllers\Direccion\Actividad\PrintController;
use Intranet\Services\General\AutorizacionPrintService;
use Mockery;
use Tests\TestCase;

class ActividadDireccionControllersTest extends TestCase
{
    private string $sqlitePath;

    protected function setUp(): void
    {
        parent::setUp();

        config(['auth.defaults.guard' => 'profesor']);

        $this->sqlitePath = storage_path('actividad_direccion_controllers_testing.sqlite');
        if (file_exists($this->sqlitePath)) {
            @unlink($this->sqlitePath);
        }

        touch($this->sqlitePath);
        config(['database.default' => 'sqlite']);
        config(['database.connections.sqlite.database' => $this->sqlitePath]);
        config(['services.calendar.calendarCredentialsPath' => 'missing-google-calendar.json']);

        DB::setDefaultConnection('sqlite');
        DB::purge('sqlite');
        DB::reconnect('sqlite');

        $this->createSchema();
        $this->seedProfesor('DIR001', config('roles.rol.direccion'));
        $this->actingAs(Profesor::on('sqlite')->findOrFail('DIR001'), 'profesor');
    }

    protected function tearDown(): void
    {
        Mockery::close();

        Schema::connection('sqlite')->dropIfExists('actividad_profesor');
        Schema::connection('sqlite')->dropIfExists('actividades');
        Schema::connection('sqlite')->dropIfExists('profesores');

        if (file_exists($this->sqlitePath)) {
            @unlink($this->sqlitePath);
        }

        parent::tearDown();
    }

    public function test_authorize_controller_autoritza_totes_les_pendents(): void
    {
        $this->seedActividad(1, 'Activitat pendent 1');
        $this->seedActividad(1, 'Activitat pendent 2');
        $this->seedActividad(2, 'Activitat autoritzada');

        $controller = new AuthorizeController();
        $response = $controller();

        $this->assertSame(302, $response->getStatusCode());
        $this->assertSame(2, (int) DB::connection('sqlite')->table('actividades')->where('id', 1)->value('estado'));
        $this->assertSame(2, (int) DB::connection('sqlite')->table('actividades')->where('id', 2)->value('estado'));
        $this->assertSame(2, (int) DB::connection('sqlite')->table('actividades')->where('id', 3)->value('estado'));
    }

    public function test_print_controller_delega_en_autorizacion_print_service(): void
    {
        $expected = new Response('pdf', 200);

        $service = Mockery::mock(AutorizacionPrintService::class);
        $service->shouldReceive('imprimir')
            ->once()
            ->with('Intranet\\Entities\\Actividad', 'Actividad', 'extraescolars', null, null, 'portrait', true)
            ->andReturn($expected);

        $this->app->instance(AutorizacionPrintService::class, $service);

        $controller = new PrintController();

        $this->assertSame($expected, $controller());
    }

    private function createSchema(): void
    {
        Schema::connection('sqlite')->create('profesores', function (Blueprint $table): void {
            $table->string('dni', 10)->primary();
            $table->string('nombre')->nullable();
            $table->string('apellido1')->nullable();
            $table->string('apellido2')->nullable();
            $table->string('email')->nullable();
            $table->unsignedInteger('rol')->default(config('roles.rol.profesor'));
            $table->boolean('activo')->default(true);
            $table->date('fecha_baja')->nullable();
            $table->timestamps();
        });

        Schema::connection('sqlite')->create('actividades', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->text('descripcion')->nullable();
            $table->dateTime('desde')->nullable();
            $table->dateTime('hasta')->nullable();
            $table->boolean('extraescolar')->default(true);
            $table->tinyInteger('estado')->default(0);
            $table->timestamps();
        });

        Schema::connection('sqlite')->create('actividad_profesor', function (Blueprint $table): void {
            $table->unsignedInteger('idActividad');
            $table->string('idProfesor', 10);
            $table->boolean('coordinador')->default(false);
        });
    }

    private function seedProfesor(string $dni, int $rol): void
    {
        DB::connection('sqlite')->table('profesores')->insert([
            'dni' => $dni,
            'nombre' => 'Nom',
            'apellido1' => 'Prova',
            'apellido2' => 'Direccio',
            'email' => strtolower($dni) . '@test.local',
            'rol' => $rol,
            'activo' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function seedActividad(int $estado, string $name): void
    {
        DB::connection('sqlite')->table('actividades')->insert([
            'name' => $name,
            'descripcion' => 'Descripcio',
            'desde' => '2026-03-18 09:00:00',
            'hasta' => '2026-03-18 10:00:00',
            'extraescolar' => 1,
            'estado' => $estado,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
