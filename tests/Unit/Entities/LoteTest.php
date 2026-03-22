<?php

declare(strict_types=1);

namespace Tests\Unit\Entities;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Intranet\Entities\Lote;
use Tests\TestCase;

class LoteTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('database.default', 'sqlite');
        config()->set('database.connections.sqlite.database', ':memory:');
        DB::purge('sqlite');
        DB::reconnect('sqlite');

        $schema = Schema::connection('sqlite');
        $schema->dropIfExists('materiales');
        $schema->dropIfExists('articulos_lote');
        $schema->dropIfExists('lotes');

        $schema->create('lotes', function (Blueprint $table): void {
            $table->string('registre')->primary();
            $table->unsignedTinyInteger('procedencia')->nullable();
            $table->string('proveedor')->nullable();
            $table->date('fechaAlta')->nullable();
            $table->string('factura')->nullable();
            $table->unsignedInteger('departamento_id')->nullable();
        });

        $schema->create('articulos_lote', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('lote_id')->nullable();
            $table->unsignedInteger('articulo_id')->nullable();
            $table->unsignedSmallInteger('unidades')->default(1);
        });

        $schema->create('materiales', function (Blueprint $table): void {
            $table->increments('id');
            $table->unsignedInteger('articulo_lote_id')->nullable();
            $table->string('espacio', 10)->nullable();
        });
    }

    public function test_estado_accessor_en_fallback_retornat_tots_els_estats(): void
    {
        DB::table('lotes')->insert([
            ['registre' => 'L-000'],
            ['registre' => 'L-100'],
            ['registre' => 'L-200'],
            ['registre' => 'L-300'],
        ]);

        DB::table('articulos_lote')->insert([
            ['id' => 10, 'lote_id' => 'L-100', 'articulo_id' => 1, 'unidades' => 2],
            ['id' => 20, 'lote_id' => 'L-200', 'articulo_id' => 2, 'unidades' => 1],
            ['id' => 30, 'lote_id' => 'L-300', 'articulo_id' => 3, 'unidades' => 1],
        ]);

        DB::table('materiales')->insert([
            ['articulo_lote_id' => 20, 'espacio' => 'INVENT'],
            ['articulo_lote_id' => 30, 'espacio' => 'AULA1'],
        ]);

        $lotes = Lote::query()->orderBy('registre')->get()->keyBy('registre');

        $this->assertSame(0, $lotes->get('L-000')->estado);
        $this->assertSame(1, $lotes->get('L-100')->estado);
        $this->assertSame(2, $lotes->get('L-200')->estado);
        $this->assertSame(3, $lotes->get('L-300')->estado);
    }
}
