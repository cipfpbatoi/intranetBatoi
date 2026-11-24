<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

/** Comprova la persistència separada de la clau d'importació d'ITACA. */
class ItacaFeImportKeyMigrationTest extends TestCase
{
    private string $sqlitePath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sqlitePath = tempnam(sys_get_temp_dir(), 'itaca-fe-') ?: ':memory:';
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => $this->sqlitePath]);
        DB::setDefaultConnection('sqlite');
        DB::purge('sqlite');
        DB::reconnect('sqlite');
        Schema::create('alumno_fcts', function (Blueprint $table): void {
            $table->id();
            $table->string('idSao')->nullable();
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

    public function test_guarda_una_clau_d_itaca_sense_reutilitzar_id_sao(): void
    {
        $this->migration()->up();
        DB::table('alumno_fcts')->insert([
            'idSao' => null,
            'itacaImportKey' => 'itaca-fe:prova',
        ]);

        $this->assertDatabaseHas('alumno_fcts', [
            'idSao' => null,
            'itacaImportKey' => 'itaca-fe:prova',
        ]);
    }

    private function migration(): object
    {
        return require database_path('migrations/2026_11_18_120000_add_itaca_import_key_to_alumno_fcts_table.php');
    }
}
