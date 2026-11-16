<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Schema;
use Intranet\Entities\Profesor;
use Tests\TestCase;

/**
 * Tests de regressió de la captura de materials en lots.
 */
class LoteCaptureFeatureTest extends TestCase
{
    private string $sqlitePath;

    protected function setUp(): void
    {
        parent::setUp();

        Event::fake();
        $this->sqlitePath = storage_path('lote_capture_feature_testing.sqlite');
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
        DB::table('profesores')->insert([
            'dni' => 'PDIRCAP01',
            'rol' => config('roles.rol.direccion'),
        ]);
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

    public function test_capture_normalitza_unitats_absents_a_una(): void
    {
        DB::table('lotes')->insert(['registre' => 'LOT-CAP-1']);
        DB::table('materiales')->insert([
            'id' => 100,
            'descripcion' => 'Pantalla portàtil',
            'marca' => 'Marca',
            'modelo' => 'Model',
            'unidades' => null,
            'inventariable' => 0,
            'espacio' => 'ALMACEN',
        ]);

        $this->actingAs(Profesor::on('sqlite')->findOrFail('PDIRCAP01'))
            ->withoutMiddleware()
            ->post('/direccion/lote/LOT-CAP-1/capture', [100 => 'Pantalla portàtil']);

        $articuloLote = DB::table('articulos_lote')->first();

        $this->assertNotNull($articuloLote);
        $this->assertSame(1, (int) $articuloLote->unidades);
        $this->assertNull(DB::table('materiales')->where('id', 100)->first());
        $this->assertSame(
            1,
            DB::table('materiales')
                ->where('articulo_lote_id', $articuloLote->id)
                ->where('inventariable', 1)
                ->count()
        );
    }

    public function test_capture_rebutja_files_invalides_sense_canvis_parcials(): void
    {
        DB::table('lotes')->insert(['registre' => 'LOT-CAP-2']);
        DB::table('materiales')->insert([
            [
                'id' => 201,
                'descripcion' => 'Material vàlid',
                'unidades' => 2,
                'inventariable' => 0,
                'espacio' => 'ALMACEN',
            ],
            [
                'id' => 202,
                'descripcion' => 'Material invàlid',
                'unidades' => 0,
                'inventariable' => 0,
                'espacio' => 'ALMACEN',
            ],
        ]);

        $response = $this->actingAs(Profesor::on('sqlite')->findOrFail('PDIRCAP01'))
            ->withoutMiddleware()
            ->post('/direccion/lote/LOT-CAP-2/capture', [
                201 => 'Material vàlid',
                202 => 'Material invàlid',
            ]);

        $response->assertSessionHasErrors('unidades');
        $this->assertSame(0, DB::table('articulos')->count());
        $this->assertSame(0, DB::table('articulos_lote')->count());
        $this->assertSame(2, DB::table('materiales')->count());

        $this->assertStringContainsString(
            '<x-ui.errors />',
            (string) file_get_contents(resource_path('views/lote/inventario.blade.php'))
        );

        $this->withViewErrors([
            'unidades' => 'Les unitats del material «Material invàlid» han de ser un enter positiu.',
        ])
            ->blade('<x-ui.errors />')
            ->assertSee('Les unitats del material «Material invàlid» han de ser un enter positiu.');
    }

    private function createSchema(): void
    {
        Schema::connection('sqlite')->create('profesores', function (Blueprint $table): void {
            $table->string('dni', 10)->primary();
            $table->unsignedInteger('rol');
        });

        Schema::connection('sqlite')->create('lotes', function (Blueprint $table): void {
            $table->string('registre', 12)->primary();
        });

        Schema::connection('sqlite')->create('articulos', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('descripcion', 200)->nullable();
        });

        Schema::connection('sqlite')->create('articulos_lote', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('lote_id', 12);
            $table->unsignedInteger('articulo_id');
            $table->string('marca', 50)->nullable();
            $table->string('modelo', 50)->nullable();
            $table->smallInteger('unidades');
        });

        Schema::connection('sqlite')->create('materiales', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('descripcion', 255);
            $table->string('marca', 50)->nullable();
            $table->string('modelo', 50)->nullable();
            $table->smallInteger('unidades')->nullable();
            $table->unsignedTinyInteger('inventariable')->default(0);
            $table->string('espacio', 10);
            $table->date('fechaultimoinventario')->nullable();
            $table->unsignedInteger('articulo_lote_id')->nullable();
        });
    }
}
