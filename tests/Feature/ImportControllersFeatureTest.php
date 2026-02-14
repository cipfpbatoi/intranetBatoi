<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\View\View;
use Intranet\Http\Controllers\ImportController;
use Intranet\Http\Controllers\ImportEmailController;
use Intranet\Http\Controllers\TeacherImportController;
use Intranet\Http\Middleware\RoleMiddleware;
use Intranet\Http\Middleware\VerifyCsrfToken;
use Tests\TestCase;

class ImportControllersFeatureTest extends TestCase
{
    public function test_import_email_create_mostra_la_vista(): void
    {
        $controller = app(ImportEmailController::class);
        $view = $controller->create();

        $this->assertInstanceOf(View::class, $view);
        $this->assertSame('seeder.importEmail', $view->name());
    }

    public function test_import_email_store_falla_si_no_hi_ha_fitxer(): void
    {
        $response = $this->from('/importEmail')
            ->withoutMiddleware([RoleMiddleware::class, VerifyCsrfToken::class])
            ->post(route('importEmail.store'), []);

        $response->assertStatus(302);
        $response->assertRedirect('/importEmail');
    }

    public function test_import_email_store_falla_si_fitxer_no_es_csv(): void
    {
        $file = UploadedFile::fake()->create('emails.txt', 4, 'text/plain');

        $response = $this->from('/importEmail')
            ->withoutMiddleware([RoleMiddleware::class, VerifyCsrfToken::class])
            ->post(route('importEmail.store'), ['fichero' => $file]);

        $response->assertStatus(302);
        $response->assertRedirect('/importEmail');
    }

    public function test_import_store_falla_si_no_hi_ha_fitxer(): void
    {
        $response = $this->from('/import')
            ->withoutMiddleware([RoleMiddleware::class, VerifyCsrfToken::class])
            ->post(route('import.store'), []);

        $response->assertStatus(302);
        $response->assertRedirect('/import');
    }

    public function test_import_store_falla_si_fitxer_no_es_xml(): void
    {
        $file = UploadedFile::fake()->create('import.csv', 4, 'text/csv');

        $response = $this->from('/import')
            ->withoutMiddleware([RoleMiddleware::class, VerifyCsrfToken::class])
            ->post(route('import.store'), ['fichero' => $file]);

        $response->assertStatus(302);
        $response->assertRedirect('/import');
    }

    public function test_import_store_amb_xml_valid_torna_vista_store(): void
    {
        $controller = $this->partialMock(ImportController::class, function ($mock): void {
            $mock->shouldReceive('run')->once();
            $mock->shouldReceive('asignarTutores')->once();
        });

        $xml = <<<'XML'
<?xml version="1.0"?>
<centro codigo="03012165" denominacion="CIPFP BATOI" curso="2025" fechaExportacion="09/09/2025 14:13:17" version="1.0">
  <aulas>
    <aula codigo="A-213" nombre="A-213 - Aula de pissarra" capacidad="35" m2="60" observaciones="Edifici 2 - Planta 1"/>
  </aulas>
  <grupos>
    <grupo codigo="1CFSC" nombre="1R CFS ASIX -V- (LOE)" ensenanza="5" linea="1" turno="D" modalidad="COM" aula="T-117" capacidad="1" tutor_ppal=" " tutor_sec=" " oficial="S"/>
  </grupos>
  <alumnos>
    <alumno NIA="10861014" nombre="PAU" apellido1="FRAU" apellido2="MASANET" fecha_nac="30/01/2008" municipio_nac="9" municipio_nac_ext=" " provincia_nac="3" pais_nac="724" nacionalidad="724" sexo="H" tipo_doc="O" documento="029570816S" expediente=" " libro_escolaridad=" " cod_postal="03803" tipo_via="AV" domicilio="Avenida Alameda Camilo Sesto" numero="43B" puerta="izq" escalera=" " letra=" " piso="2" provincia="3" municipio="9" localidad="108525" telefono1="640355344" telefono2=" " telefono3=" " email1="fraumasanetpau45@gmail.com" email2=" " sip="7195255" nuss="031161419445" observaciones=" " ampa=" " seguro="N" fecha_matricula="29/07/2025" fecha_ingreso_centro="01/09/2025" estado_matricula="M" tipo_matricula="OR" repite="0" num_repeticion="0" ensenanza="5" curso="3306170449" grupo="1CFSC" turno="D" linea="1" trabaja="N" fuera_comunidad="N" matricula_parcial="N" matricula_condic="N" informe_medico="N" banco=" " sucursal=" " digito_control=" " cuenta=" " modalidad="COM" iban=" "/>
  </alumnos>
</centro>
XML;

        $file = UploadedFile::fake()->createWithContent('import.xml', $xml);
        $request = Request::create('/import', 'POST', ['primera' => 'on'], [], ['fichero' => $file]);

        $view = $controller->store($request);

        $this->assertInstanceOf(View::class, $view);
        $this->assertSame('seeder.store', $view->name());
    }

