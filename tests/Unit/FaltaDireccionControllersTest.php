<?php

declare(strict_types=1);

namespace Tests\Unit;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;
use Intranet\Entities\Profesor;
use Intranet\Exceptions\NotFoundDomainException;
use Intranet\Http\Controllers\Direccion\Falta\DocumentController;
use Intranet\Http\Controllers\Direccion\Falta\ShowController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Tests\TestCase;

class FaltaDireccionControllersTest extends TestCase
{
    private string $sqlitePath;
    private string $documentPath;

    protected function setUp(): void
    {
        parent::setUp();

        config(['auth.defaults.guard' => 'profesor']);

        $this->sqlitePath = storage_path('falta_direccion_controllers_testing.sqlite');
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
        $this->seedProfesor('PF001', config('roles.rol.profesor'));
        $this->actingAs(Profesor::on('sqlite')->findOrFail('DIR001'), 'profesor');

        $directory = storage_path('app/faltas');
        if (!is_dir($directory)) {
            mkdir($directory, 0777, true);
        }

        $this->documentPath = $directory . '/controller-test.pdf';
        file_put_contents($this->documentPath, 'pdf');
    }

    protected function tearDown(): void
    {
        Schema::connection('sqlite')->dropIfExists('faltas');
        Schema::connection('sqlite')->dropIfExists('profesores');

        if (file_exists($this->documentPath)) {
            @unlink($this->documentPath);
        }

        if (file_exists($this->sqlitePath)) {
            @unlink($this->sqlitePath);
        }

        parent::tearDown();
    }

    public function test_show_controller_mostra_la_vista_de_detall(): void
    {
        $faltaId = $this->seedFalta('PF001', 'faltas/controller-test.pdf');

        $controller = new ShowController();
        $response = $controller($faltaId);

        $this->assertInstanceOf(View::class, $response);
        $this->assertSame('intranet.show', $response->getName());
        $this->assertSame('Falta', $response->getData()['modelo']);
    }

    public function test_document_controller_retorna_el_fitxer_adjunto(): void
    {
        $faltaId = $this->seedFalta('PF001', 'faltas/controller-test.pdf');

        $controller = new DocumentController();
        $response = $controller($faltaId);

        $this->assertInstanceOf(BinaryFileResponse::class, $response);
        $this->assertSame($this->documentPath, $response->getFile()->getPathname());
    }

    public function test_document_controller_llanca_excepcio_si_no_hi_ha_fitxer(): void
    {
        $faltaId = $this->seedFalta('PF001', null);

        $controller = new DocumentController();

        $this->expectException(NotFoundDomainException::class);
        $controller($faltaId);
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

    private function seedFalta(string $dni, ?string $fichero): int
    {
        return (int) DB::connection('sqlite')->table('faltas')->insertGetId([
            'idProfesor' => $dni,
            'baja' => 0,
            'dia_completo' => 1,
            'desde' => '2026-03-15',
            'hasta' => '2026-03-15',
            'motivos' => 1,
            'observaciones' => 'Test',
            'fichero' => $fichero,
            'estado' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
