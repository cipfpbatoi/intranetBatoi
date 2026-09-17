<?php

declare(strict_types=1);

namespace Tests\Feature\AssumpteParticular;

use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Intranet\Application\AssumpteParticular\AssumpteParticularDocumentService;
use Intranet\Application\AssumpteParticular\AssumpteParticularException;
use Intranet\Application\AssumpteParticular\RubricaAssumpteParticularService;
use Intranet\Entities\AssumpteParticular;
use Intranet\Entities\Profesor;
use setasign\Fpdi\Fpdi;
use Smalot\PdfParser\Parser;
use Tests\TestCase;

/**
 * Proves de composició del model oficial amb les dues rúbriques.
 */
class AssumpteParticularDocumentServiceTest extends TestCase
{
    private string $sqlitePath;

    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow('2026-09-15 12:00:00');
        $this->sqlitePath = tempnam(sys_get_temp_dir(), 'assumptes-document-') ?: ':memory:';
        config(['database.default' => 'sqlite']);
        config(['database.connections.sqlite.database' => $this->sqlitePath]);
        DB::setDefaultConnection('sqlite');
        DB::purge('sqlite');
        DB::reconnect('sqlite');
        Storage::fake('local');
        Storage::fake('public');
        $this->crearEsquema();
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        DB::disconnect('sqlite');
        if ($this->sqlitePath !== ':memory:' && file_exists($this->sqlitePath)) {
            @unlink($this->sqlitePath);
        }

        parent::tearDown();
    }

    public function test_genera_una_unica_pagina_amb_les_dues_rubriques(): void
    {
        $professor = $this->crearProfessor('PROF001', 'Professor');
        $directora = $this->crearProfessor('DIR001', 'Directora');
        $this->crearRubrica($professor);
        $this->crearRubrica($directora);
        $peticio = $this->crearPeticio($professor);

        $ruta = $this->servei()->generarAutoritzada($peticio, $directora);

        Storage::disk('local')->assertExists($ruta);
        $pdf = new Fpdi();
        $this->assertSame(1, $pdf->setSourceFile(Storage::disk('local')->path($ruta)));
        $text = (new Parser())->parseFile(Storage::disk('local')->path($ruta))->getText();
        $this->assertStringContainsString('Alcoi', $text);
        $this->assertStringContainsString('600000000', $text);
        $this->assertStringContainsString('PROFESSOR SECUNDÀRIA', $text);
        $this->assertStringContainsString('INFORMÀTICA', $text);
        $this->assertStringContainsString('Lectius: 0', $text);
        $this->assertStringContainsString('No lectius: 0', $text);
    }

    public function test_el_pdf_compta_els_dies_anteriors_pero_no_la_peticio_actual(): void
    {
        $professor = $this->crearProfessor('PROF001', 'Professor');
        $directora = $this->crearProfessor('DIR001', 'Directora');
        $this->crearRubrica($professor);
        $this->crearRubrica($directora);
        $peticio = $this->crearPeticio($professor);
        AssumpteParticular::query()->create([
            'idProfesor' => $professor->dni,
            'data_gaudi' => '2026-10-01',
            'curs' => '2026-2027',
            'tipus' => AssumpteParticular::TIPUS_LECTIU,
            'torn' => AssumpteParticular::TORN_MATI,
            'estat' => AssumpteParticular::ESTAT_AUTORITZADA,
        ]);

        $ruta = $this->servei()->generarAutoritzada($peticio, $directora);
        $text = (new Parser())->parseFile(Storage::disk('local')->path($ruta))->getText();

        $this->assertStringContainsString('Lectius: 1', $text);
        $this->assertStringContainsString('No lectius: 0', $text);
    }

    public function test_no_genera_document_si_falta_la_rubrica_de_la_directora(): void
    {
        $professor = $this->crearProfessor('PROF001', 'Professor');
        $directora = $this->crearProfessor('DIR001', 'Directora');
        $this->crearRubrica($professor);
        $peticio = $this->crearPeticio($professor);

        $this->expectException(AssumpteParticularException::class);
        $this->expectExceptionMessage('director o directora no té una rúbrica');

        $this->servei()->generarAutoritzada($peticio, $directora);
    }

    private function servei(): AssumpteParticularDocumentService
    {
        return new AssumpteParticularDocumentService(new RubricaAssumpteParticularService());
    }

    private function crearEsquema(): void
    {
        Schema::create('profesores', function (Blueprint $table): void {
            $table->string('dni')->primary();
            $table->string('nombre')->nullable();
            $table->string('apellido1')->nullable();
            $table->string('apellido2')->nullable();
            $table->string('domicilio')->nullable();
            $table->string('movil1')->nullable();
            $table->string('movil2')->nullable();
            $table->string('codigo_postal')->nullable();
            $table->string('localitat')->nullable();
            $table->string('especialitat')->nullable();
            $table->string('foto')->nullable();
            $table->timestamps();
        });
        Schema::create('assumptes_particulars', function (Blueprint $table): void {
            $table->id();
            $table->string('idProfesor');
            $table->date('data_gaudi');
            $table->string('curs');
            $table->string('tipus');
            $table->string('torn');
            $table->string('estat');
            $table->timestamps();
        });
    }

    private function crearProfessor(string $dni, string $nom): Profesor
    {
        DB::table('profesores')->insert([
            'dni' => $dni,
            'nombre' => $nom,
            'apellido1' => 'Cognom',
            'apellido2' => 'Segon',
            'domicilio' => 'Carrer de prova, 1',
            'movil1' => '600000000',
            'codigo_postal' => '03801',
            'localitat' => 'Alcoi',
            'especialitat' => 'PROFESSOR SECUNDÀRIA ESPECIALITAT INFORMÀTICA',
            'foto' => $dni . '.png',
        ]);

        return Profesor::query()->findOrFail($dni);
    }

    private function crearRubrica(Profesor $professor): void
    {
        $png = base64_decode(
            'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+A8AAQUBAScY42YAAAAASUVORK5CYII=',
            true
        );
        Storage::disk('public')->put('signatures/' . $professor->foto, $png ?: '');
    }

    private function crearPeticio(Profesor $professor): AssumpteParticular
    {
        return AssumpteParticular::query()->create([
            'idProfesor' => $professor->dni,
            'data_gaudi' => '2026-10-15',
            'curs' => '2026-2027',
            'tipus' => AssumpteParticular::TIPUS_LECTIU,
            'torn' => AssumpteParticular::TORN_MATI,
            'estat' => AssumpteParticular::ESTAT_PENDENT,
        ]);
    }
}
