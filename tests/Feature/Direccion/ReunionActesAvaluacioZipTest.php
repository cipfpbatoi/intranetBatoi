<?php

declare(strict_types=1);

namespace Tests\Feature\Direccion;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Intranet\Application\Reunion\ReunionActaExportService;
use Intranet\Entities\Profesor;
use Tests\TestCase;
use ZipArchive;

/**
 * Proves de descàrrega agrupada d'actes d'avaluació arxivades.
 */
class ReunionActesAvaluacioZipTest extends TestCase
{
    private string $sqlitePath;

    protected function setUp(): void
    {
        parent::setUp();

        if (!class_exists(ZipArchive::class)) {
            $this->markTestSkipped('ZipArchive no està disponible en este entorn.');
        }

        Carbon::setTestNow(Carbon::parse('2026-09-03 10:00:00'));
        $this->sqlitePath = storage_path('reunion_actes_avaluacio_zip_testing.sqlite');
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
        $this->insertProfesor('DIR001', (int) config('roles.rol.direccion'));
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        Schema::connection('sqlite')->dropIfExists('documentos');
        Schema::connection('sqlite')->dropIfExists('reuniones');
        Schema::connection('sqlite')->dropIfExists('profesores');

        foreach (glob(storage_path('app/zip/actes_avaluacio_2026-2027_*.zip')) ?: [] as $file) {
            @unlink($file);
        }

        foreach (glob(storage_path('app/gestor/2026-2027/Reunion/Acta_*.pdf')) ?: [] as $file) {
            @unlink($file);
        }

        if (file_exists($this->sqlitePath)) {
            @unlink($this->sqlitePath);
        }

        parent::tearDown();
    }

    public function test_exporta_zip_amb_actes_arxivades_de_l_avaluacio_i_curs_demanats(): void
    {
        $this->createActa(1, 31, '2026-2027', '1DAM');
        $this->createActa(2, 31, '2026-2027', '2DAM');
        $this->createActa(3, 32, '2026-2027', '1DAW');
        $this->createActa(4, 31, '2025-2026', '1ASIX');
        $this->createActa(5, 31, '2026-2027', 'PROJECTE', 11);
        $this->createActa(6, 31, '2026-2027', 'NOARXIVADA', 7, false);

        $result = app(ReunionActaExportService::class)->export(31, '2026-2027');

        $this->assertSame(2, $result['documents']);
        $this->assertSame(2, $result['exported']);
        $this->assertFileExists($result['path']);

        $zip = new ZipArchive();
        $this->assertTrue($zip->open($result['path']));
        $this->assertNotFalse($zip->locateName('1DAM/Acta_1.pdf'));
        $this->assertNotFalse($zip->locateName('2DAM/Acta_2.pdf'));
        $this->assertFalse($zip->locateName('1DAW/Acta_3.pdf'));
        $this->assertFalse($zip->locateName('1ASIX/Acta_4.pdf'));
        $this->assertFalse($zip->locateName('PROJECTE/Acta_5.pdf'));
        $this->assertFalse($zip->locateName('NOARXIVADA/Acta_6.pdf'));
        $zip->close();
    }

    public function test_no_genera_zip_si_no_hi_ha_fitxers_valids(): void
    {
        $this->createActa(10, 31, '2026-2027', '1DAM', 7, true, false);

        $result = app(ReunionActaExportService::class)->export(31, '2026-2027');

        $this->assertNull($result['path']);
        $this->assertSame(1, $result['documents']);
        $this->assertSame(0, $result['exported']);
        $this->assertCount(1, $result['missing']);
    }

    public function test_ruta_de_direccio_descarrega_zip_de_l_avaluacio(): void
    {
        $this->createActa(20, 31, '2026-2027', '1DAM');

        $response = $this
            ->actingAs(Profesor::on('sqlite')->findOrFail('DIR001'), 'profesor')
            ->get(route('reunion.actesAvaluacio.zip', ['numero' => 31]));

        $response->assertOk();
        $response->assertDownload('actes_avaluacio_2026-2027_1Ava.zip');
    }

    private function createSchema(): void
    {
        Schema::connection('sqlite')->create('profesores', function (Blueprint $table): void {
            $table->string('dni', 10)->primary();
            $table->string('nombre')->nullable();
            $table->string('apellido1')->nullable();
            $table->string('apellido2')->nullable();
            $table->string('email')->nullable();
            $table->unsignedInteger('rol')->default(3);
            $table->boolean('activo')->default(true);
            $table->date('fecha_baja')->nullable();
            $table->timestamps();
        });

        Schema::connection('sqlite')->create('reuniones', function (Blueprint $table): void {
            $table->increments('id');
            $table->unsignedTinyInteger('tipo')->default(0);
            $table->string('curso')->nullable();
            $table->string('numero', 2)->nullable();
            $table->dateTime('fecha')->nullable();
            $table->string('descripcion')->nullable();
            $table->string('idProfesor', 10)->nullable();
            $table->string('idEspacio', 10)->nullable();
            $table->boolean('archivada')->default(false);
            $table->string('fichero')->nullable();
            $table->timestamps();
        });

        Schema::connection('sqlite')->create('documentos', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('tipoDocumento')->nullable();
            $table->string('curso')->nullable();
            $table->integer('idDocumento')->nullable();
            $table->string('propietario')->nullable();
            $table->string('supervisor')->nullable();
            $table->string('descripcion')->nullable();
            $table->string('grupo')->nullable();
            $table->string('fichero')->nullable();
            $table->string('tags')->nullable();
            $table->integer('rol')->default(3);
            $table->timestamps();
        });
    }

    private function insertProfesor(string $dni, int $rol): void
    {
        DB::table('profesores')->insert([
            'dni' => $dni,
            'nombre' => 'Test',
            'apellido1' => 'Direccio',
            'apellido2' => 'Usuari',
            'email' => strtolower($dni) . '@test.local',
            'rol' => $rol,
            'activo' => 1,
            'fecha_baja' => null,
        ]);
    }

    private function createActa(
        int $id,
        int $numero,
        string $curso,
        string $grupo,
        int $tipo = 7,
        bool $archivada = true,
        bool $withFile = true
    ): void {
        $fichero = "gestor/$curso/Reunion/Acta_$id.pdf";
        if ($withFile) {
            $path = storage_path('app/' . $fichero);
            if (!is_dir(dirname($path))) {
                mkdir(dirname($path), 0775, true);
            }
            file_put_contents($path, "PDF $id");
        }

        DB::table('reuniones')->insert([
            'id' => $id,
            'tipo' => $tipo,
            'curso' => $curso,
            'numero' => (string) $numero,
            'fecha' => '2026-09-01 12:00:00',
            'descripcion' => "Acta $grupo",
            'idProfesor' => 'DIR001',
            'idEspacio' => 'A1',
            'archivada' => $archivada ? 1 : 0,
            'fichero' => $fichero,
        ]);

        DB::table('documentos')->insert([
            'tipoDocumento' => 'Acta',
            'curso' => $curso,
            'idDocumento' => $id,
            'propietario' => 'Test Direccio Usuari',
            'supervisor' => 'Test Direccio Usuari',
            'descripcion' => "Acta $grupo",
            'grupo' => $grupo,
            'fichero' => $fichero,
            'tags' => 'Reunió Avaluació',
            'rol' => config('roles.rol.profesor'),
        ]);
    }
}
