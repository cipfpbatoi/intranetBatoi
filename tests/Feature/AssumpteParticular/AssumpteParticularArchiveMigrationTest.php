<?php

declare(strict_types=1);

namespace Tests\Feature\AssumpteParticular;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Intranet\Entities\AssumpteParticular;
use Tests\TestCase;

/** Comprova que les resolucions existents entren en el gestor documental. */
class AssumpteParticularArchiveMigrationTest extends TestCase
{
    private string $sqlitePath;

    protected function setUp(): void
    {
        parent::setUp();
        $this->sqlitePath = tempnam(sys_get_temp_dir(), 'arxiu-assumptes-') ?: ':memory:';
        config(['database.default' => 'sqlite']);
        config(['database.connections.sqlite.database' => $this->sqlitePath]);
        DB::setDefaultConnection('sqlite');
        DB::purge('sqlite');
        DB::reconnect('sqlite');
        Storage::fake('local');

        Schema::create('profesores', function (Blueprint $table): void {
            $table->string('dni')->primary();
            $table->string('nombre');
            $table->string('apellido1')->nullable();
            $table->string('apellido2')->nullable();
        });
        Schema::create('documentos', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('tipoDocumento');
            $table->string('curso');
            $table->string('propietario')->nullable();
            $table->string('descripcion');
            $table->string('fichero')->nullable();
            $table->integer('rol')->default(1);
            $table->timestamps();
        });
        Schema::create('assumptes_particulars', function (Blueprint $table): void {
            $table->id();
            $table->string('idProfesor');
            $table->date('data_gaudi');
            $table->string('curs');
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

    public function test_arxiva_resolucions_existents_sense_duplicar_documents(): void
    {
        DB::table('profesores')->insert([
            'dni' => 'PROF001',
            'nombre' => 'Nom',
            'apellido1' => 'Cognom',
            'apellido2' => 'Segon',
        ]);
        DB::table('assumptes_particulars')->insert([
            'idProfesor' => 'PROF001',
            'data_gaudi' => '2026-10-15',
            'curs' => '2026-2027',
            'estat' => 'autoritzada',
            'resolucio_document' => 'assumptes-particulars/resolucions/existent.pdf',
        ]);
        Storage::disk('local')->put('assumptes-particulars/resolucions/existent.pdf', 'PDF');

        $migracio = require database_path('migrations/2026_09_16_100000_add_localitat_and_archive_owner.php');
        $migracio->up();

        $this->assertTrue(Schema::hasColumn('profesores', 'localitat'));
        $this->assertDatabaseCount('documentos', 1);
        $this->assertDatabaseHas('documentos', [
            'tipoDocumento' => 'AssumpteParticular',
            'propietario_dni' => 'PROF001',
            'fichero' => 'assumptes-particulars/resolucions/existent.pdf',
        ]);
    }
}
