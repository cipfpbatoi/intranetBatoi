<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

/** Comprova la separació segura del NIF llegat del gerent. */
class EmpresaGerentNifMigrationTest extends TestCase
{
    private string $sqlitePath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sqlitePath = tempnam(sys_get_temp_dir(), 'empresa-gerent-') ?: ':memory:';
        config(['database.default' => 'sqlite']);
        config(['database.connections.sqlite.database' => $this->sqlitePath]);
        DB::setDefaultConnection('sqlite');
        DB::purge('sqlite');
        DB::reconnect('sqlite');

        Schema::create('empresas', function (Blueprint $table): void {
            $table->id();
            $table->string('gerente')->nullable();
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

    public function test_separa_dni_nie_i_nif_del_nom_del_gerent(): void
    {
        DB::table('empresas')->insert([
            ['gerente' => '12345678z Maria Pérez Garcia'],
            ['gerente' => 'X1234567L - Joan Soler'],
            ['gerente' => 'B12345678: Empresa Representant SL'],
        ]);

        $migracio = $this->migration();
        $migracio->up();

        $this->assertDatabaseHas('empresas', ['gerente' => 'Maria Pérez Garcia', 'nif_gerente' => '12345678Z']);
        $this->assertDatabaseHas('empresas', ['gerente' => 'Joan Soler', 'nif_gerente' => 'X1234567L']);
        $this->assertDatabaseHas('empresas', ['gerente' => 'Empresa Representant SL', 'nif_gerente' => 'B12345678']);
    }

    public function test_conserva_noms_i_valors_ambigus_sense_identificador_reconegut(): void
    {
        DB::table('empresas')->insert([
            ['gerente' => 'Maria Pérez Garcia'],
            ['gerente' => 'REFERENCIA123 Maria Pérez'],
        ]);

        $this->migration()->up();

        $this->assertDatabaseHas('empresas', ['gerente' => 'Maria Pérez Garcia', 'nif_gerente' => null]);
        $this->assertDatabaseHas('empresas', ['gerente' => 'REFERENCIA123 Maria Pérez', 'nif_gerente' => null]);
    }

    public function test_reversio_recompon_el_valor_sense_perdre_nif_ni_nom(): void
    {
        DB::table('empresas')->insert(['gerente' => '12345678Z Maria Pérez']);

        $migracio = $this->migration();
        $migracio->up();
        $migracio->down();

        $this->assertFalse(Schema::hasColumn('empresas', 'nif_gerente'));
        $this->assertDatabaseHas('empresas', ['gerente' => '12345678Z Maria Pérez']);
    }

    /**
     * Carrega una instància nova de la migració per a cada prova.
     */
    private function migration(): object
    {
        return require database_path('migrations/2026_09_23_120000_add_nif_gerente_to_empresas_table.php');
    }
}
