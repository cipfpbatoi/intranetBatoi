<?php

declare(strict_types=1);

namespace Tests\Unit;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Intranet\Entities\Profesor;
use Intranet\Exceptions\NotFoundDomainException;
use Intranet\Http\Controllers\ComisionDireccionGestorController;
use Intranet\Http\Controllers\ComisionDireccionPaymentPrintController;
use Intranet\Http\Controllers\ComisionDireccionPrintController;
use Intranet\Services\General\AutorizacionPrintService;
use Mockery;
use Tests\TestCase;

class ComisionDireccionControllersTest extends TestCase
{
    private string $sqlitePath;

    protected function setUp(): void
    {
        parent::setUp();

        config(['auth.defaults.guard' => 'profesor']);

        $this->sqlitePath = storage_path('comision_direccion_controllers_testing.sqlite');
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
        $this->seedProfesor('P001', config('roles.rol.profesor'));
        $this->actingAs(Profesor::on('sqlite')->findOrFail('P001'), 'profesor');
    }

    protected function tearDown(): void
    {
        Mockery::close();

        Schema::connection('sqlite')->dropIfExists('comisiones');
        Schema::connection('sqlite')->dropIfExists('profesores');

        if (file_exists($this->sqlitePath)) {
            @unlink($this->sqlitePath);
        }

        parent::tearDown();
    }

    public function test_comision_direccion_print_controller_delega_en_servei(): void
    {
        $expected = new Response('pdf', 200);

        $printService = Mockery::mock(AutorizacionPrintService::class);
        $printService->shouldReceive('imprimir')
            ->once()
            ->with('Intranet\\Entities\\Comision', 'Comision', 'comisionsServei')
            ->andReturn($expected);

        $controller = new ComisionDireccionPrintController($printService);

        $this->assertSame($expected, $controller());
    }

    public function test_comision_direccion_payment_print_controller_delega_en_servei(): void
    {
        $expected = new Response('pdf', 200);

        $printService = Mockery::mock(AutorizacionPrintService::class);
        $printService->shouldReceive('imprimir')
            ->once()
            ->with('Intranet\\Entities\\Comision', 'Comision', 'payments', 6, 5, 'landscape', false)
            ->andReturn($expected);

        $controller = new ComisionDireccionPaymentPrintController($printService);

        $this->assertSame($expected, $controller());
    }

    public function test_comision_direccion_print_controller_torna_back_quan_no_hi_ha_pdf(): void
    {
        $printService = Mockery::mock(AutorizacionPrintService::class);
        $printService->shouldReceive('imprimir')
            ->once()
            ->andReturn(null);

        $controller = new ComisionDireccionPrintController($printService);

        $this->assertInstanceOf(RedirectResponse::class, $controller());
    }

    public function test_comision_direccion_gestor_redirigeix_al_document(): void
    {
        $comisionId = $this->seedComision('P001', 77);

        $controller = new ComisionDireccionGestorController();
        $response = $controller($comisionId);

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertSame(url('/documento/77/show'), $response->getTargetUrl());
    }

    public function test_comision_direccion_gestor_llanca_excepcio_si_no_hi_ha_document(): void
    {
        $comisionId = $this->seedComision('P001', null);

        $controller = new ComisionDireccionGestorController();

        $this->expectException(NotFoundDomainException::class);
        $controller($comisionId);
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

        Schema::connection('sqlite')->create('comisiones', function (Blueprint $table): void {
            $table->id();
            $table->string('idProfesor', 10)->nullable();
            $table->unsignedBigInteger('idDocumento')->nullable();
            $table->dateTime('desde')->nullable();
            $table->dateTime('hasta')->nullable();
            $table->unsignedTinyInteger('fct')->default(0);
            $table->text('servicio')->nullable();
            $table->tinyInteger('estado')->default(0);
            $table->timestamps();
        });
    }

    private function seedProfesor(string $dni, int $rol): void
    {
        DB::connection('sqlite')->table('profesores')->insert([
            'dni' => $dni,
            'nombre' => 'Nom',
            'apellido1' => 'Prova',
            'apellido2' => 'Controller',
            'email' => strtolower($dni) . '@test.local',
            'rol' => $rol,
            'activo' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function seedComision(string $dni, ?int $idDocumento): int
    {
        return (int) DB::connection('sqlite')->table('comisiones')->insertGetId([
            'idProfesor' => $dni,
            'idDocumento' => $idDocumento,
            'desde' => '2026-03-10 09:00:00',
            'hasta' => '2026-03-10 10:00:00',
            'fct' => 0,
            'servicio' => 'Visita',
            'estado' => 2,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
