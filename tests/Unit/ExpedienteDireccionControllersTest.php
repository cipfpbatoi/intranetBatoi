<?php

declare(strict_types=1);

namespace Tests\Unit;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Intranet\Application\Expediente\ExpedienteService;
use Intranet\Entities\Expediente;
use Intranet\Entities\Profesor;
use Intranet\Http\Controllers\Direccion\Expediente\AuthorizeController;
use Intranet\Http\Controllers\Direccion\Expediente\GestorController;
use Intranet\Http\Controllers\Direccion\Expediente\PrintController;
use Intranet\Services\School\ExpedienteWorkflowService;
use Mockery;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Tests\TestCase;

class ExpedienteDireccionControllersTest extends TestCase
{
    private string $sqlitePath;

    protected function setUp(): void
    {
        parent::setUp();

        config(['auth.defaults.guard' => 'profesor']);

        $this->sqlitePath = storage_path('expediente_direccion_controllers_testing.sqlite');
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
        $this->seedProfesor('DIR001', config('roles.rol.direccion'));
        $this->actingAs(Profesor::on('sqlite')->findOrFail('DIR001'), 'profesor');
    }

    protected function tearDown(): void
    {
        Mockery::close();

        Schema::connection('sqlite')->dropIfExists('profesores');

        if (file_exists($this->sqlitePath)) {
            @unlink($this->sqlitePath);
        }

        parent::tearDown();
    }

    public function test_authorize_controller_autoritza_els_pendents(): void
    {
        $workflow = Mockery::mock(ExpedienteWorkflowService::class);
        $workflow->shouldReceive('authorizePending')->once();
        $this->app->instance(ExpedienteWorkflowService::class, $workflow);

        $controller = new AuthorizeController();
        $response = $controller();

        $this->assertInstanceOf(RedirectResponse::class, $response);
    }

    public function test_print_controller_torna_arrere_si_no_hi_ha_expedients(): void
    {
        $service = Mockery::mock(ExpedienteService::class);
        $service->shouldReceive('readyToPrint')->once()->andReturn(new EloquentCollection());

        $controller = new PrintController();
        $response = $controller($service);

        $this->assertInstanceOf(RedirectResponse::class, $response);
    }

    public function test_gestor_controller_redirigeix_al_document(): void
    {
        $expediente = new Expediente();
        $expediente->id = 1;
        $expediente->idDocumento = 77;

        $service = Mockery::mock(ExpedienteService::class);
        $service->shouldReceive('findOrFail')->once()->with(1)->andReturn($expediente);

        $controller = new GestorController();
        $response = $controller(1, $service);

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertStringEndsWith('/documento/77/show', $response->getTargetUrl());
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
    }

    private function seedProfesor(string $dni, int $rol): void
    {
        DB::connection('sqlite')->table('profesores')->insert([
            'dni' => $dni,
            'nombre' => 'Nom',
            'apellido1' => 'Direccio',
            'apellido2' => 'Test',
            'email' => strtolower($dni) . '@test.local',
            'rol' => $rol,
            'activo' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
