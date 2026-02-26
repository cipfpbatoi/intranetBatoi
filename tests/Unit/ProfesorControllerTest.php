<?php

declare(strict_types=1);

namespace Tests\Unit;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Intranet\Http\Controllers\ProfesorController;
use Tests\TestCase;

class ProfesorControllerTest extends TestCase
{
    private string $sqlitePath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sqlitePath = storage_path('profesor_controller_testing.sqlite');
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
        Schema::connection('sqlite')->dropIfExists('profesores');

        if (file_exists($this->sqlitePath)) {
            @unlink($this->sqlitePath);
        }

        parent::tearDown();
    }

    public function test_change_torna_back_si_professor_no_existix(): void
    {
        $this->bindRequestWithReferer('/admin/profesor');
        $controller = new DummyProfesorController();

        $response = $this->callProtectedMethod($controller, 'change', ['NO_EXISTIX']);

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertSame(url('/admin/profesor'), $response->getTargetUrl());
        $this->assertFalse(session()->has('userChange'));
    }

    public function test_back_change_torna_home_i_neteja_sessio_si_no_hi_ha_usuari_original(): void
    {
        session()->put('userChange', 'NO_EXISTIX');
        $controller = new DummyProfesorController();

        $response = $this->callProtectedMethod($controller, 'backChange');

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertSame(url('/home'), $response->getTargetUrl());
        $this->assertFalse(session()->has('userChange'));
    }

    private function createSchema(): void
    {
        if (!Schema::connection('sqlite')->hasTable('profesores')) {
            Schema::connection('sqlite')->create('profesores', function (Blueprint $table): void {
                $table->string('dni', 10)->primary();
                $table->unsignedInteger('rol')->default(3);
                $table->boolean('activo')->default(true);
                $table->date('fecha_baja')->nullable();
                $table->string('sustituye_a', 10)->nullable();
                $table->timestamps();
            });
        }
    }

    private function bindRequestWithReferer(string $referer): void
    {
        $request = Request::create('/dummy', 'GET', [], [], [], ['HTTP_REFERER' => $referer]);
        $this->app->instance('request', $request);
    }
}

class DummyProfesorController extends ProfesorController
{
    public function __construct()
    {
        // Evita inicialitzar UI/panel en proves unitàries.
    }
}