    public function test_import_store_amb_primera_off_no_crida_asignar_tutores(): void
    {
        $controller = $this->partialMock(ImportController::class, function ($mock): void {
            $mock->shouldReceive('run')->once();
            $mock->shouldNotReceive('asignarTutores');
        });

        $xml = <<<'XML'
<?xml version="1.0"?>
<centro codigo="03012165" denominacion="CIPFP BATOI" curso="2025" fechaExportacion="09/09/2025 14:13:17" version="1.0">
  <aulas>
    <aula codigo="A-213" nombre="A-213 - Aula de pissarra" capacidad="35" m2="60" observaciones="Edifici 2 - Planta 1"/>
  </aulas>
</centro>
XML;

        $file = UploadedFile::fake()->createWithContent('import.xml', $xml);
        $request = Request::create('/import', 'POST', ['primera' => 'off'], [], ['fichero' => $file]);

        $view = $controller->store($request);

        $this->assertInstanceOf(View::class, $view);
        $this->assertSame('seeder.store', $view->name());
    }

    public function test_teacher_import_store_falla_si_no_hi_ha_fitxer(): void
    {
        $response = $this->from('/teacherImport')
            ->withoutMiddleware([RoleMiddleware::class, VerifyCsrfToken::class])
            ->post(route('teacherImport.store'), []);

        $response->assertStatus(302);
        $response->assertRedirect('/teacherImport');
    }

    public function test_teacher_import_store_falla_si_fitxer_no_es_xml(): void
    {
        $file = UploadedFile::fake()->create('teacher.csv', 4, 'text/csv');

        $response = $this->from('/teacherImport')
            ->withoutMiddleware([RoleMiddleware::class, VerifyCsrfToken::class])
            ->post(route('teacherImport.store'), ['fichero' => $file]);

        $response->assertStatus(302);
        $response->assertRedirect('/teacherImport');
    }

    public function test_teacher_import_store_amb_xml_valid_torna_vista_store(): void
    {
        $controller = $this->partialMock(TeacherImportController::class, function ($mock): void {
            $mock->shouldReceive('run')->once();
        });

        $xml = <<<'XML'
<?xml version="1.0"?>
<centro codigo="03012165" denominacion="CIPFP BATOI" curso="2025" fechaExportacion="09/09/2025 14:13:17" version="1.0">
  <docentes>
    <docente documento="021648508B" nombre="PROVA" apellido1="DOCENT" apellido2="TEST" sexo="H" cod_postal="03803" domicilio="Carrer prova" telefono1="600000000" telefono2=" " email1="docent@test.local" titular_sustituido=" " fecha_nac="01/01/1980" fecha_ingreso="01/09/2010" fecha_antiguedad="01/09/2010"/>
  </docentes>
</centro>
XML;

        $file = UploadedFile::fake()->createWithContent('teacher_import.xml', $xml);
        $request = Request::create('/teacherImport', 'POST', ['horari' => false], [], ['fichero' => $file]);

        $view = $controller->store($request);

        $this->assertInstanceOf(View::class, $view);
        $this->assertSame('seeder.store', $view->name());
    }

}
