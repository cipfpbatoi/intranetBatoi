<?php

declare(strict_types=1);

namespace Tests\Unit\Entities;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Intranet\Entities\Articulo;
use Tests\TestCase;

class ArticuloTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('database.default', 'sqlite');
        config()->set('database.connections.sqlite.database', ':memory:');
        DB::purge('sqlite');
        DB::reconnect('sqlite');

        $schema = Schema::connection('sqlite');
        $schema->dropIfExists('articulos_lote');
        $schema->dropIfExists('lotes');
        $schema->dropIfExists('articulos');

        $schema->create('articulos', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('descripcion')->nullable();
            $table->string('fichero')->nullable();
        });

        $schema->create('lotes', function (Blueprint $table): void {
            $table->string('registre')->primary();
            $table->string('proveedor')->nullable();
            $table->string('factura')->nullable();
            $table->unsignedTinyInteger('procedencia')->nullable();
            $table->date('fechaAlta')->nullable();
            $table->unsignedInteger('departamento_id')->nullable();
        });

        $schema->create('articulos_lote', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('lote_id')->nullable();
            $table->unsignedInteger('articulo_id');
            $table->string('marca')->nullable();
            $table->string('modelo')->nullable();
            $table->unsignedInteger('unidades')->default(1);
        });
    }

    public function test_lote_relation_recupera_lots_per_pivot(): void
    {
        DB::table('articulos')->insert([
            'id' => 10,
            'descripcion' => 'Portatil',
        ]);

        DB::table('lotes')->insert([
            ['registre' => 'L-001', 'proveedor' => 'A'],
            ['registre' => 'L-002', 'proveedor' => 'B'],
        ]);

        DB::table('articulos_lote')->insert([
            ['lote_id' => 'L-001', 'articulo_id' => 10, 'unidades' => 3],
            ['lote_id' => 'L-002', 'articulo_id' => 10, 'unidades' => 1],
        ]);

        $articulo = Articulo::findOrFail(10);
        $registres = $articulo->Lote()->pluck('registre')->sort()->values()->all();

        $this->assertSame(['L-001', 'L-002'], $registres);
    }

    public function test_fill_file_guarda_i_retorna_ruta(): void
    {
        Storage::fake('public');

        $articulo = new Articulo();
        $articulo->id = 25;
        $file = UploadedFile::fake()->image('foto.jpg');

        $path = $articulo->fillFile($file);

        $this->assertSame('Articulos/25.jpg', $path);
        Storage::disk('public')->assertExists('Articulos/25.jpg');
    }
}

