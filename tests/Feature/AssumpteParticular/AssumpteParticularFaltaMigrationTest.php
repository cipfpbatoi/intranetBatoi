<?php

declare(strict_types=1);

namespace Tests\Feature\AssumpteParticular;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Intranet\Entities\AssumpteParticular;
use Tests\TestCase;

/** Comprova la vinculació retroactiva de resolucions al circuit de faltes. */
class AssumpteParticularFaltaMigrationTest extends TestCase
{
    private string $sqlitePath;

    protected function setUp(): void
    {
        parent::setUp();
        $this->sqlitePath = tempnam(sys_get_temp_dir(), 'falta-assumptes-') ?: ':memory:';
        config(['database.default' => 'sqlite']);
        config(['database.connections.sqlite.database' => $this->sqlitePath]);
        DB::setDefaultConnection('sqlite');
        DB::purge('sqlite');
        DB::reconnect('sqlite');

        Schema::create('faltas', function (Blueprint $table): void {
            $table->id();
            $table->string('fichero')->nullable();
        });
        Schema::create('assumptes_particulars', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('falta_id');
            $table->string('estat');
            $table->string('origen')->default(AssumpteParticular::ORIGEN_SOLLICITUD);
            $table->string('resolucio_document')->nullable();
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

    public function test_vincula_nomes_resolucions_autoritzades_sense_sobreescriure_justificants(): void
    {
        DB::table('faltas')->insert([
            ['id' => 1, 'fichero' => null],
            ['id' => 2, 'fichero' => 'justificant.pdf'],
            ['id' => 3, 'fichero' => null],
        ]);
        DB::table('assumptes_particulars')->insert([
            ['falta_id' => 1, 'estat' => 'autoritzada', 'resolucio_document' => 'resolucio.pdf'],
            ['falta_id' => 2, 'estat' => 'autoritzada', 'resolucio_document' => 'altra.pdf'],
            ['falta_id' => 3, 'estat' => 'denegada', 'resolucio_document' => 'no.pdf'],
        ]);

        $migracio = require database_path('migrations/2026_09_16_110000_link_assumpte_particular_pdf_to_falta.php');
        $migracio->up();

        $this->assertSame('resolucio.pdf', DB::table('faltas')->where('id', 1)->value('fichero'));
        $this->assertSame('justificant.pdf', DB::table('faltas')->where('id', 2)->value('fichero'));
        $this->assertNull(DB::table('faltas')->where('id', 3)->value('fichero'));
    }
}
