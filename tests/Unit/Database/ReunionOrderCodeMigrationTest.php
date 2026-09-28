<?php

declare(strict_types=1);

namespace Tests\Unit\Database;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

/**
 * Proves de la migració segura dels codis dels punts de reunió.
 */
class ReunionOrderCodeMigrationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('database.default', 'sqlite');
        config()->set('database.connections.sqlite.database', ':memory:');
        DB::purge('sqlite');
        DB::reconnect('sqlite');

        Schema::create('ordenes_reuniones', function (Blueprint $table): void {
            $table->increments('id');
            $table->unsignedInteger('idReunion');
            $table->string('descripcion', 120)->nullable();
            $table->text('resumen')->nullable();
        });
    }

    public function test_inferix_nomes_descripcions_exactes_sense_modificar_contingut(): void
    {
        DB::table('ordenes_reuniones')->insert([
            [
                'idReunion' => 1,
                'descripcion' => 'Acords adoptats',
                'resumen' => '<p>Acord vigent</p>',
            ],
            [
                'idReunion' => 1,
                'descripcion' => 'Acords de la reunió',
                'resumen' => 'Text manual',
            ],
            [
                'idReunion' => 1,
                'descripcion' => 'acords adoptats',
                'resumen' => 'Text amb caixa diferent',
            ],
        ]);

        $migration = require database_path(
            'migrations/2026_09_25_130000_add_codigo_to_ordenes_reuniones_table.php'
        );
        $migration->up();

        $this->assertDatabaseHas('ordenes_reuniones', [
            'descripcion' => 'Acords adoptats',
            'resumen' => '<p>Acord vigent</p>',
            'codigo' => 'agreements',
        ]);
        $this->assertDatabaseHas('ordenes_reuniones', [
            'descripcion' => 'Acords de la reunió',
            'resumen' => 'Text manual',
            'codigo' => null,
        ]);
        $this->assertDatabaseHas('ordenes_reuniones', [
            'descripcion' => 'acords adoptats',
            'resumen' => 'Text amb caixa diferent',
            'codigo' => null,
        ]);

        $migration->down();
        $this->assertFalse(Schema::hasColumn('ordenes_reuniones', 'codigo'));
    }
}
