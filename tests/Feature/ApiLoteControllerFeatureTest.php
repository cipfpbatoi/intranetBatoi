<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Intranet\Entities\Profesor;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ApiLoteControllerFeatureTest extends TestCase
{
    private string $sqlitePath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sqlitePath = storage_path('api_lote_controller_feature_testing.sqlite');
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
        Schema::connection('sqlite')->dropIfExists('materiales');
        Schema::connection('sqlite')->dropIfExists('articulos_lote');
        Schema::connection('sqlite')->dropIfExists('articulos');
        Schema::connection('sqlite')->dropIfExists('lotes');
        Schema::connection('sqlite')->dropIfExists('profesores');

        if (file_exists($this->sqlitePath)) {
            @unlink($this->sqlitePath);
        }

        parent::tearDown();
    }

    public function test_update_retorna_403_sense_rol_de_direccio_o_admin(): void
    {
        DB::table('lotes')->insert([
            'registre' => 'LOT-200',
            'procedencia' => 2,
            'proveedor' => 'Proveidor Test',
        ]);
        $this->insertProfesor('PPROF01', config('roles.rol.profesor'));
        Sanctum::actingAs(Profesor::on('sqlite')->findOrFail('PPROF01'));

        $this->withoutMiddleware()
            ->putJson('/api/lote/LOT-200', ['proveedor' => 'Proveidor Nou'])
            ->assertStatus(403);

        $this->assertSame(
            'Proveidor Test',
            DB::table('lotes')->where('registre', 'LOT-200')->value('proveedor')
        );
    }

    public function test_update_permet_a_direccio(): void
    {
        DB::table('lotes')->insert([
            'registre' => 'LOT-201',
            'procedencia' => 2,
            'proveedor' => 'Proveidor Test',
        ]);
        $this->insertProfesor('PDIR01', config('roles.rol.direccion'));
        Sanctum::actingAs(Profesor::on('sqlite')->findOrFail('PDIR01'));

        $this->withoutMiddleware()
            ->putJson('/api/lote/LOT-201', ['proveedor' => 'Proveidor Nou'])
            ->assertOk();

        $this->assertSame(
            'Proveidor Nou',
            DB::table('lotes')->where('registre', 'LOT-201')->value('proveedor')
        );
    }

    public function test_destroy_retorna_403_sense_rol_de_direccio_o_admin(): void
    {
        DB::table('lotes')->insert([
            'registre' => 'LOT-202',
            'procedencia' => 2,
            'proveedor' => 'Proveidor Test',
        ]);
        $this->insertProfesor('PPROF02', config('roles.rol.profesor'));
        Sanctum::actingAs(Profesor::on('sqlite')->findOrFail('PPROF02'));

        $this->withoutMiddleware()
            ->deleteJson('/api/lote/LOT-202')
            ->assertStatus(403);

        $this->assertNotNull(DB::table('lotes')->where('registre', 'LOT-202')->first());
    }

    public function test_destroy_permet_a_direccio(): void
    {
        DB::table('lotes')->insert([
            'registre' => 'LOT-203',
            'procedencia' => 2,
            'proveedor' => 'Proveidor Test',
        ]);
        $this->insertProfesor('PDIR02', config('roles.rol.direccion'));
        Sanctum::actingAs(Profesor::on('sqlite')->findOrFail('PDIR02'));

        $this->withoutMiddleware()
            ->deleteJson('/api/lote/LOT-203')
            ->assertOk();

        $this->assertNull(DB::table('lotes')->where('registre', 'LOT-203')->first());
    }

    public function test_get_articulos_retorna_404_quan_lot_no_existeix(): void
    {
        $response = $this
            ->withoutMiddleware()
            ->getJson('/api/lote/NO-EXIST/articulos');

        $response->assertStatus(404);
        $response->assertJsonPath('success', false);
        $response->assertJsonPath('message', 'Not found: Lote #NO-EXIST');
    }

    public function test_put_articulos_retorna_404_quan_lot_no_existeix(): void
    {
        $response = $this
            ->withoutMiddleware()
            ->putJson('/api/lote/NO-EXIST/articulos', ['inventariar' => true]);

        $response->assertStatus(404);
        $response->assertJsonPath('success', false);
        $response->assertJsonPath('message', 'Not found: Lote #NO-EXIST');
    }

    public function test_put_articulos_es_idempotent_i_no_duplica_materials(): void
    {
        DB::table('lotes')->insert([
            'registre' => 'LOT-100',
            'procedencia' => 2,
            'proveedor' => 'Proveidor Test',
        ]);

        DB::table('articulos')->insert([
            'id' => 1,
            'descripcion' => 'Portatil',
        ]);

        DB::table('articulos_lote')->insert([
            'id' => 1,
            'lote_id' => 'LOT-100',
            'articulo_id' => 1,
            'marca' => 'Lenovo',
            'modelo' => 'T14',
            'unidades' => 2,
        ]);

        $this->withoutMiddleware()->putJson('/api/lote/LOT-100/articulos', ['inventariar' => true])
            ->assertOk();

        $this->withoutMiddleware()->putJson('/api/lote/LOT-100/articulos', ['inventariar' => true])
            ->assertOk();

        $this->assertSame(
            2,
            DB::table('materiales')
                ->where('articulo_lote_id', 1)
                ->where('espacio', 'INVENT')
                ->count()
        );
    }

    private function createSchema(): void
    {
        if (!Schema::connection('sqlite')->hasTable('lotes')) {
            Schema::connection('sqlite')->create('lotes', function (Blueprint $table): void {
                $table->string('registre', 12)->primary();
                $table->unsignedTinyInteger('procedencia')->nullable();
                $table->string('proveedor', 90)->nullable();
                $table->date('fechaAlta')->nullable();
                $table->string('factura', 15)->nullable();
                $table->unsignedTinyInteger('departamento_id')->nullable();
            });
        }

        if (!Schema::connection('sqlite')->hasTable('articulos')) {
            Schema::connection('sqlite')->create('articulos', function (Blueprint $table): void {
                $table->increments('id');
                $table->string('descripcion', 200)->nullable();
            });
        }

        if (!Schema::connection('sqlite')->hasTable('articulos_lote')) {
            Schema::connection('sqlite')->create('articulos_lote', function (Blueprint $table): void {
                $table->increments('id');
                $table->string('lote_id', 12)->nullable();
                $table->unsignedInteger('articulo_id');
                $table->string('marca', 50)->nullable();
                $table->string('modelo', 50)->nullable();
                $table->smallInteger('unidades')->default(1);
            });
        }

        if (!Schema::connection('sqlite')->hasTable('materiales')) {
            Schema::connection('sqlite')->create('materiales', function (Blueprint $table): void {
                $table->increments('id');
                $table->string('descripcion', 255);
                $table->string('marca', 50)->nullable();
                $table->string('modelo', 50)->nullable();
                $table->unsignedTinyInteger('procedencia')->nullable();
                $table->unsignedTinyInteger('estado')->default(1);
                $table->smallInteger('unidades')->default(1);
                $table->string('proveedor', 90)->nullable();
                $table->unsignedTinyInteger('inventariable')->default(1);
                $table->string('espacio', 10);
                $table->unsignedInteger('articulo_lote_id')->nullable();
            });
        }

        if (!Schema::connection('sqlite')->hasTable('profesores')) {
            Schema::connection('sqlite')->create('profesores', function (Blueprint $table): void {
                $table->string('dni', 10)->primary();
                $table->unsignedInteger('rol')->default(config('roles.rol.profesor'));
                $table->string('api_token', 80)->nullable();
            });
        }
    }

    private function insertProfesor(string $dni, int $rol): void
    {
        DB::table('profesores')->insert([
            'dni' => $dni,
            'rol' => $rol,
            'api_token' => bin2hex(random_bytes(20)),
        ]);
    }
}
