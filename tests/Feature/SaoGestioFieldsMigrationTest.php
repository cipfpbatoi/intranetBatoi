<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

/** Comprova les columnes de compatibilitat amb SAO Gestió. */
class SaoGestioFieldsMigrationTest extends TestCase
{
    private string $sqlitePath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sqlitePath = tempnam(sys_get_temp_dir(), 'sao-gestio-') ?: ':memory:';
        config(['database.default' => 'sqlite']);
        config(['database.connections.sqlite.database' => $this->sqlitePath]);
        DB::setDefaultConnection('sqlite');
        DB::purge('sqlite');
        DB::reconnect('sqlite');

        Schema::create('empresas', function (Blueprint $table): void {
            $table->id();
            $table->boolean('sao')->default(true);
        });
        Schema::create('centros', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('idSao')->nullable();
        });
    }

    protected function tearDown(): void
    {
        DB::disconnect('sqlite');
        if ($this->sqlitePath !== ':memory:' && file_exists($this->sqlitePath)) {
            @unlink($this->sqlitePath);
        }

        parent::tearDown();
    }

    public function test_afig_el_valor_per_defecte_i_les_coordenades_opcionals(): void
    {
        $this->migration()->up();

        DB::table('empresas')->insert(['sao' => true]);
        DB::table('centros')->insert(['idSao' => null, 'latitud' => 38.6988, 'longitud' => -0.4815]);

        $this->assertDatabaseHas('empresas', ['dependent_gva' => 0]);
        $this->assertDatabaseHas('centros', ['latitud' => 38.6988, 'longitud' => -0.4815]);
    }

    public function test_reverteix_les_columnes_de_sao_gestio(): void
    {
        $migracio = $this->migration();
        $migracio->up();
        $migracio->down();

        $this->assertFalse(Schema::hasColumn('empresas', 'dependent_gva'));
        $this->assertFalse(Schema::hasColumn('centros', 'latitud'));
        $this->assertFalse(Schema::hasColumn('centros', 'longitud'));
    }

    /**
     * Carrega una instància nova de la migració per a cada prova.
     */
    private function migration(): object
    {
        return require database_path('migrations/2026_09_24_120000_add_sao_gestio_fields_to_empresas_and_centros.php');
    }
}
