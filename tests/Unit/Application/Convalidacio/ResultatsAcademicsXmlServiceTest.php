<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Convalidacio;

use Illuminate\Support\Facades\Storage;
use Intranet\Application\Convalidacio\ConvalidacioException;
use Intranet\Application\Convalidacio\ResultatsAcademicsXmlService;
use Tests\TestCase;

/** Proves del lector privat de resultats acadèmics. */
class ResultatsAcademicsXmlServiceTest extends TestCase
{
    private ResultatsAcademicsXmlService $service;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('convalidacions_xml');
        $this->service = app(ResultatsAcademicsXmlService::class);
    }

    public function test_retorna_fi_i_ex_aprovades_i_omet_els_suspesos(): void
    {
        Storage::disk('convalidacions_xml')->put('2024.xml', $this->xml('6', '4', '7'));

        $resultats = $this->service->aprovats('12345678');
        $perModul = collect($resultats)->keyBy('modul');

        $this->assertCount(2, $resultats);
        $this->assertSame('ordinària (FI)', $perModul['M1']['convocatoria']);
        $this->assertSame('extraordinària (EX)', $perModul['M2']['convocatoria']);
        $this->assertSame(2024, $perModul['M1']['any']);
        $this->assertSame(7.0, $perModul['M2']['nota']);
        $this->assertArrayNotHasKey('source', $resultats[0]);
    }

    public function test_mante_separat_el_mateix_resultat_de_dos_fitxers(): void
    {
        Storage::disk('convalidacions_xml')->put('2023.xml', $this->xml('6', '4', '7'));
        Storage::disk('convalidacions_xml')->put('2024.xml', $this->xml('6', '4', '7'));

        $resultats = array_values(array_filter(
            $this->service->aprovats('12345678'),
            static fn (array $resultat): bool => $resultat['modul'] === 'M1'
        ));

        $this->assertCount(2, $resultats);
        $this->assertNotSame($resultats[0]['id'], $resultats[1]['id']);
    }

    public function test_rebutja_estructura_invalida_i_declaracions_d_entitat(): void
    {
        foreach (['<centro/>', '<!DOCTYPE foo [<!ENTITY xxe SYSTEM "file:///etc/passwd">]><centro/>'] as $xml) {
            try {
                $this->service->validarContingut($xml);
                $this->fail('L\'XML havia de ser rebutjat.');
            } catch (ConvalidacioException) {
                $this->addToAssertionCount(1);
            }
        }
    }

    public function test_rebutja_avaluacions_sense_un_any_valid(): void
    {
        foreach (['', '24', '2024-2025', 'dos mil vint-i-quatre'] as $any) {
            try {
                $this->service->validarContingut($this->xml('6', '4', '7', $any));
                $this->fail('L\'avaluació sense un any de quatre dígits havia de ser rebutjada.');
            } catch (ConvalidacioException $exception) {
                $this->assertStringContainsString('curs acadèmic vàlid', $exception->getMessage());
            }
        }
    }

    public function test_una_seleccio_opaca_de_un_altre_alumne_no_es_resol(): void
    {
        Storage::disk('convalidacions_xml')->put('2024.xml', $this->xml('6', '4', '7'));
        $id = $this->service->aprovats('12345678')[0]['id'];

        $this->assertNull($this->service->trobarAprovat('87654321', $id));
    }

    /** Genera una font sintètica amb FI, EX i un suspés. */
    private function xml(string $notaFiM1, string $notaFiM2, string $notaExM2, string $any = '2024'): string
    {
        return <<<XML
<?xml version="1.0"?>
<centro curso="{$any}">
  <cursos>
    <curso codigo="CICLE" padre="" nombre_val="Cicle de prova"/>
    <curso codigo="CURS" padre="CICLE" nombre_val="Primer"/>
  </cursos>
  <contenidos>
    <contenido curso="CURS" codigo="M1" nombre_val="Mòdul ordinari"/>
    <contenido curso="CURS" codigo="M2" nombre_val="Mòdul extraordinari"/>
    <contenido curso="CURS" codigo="M3" nombre_val="Mòdul suspés"/>
  </contenidos>
  <calificaciones>
    <calificacion alumno="12345678" curso="CURS" contenido="M1" evaluacion="FI" nota_numerica="{$notaFiM1}"/>
    <calificacion alumno="12345678" curso="CURS" contenido="M2" evaluacion="FI" nota_numerica="{$notaFiM2}"/>
    <calificacion alumno="12345678" curso="CURS" contenido="M2" evaluacion="EX" nota_numerica="{$notaExM2}"/>
    <calificacion alumno="12345678" curso="CURS" contenido="M3" evaluacion="FI" nota_numerica="3"/>
  </calificaciones>
</centro>
XML;
    }
}
