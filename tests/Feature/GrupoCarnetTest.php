<?php

namespace Tests\Feature;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Intranet\Http\Controllers\GrupoController;
use Intranet\Services\Document\PdfService;
use Mockery;
use ReflectionClass;
use Tests\TestCase;

/**
 * Verifica la selecció d'alumnat per als carnets de grup amb una BD aïllada.
 */
class GrupoCarnetTest extends TestCase
{
    /**
     * Prepara les dades mínimes sense utilitzar la BD de l'aplicació.
     */
    protected function setUp(): void
    {
        parent::setUp();

        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');

        Schema::create('alumnos', function (Blueprint $table): void {
            $table->string('nia')->primary();
            $table->string('apellido1');
            $table->string('apellido2');
            $table->string('foto')->nullable();
        });
        Schema::create('alumnos_grupos', function (Blueprint $table): void {
            $table->string('idAlumno');
            $table->string('idGrupo');
        });
    }

    /**
     * Exclou fotos nul·les o buides i alumnat d'altres grups.
     */
    public function test_carnets_nomes_inclouen_alumnat_del_grup_amb_foto(): void
    {
        $this->insertAlumne('1', 'Zulu', 'A', '1.jpg');
        $this->insertAlumne('2', 'Alpha', 'B', null);
        $this->insertAlumne('3', 'Alpha', 'C', '');
        $this->insertAlumne('4', 'Alpha', 'A', '4.jpg');
        $this->insertAlumne('5', 'Altre', 'A', '5.jpg', 'G2');

        $this->assertCarnets(['4', '1']);
    }

    /**
     * Conserva tots els carnets i l'ordre pels dos cognoms quan hi ha foto.
     */
    public function test_conserva_tot_lalumnat_amb_foto_ordenat_per_cognoms(): void
    {
        $this->insertAlumne('1', 'Zulu', 'A', '1.jpg');
        $this->insertAlumne('2', 'Alpha', 'B', '2.jpg');
        $this->insertAlumne('3', 'Alpha', 'A', '3.jpg');

        $this->assertCarnets(['3', '2', '1']);
    }

    /**
     * No envia cap alumne al PDF si ningú té foto.
     */
    public function test_grup_sense_fotos_no_envia_carnets_al_pdf(): void
    {
        $this->insertAlumne('1', 'Alpha', 'A', null);
        $this->insertAlumne('2', 'Beta', 'B', '');

        $this->assertCarnets([]);
    }

    /**
     * Crea un alumne i la seua matrícula en el grup indicat.
     */
    private function insertAlumne(string $nia, string $primer, string $segon, ?string $foto, string $grup = 'G1'): void
    {
        DB::table('alumnos')->insert(['nia' => $nia, 'apellido1' => $primer, 'apellido2' => $segon, 'foto' => $foto]);
        DB::table('alumnos_grupos')->insert(['idAlumno' => $nia, 'idGrupo' => $grup]);
    }

    /**
     * Comprova les dades reals de la consulta abans del renderitzat del PDF.
     *
     * @param array<int, string> $nias Identificadors esperats en ordre.
     */
    private function assertCarnets(array $nias): void
    {
        $document = Mockery::mock();
        $document->shouldReceive('stream')->once()->andReturn('pdf');
        $pdf = Mockery::mock(PdfService::class);
        $pdf->shouldReceive('hazPdf')->once()->withArgs(function ($vista, Collection $alumnat) use ($nias): bool {
            $this->assertSame('pdf.carnet', $vista);
            $this->assertSame($nias, $alumnat->pluck('nia')->all());

            return true;
        })->andReturn($document);
        $this->app->instance(PdfService::class, $pdf);

        $controller = (new ReflectionClass(GrupoController::class))->newInstanceWithoutConstructor();
        $this->assertSame('pdf', $controller->carnet('G1'));
    }
}
