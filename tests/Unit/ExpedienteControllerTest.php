<?php

declare(strict_types=1);

namespace Tests\Unit;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Intranet\Http\Controllers\ExpedienteController;
use Illuminate\View\View;
use Tests\TestCase;

class ExpedienteControllerTest extends TestCase
{
    private string $sqlitePath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sqlitePath = storage_path('expediente_controller_testing.sqlite');
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
        Schema::connection('sqlite')->dropIfExists('expedientes');

        if (file_exists($this->sqlitePath)) {
            @unlink($this->sqlitePath);
        }

        parent::tearDown();
    }

    public function test_show_retorna_vista_expediente_show(): void
    {
        $id = $this->insertExpediente(1);

        $controller = new DummyExpedienteController();
        $response = $controller->show($id);

        $this->assertInstanceOf(View::class, $response);
        $this->assertSame('expediente.show', $response->name());
        $data = $response->getData();
        $this->assertSame('Expediente', $data['modelo']);
        $this->assertSame($id, $data['elemento']->id);
    }

    public function test_autorizar_actualitza_estat_1_a_2_i_torna_back(): void
    {
        $idPendiente = $this->insertExpediente(1);
        $idNoPendiente = $this->insertExpediente(3);

        $this->bindRequestWithReferer('/direccion/expediente');

        $controller = new DummyExpedienteController();
        $response = $controller->autorizar();

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertSame(2, (int) DB::table('expedientes')->where('id', $idPendiente)->value('estado'));
        $this->assertSame(3, (int) DB::table('expedientes')->where('id', $idNoPendiente)->value('estado'));
    }

    private function createSchema(): void
    {
        if (!Schema::connection('sqlite')->hasTable('expedientes')) {
            Schema::connection('sqlite')->create('expedientes', function (Blueprint $table): void {
                $table->id();
                $table->unsignedInteger('tipo')->nullable();
                $table->string('idModulo')->nullable();
                $table->string('idAlumno')->nullable();
                $table->string('idProfesor')->nullable();
                $table->text('explicacion')->nullable();
                $table->date('fecha')->nullable();
                $table->date('fechatramite')->nullable();
                $table->tinyInteger('estado')->default(0);
            });
        }
    }

    private function insertExpediente(int $estado): int
    {
        return (int) DB::table('expedientes')->insertGetId([
            'tipo' => 1,
            'idAlumno' => 'A001',
            'idProfesor' => 'P001',
            'fecha' => '2026-02-12',
            'estado' => $estado,
        ]);
    }

    private function bindRequestWithReferer(string $referer): void
    {
        $request = Request::create('/dummy', 'GET', [], [], [], ['HTTP_REFERER' => $referer]);
        $this->app->instance('request', $request);
    }
}

class DummyExpedienteController extends ExpedienteController
{
    /**
     * Constructor buit per evitar la inicialització de panell en proves unitàries.
     */
    public function __construct()
    {
        // Intencionalment buit.
    }

    /**
     * Omet l'autorització en tests unitaris de comportament intern del controlador.
     *
     * @param mixed $ability
     * @param mixed $arguments
     * @return true
     */
    public function authorize($ability, $arguments = [])
    {
        return true;
    }
}
