<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Convalidacio;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
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
        if (!Schema::hasTable('convalidacions_moduls_fol_logse')) {
            Schema::create('convalidacions_moduls_fol_logse', function (Blueprint $table): void {
                $table->string('codigo', 6)->primary();
                $table->string('modul');
                $table->string('cicle');
                $table->string('nivell', 2);
                $table->string('sistema', 10);
            });
        }
        DB::table('convalidacions_moduls_fol_logse')->delete();
        DB::table('convalidacions_moduls_fol_logse')->insert([
            'codigo' => 'M1', 'modul' => 'Formació i orientació laboral', 'cicle' => 'Cicle de prova', 'nivell' => 'GM', 'sistema' => 'LOGSE',
        ]);
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
        $this->assertSame('Mòdul ordinari', $perModul['M1']['nom_modul_val']);
        $this->assertSame('Módulo ordinario', $perModul['M1']['nom_modul_cas']);
        $this->assertSame('Cicle de prova', $perModul['M1']['nom_cicle_val']);
        $this->assertSame('Ciclo de prueba', $perModul['M1']['nom_cicle_cas']);
        $this->assertSame('FAM', $perModul['M1']['familia_professional']);
        $this->assertSame('Família professional', $perModul['M1']['familia_professional_val']);
        $this->assertSame('Familia profesional', $perModul['M1']['familia_professional_cas']);
        $this->assertSame('CICLE', $perModul['M1']['nivell_formatiu_origen']);
        $this->assertSame('Cicle de prova', $perModul['M1']['nivell_formatiu_origen_val']);
        $this->assertSame('Ciclo de prueba', $perModul['M1']['nivell_formatiu_origen_cas']);
        $this->assertArrayNotHasKey('source', $resultats[0]);
    }

    public function test_familia_no_resoluble_no_es_considera_valida(): void
    {
        $xml = str_replace('padre="FAM"', 'padre="INEXISTENT"', $this->xml('6', '4', '7'));
        Storage::disk('convalidacions_xml')->put('2024.xml', $xml);

        $resultats = $this->service->aprovats('12345678');

        $this->assertNull($resultats[0]['familia_professional']);
        $this->assertNull($resultats[0]['familia_professional_val']);
        $this->assertNull($resultats[0]['familia_professional_cas']);
    }

    public function test_nivell_origen_es_el_node_immediatament_inferior_a_la_familia(): void
    {
        $xml = str_replace(
            '<curso codigo="CICLE" padre="FAM" nombre_val="Cicle de prova" nombre_cas="Ciclo de prueba"/>',
            '<curso codigo="NIVELL" padre="FAM" nombre_val="Grau superior" nombre_cas="Grado superior"/>\n'
                . '    <curso codigo="CICLE" padre="NIVELL" nombre_val="Cicle de prova" nombre_cas="Ciclo de prueba"/>',
            $this->xml('6', '4', '7')
        );
        Storage::disk('convalidacions_xml')->put('2024.xml', $xml);

        $resultat = collect($this->service->aprovats('12345678'))->firstWhere('modul', 'M1');

        $this->assertSame('NIVELL', $resultat['nivell_formatiu_origen']);
        $this->assertSame('Grau superior', $resultat['nivell_formatiu_origen_val']);
        $this->assertSame('Grado superior', $resultat['nivell_formatiu_origen_cas']);
    }

    public function test_classifica_fol_logse_només_per_coincidencia_exacta_de_codi(): void
    {
        $xml = str_replace(
            '<curso codigo="CICLE" padre="FAM" nombre_val="Cicle de prova" nombre_cas="Ciclo de prueba"/>',
            '<curso codigo="CICLE" padre="FAM" normativa="LOGSE" nombre_val="Cicle de prova" nombre_cas="Ciclo de prueba"/>',
            $this->xml('6', '4', '7')
        );
        Storage::disk('convalidacions_xml')->put('2024.xml', $xml);

        $resultat = collect($this->service->aprovats('12345678'))->firstWhere('modul', 'M1');

        $this->assertTrue($resultat['fol_logse_catalog']);
        $this->assertSame('Cicle de prova', $resultat['fol_logse_cicle']);
        $this->assertSame('GM', $resultat['fol_logse_nivell']);
    }

    public function test_ignora_els_atributs_de_normativa_de_l_xml(): void
    {
        Storage::disk('convalidacions_xml')->put('2024.xml', $this->xml('6', '4', '7'));

        $resultat = collect($this->service->aprovats('12345678'))->firstWhere('modul', 'M2');

        $this->assertFalse($resultat['fol_logse_catalog']);
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
    <curso codigo="FAM" padre="   " nombre_val="Família professional" nombre_cas="Familia profesional"/>
    <curso codigo="CICLE" padre="FAM" nombre_val="Cicle de prova" nombre_cas="Ciclo de prueba"/>
    <curso codigo="CURS" padre="CICLE" nombre_val="Primer" nombre_cas="Primero"/>
  </cursos>
  <contenidos>
    <contenido curso="CURS" codigo="M1" nombre_val="Mòdul ordinari" nombre_cas="Módulo ordinario"/>
    <contenido curso="CURS" codigo="M2" nombre_val="Mòdul extraordinari" nombre_cas="Módulo extraordinario"/>
    <contenido curso="CURS" codigo="M3" nombre_val="Mòdul suspés" nombre_cas="Módulo suspenso"/>
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
