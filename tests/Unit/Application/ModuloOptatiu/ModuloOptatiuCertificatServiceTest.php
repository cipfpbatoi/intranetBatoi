<?php

declare(strict_types=1);

namespace Tests\Unit\Application\ModuloOptatiu;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Intranet\Application\ModuloOptatiu\ModuloOptatiuCertificatService;
use Intranet\Application\Profesor\ProfesorService;
use Intranet\Entities\Alumno;
use Intranet\Entities\Modulo_grupo;
use Intranet\Entities\ModulOptatiuCertificat;
use Intranet\Entities\Profesor;
use Intranet\Jobs\SendEmail;
use Intranet\Services\Document\PdfService;
use Intranet\Services\School\ModuloGrupoService;
use Intranet\Services\School\SecretariaService;
use Tests\TestCase;

/**
 * Proves del cas d'ús de certificats de mòduls optatius.
 */
class ModuloOptatiuCertificatServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('database.default', 'sqlite');
        config()->set('database.connections.sqlite.database', ':memory:');
        DB::purge('sqlite');
        DB::reconnect('sqlite');

        $schema = Schema::connection('sqlite');
        foreach ([
            'modul_optatiu_certificat_alumnes',
            'modul_optatiu_certificats',
            'alumno_resultados',
            'alumnos_grupos',
            'alumnos',
            'modulo_grupos',
            'modulo_ciclos',
            'modulos',
            'grupos',
        ] as $table) {
            $schema->dropIfExists($table);
        }

        $schema->create('modulos', function (Blueprint $table): void {
            $table->string('codigo', 20)->primary();
            $table->string('cliteral')->nullable();
            $table->string('vliteral')->nullable();
        });

        $schema->create('modulo_ciclos', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('idModulo', 20)->nullable();
            $table->unsignedInteger('idCiclo')->nullable();
            $table->unsignedInteger('idDepartamento')->nullable();
            $table->unsignedTinyInteger('curso')->default(1);
        });

        $schema->create('grupos', function (Blueprint $table): void {
            $table->string('codigo', 10)->primary();
            $table->unsignedInteger('idCiclo')->nullable();
            $table->string('nombre')->nullable();
            $table->string('turno', 1)->nullable();
            $table->timestamps();
        });

        $schema->create('modulo_grupos', function (Blueprint $table): void {
            $table->increments('id');
            $table->unsignedInteger('idModuloCiclo')->nullable();
            $table->string('idGrupo', 10)->nullable();
        });

        $schema->create('alumnos', function (Blueprint $table): void {
            $table->string('nia', 8)->primary();
            $table->string('dni')->nullable();
            $table->string('nombre')->nullable();
            $table->string('apellido1')->nullable();
            $table->string('apellido2')->nullable();
            $table->string('email')->nullable();
            $table->string('sexo', 1)->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        $schema->create('alumnos_grupos', function (Blueprint $table): void {
            $table->string('idAlumno', 8);
            $table->string('idGrupo', 10);
        });

        $schema->create('alumno_resultados', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('idAlumno', 8);
            $table->unsignedInteger('idModuloGrupo');
            $table->unsignedTinyInteger('nota')->default(0);
            $table->unsignedTinyInteger('valoraciones')->default(0);
            $table->string('observaciones', 200)->nullable();
        });

        $schema->create('modul_optatiu_certificats', function (Blueprint $table): void {
            $table->increments('id');
            $table->unsignedInteger('idModuloGrupo')->unique();
            $table->string('denominacio');
            $table->string('idProfesor', 10);
            $table->timestamps();
        });

        $schema->create('modul_optatiu_certificat_alumnes', function (Blueprint $table): void {
            $table->increments('id');
            $table->unsignedInteger('idCertificat');
            $table->string('idAlumno', 8);
            $table->timestamp('enviat_at')->nullable();
            $table->timestamp('registrat_at')->nullable();
            $table->string('fitxer')->nullable();
            $table->timestamps();
        });

        DB::table('modulos')->insert([
            [
                'codigo' => 'OPT1',
                'cliteral' => 'Optatiu antic',
                'vliteral' => 'Mòdul no optatiu',
            ],
            [
                'codigo' => 'CVOPT',
                'cliteral' => 'Optatiu',
                'vliteral' => 'Mòdul optatiu',
            ],
        ]);
        DB::table('modulo_ciclos')->insert([
            [
                'id' => 1,
                'idModulo' => 'OPT1',
                'idCiclo' => 1,
                'idDepartamento' => 1,
                'curso' => 1,
            ],
            [
                'id' => 2,
                'idModulo' => 'CVOPT',
                'idCiclo' => 1,
                'idDepartamento' => 1,
                'curso' => 1,
            ],
        ]);
        DB::table('grupos')->insert([
            'codigo' => 'G1',
            'idCiclo' => 1,
            'nombre' => 'Grup 1',
            'turno' => 'M',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('modulo_grupos')->insert([
            [
                'id' => 1,
                'idModuloCiclo' => 1,
                'idGrupo' => 'G1',
            ],
            [
                'id' => 2,
                'idModuloCiclo' => 2,
                'idGrupo' => 'G1',
            ],
        ]);
        DB::table('alumnos')->insert([
            [
                'nia' => 'A1',
                'dni' => '11111111A',
                'nombre' => 'ALFA',
                'apellido1' => 'U',
                'apellido2' => 'UNO',
                'email' => 'a1@example.test',
                'sexo' => 'H',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nia' => 'A2',
                'dni' => '22222222B',
                'nombre' => 'BETA',
                'apellido1' => 'D',
                'apellido2' => 'DOS',
                'email' => 'a2@example.test',
                'sexo' => 'M',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
        DB::table('alumnos_grupos')->insert([
            ['idAlumno' => 'A1', 'idGrupo' => 'G1'],
            ['idAlumno' => 'A2', 'idGrupo' => 'G1'],
        ]);
    }

    public function test_guarda_les_notes_en_alumno_resultados(): void
    {
        $service = new ModuloOptatiuCertificatService();
        $certificat = ModulOptatiuCertificat::query()->create([
            'idModuloGrupo' => 1,
            'denominacio' => 'Optativa aplicada',
            'idProfesor' => 'P1',
        ]);

        $saved = $service->save($certificat, 'Optativa actualitzada', [
            'A1' => 8,
            'A2' => 9,
        ]);

        $this->assertSame(2, $saved);
        $this->assertDatabaseHas('alumno_resultados', [
            'idAlumno' => 'A1',
            'idModuloGrupo' => 1,
            'nota' => 8,
        ]);
        $this->assertDatabaseHas('modul_optatiu_certificats', [
            'id' => $certificat->id,
            'denominacio' => 'Optativa actualitzada',
        ]);
    }

    public function test_el_selector_de_notes_te_una_sola_opcio_no_cursa(): void
    {
        $notes = collect((new ModuloOptatiuCertificatService())->noteOptions());

        $this->assertSame([12], $notes->filter(fn (string $nota): bool => $nota === 'No cursa')->keys()->all());
    }

    public function test_el_selector_de_notes_te_una_sola_opcio_no_supera(): void
    {
        $notes = (new ModuloOptatiuCertificatService())->noteOptions();

        $this->assertArrayNotHasKey(1, $notes);
        $this->assertArrayNotHasKey(2, $notes);
        $this->assertArrayNotHasKey(3, $notes);
        $this->assertSame('No supera', $notes[4]);
    }

    public function test_mostra_només_moduls_codificats_com_a_cvopt(): void
    {
        $moduloGrupoService = new class extends ModuloGrupoService {
            /**
             * Retorna mòduls de prova sense consultar l'horari real.
             *
             * @return array<int, Modulo_grupo>
             */
            public function misModulos(string $dni, ?string $modulo = null): array
            {
                return [
                    Modulo_grupo::query()->findOrFail(1),
                    Modulo_grupo::query()->findOrFail(2),
                ];
            }
        };
        $service = new ModuloOptatiuCertificatService($moduloGrupoService);

        $modules = $service->modulesForTeacher('P1');

        $this->assertSame([2], $modules->pluck('id')->all());
        $this->assertTrue($service->canManage(Modulo_grupo::query()->findOrFail(2), 'P1'));
        $this->assertFalse($service->canManage(Modulo_grupo::query()->findOrFail(1), 'P1'));
    }

    public function test_el_certificat_nou_no_usa_el_literal_generic_del_cvopt(): void
    {
        $service = new ModuloOptatiuCertificatService();

        $certificat = $service->certificateFor(Modulo_grupo::query()->findOrFail(2), 'P1');

        $this->assertSame('', $certificat->denominacio);
    }

    public function test_detecta_alumnat_sense_nota_abans_demetre(): void
    {
        $service = new ModuloOptatiuCertificatService();
        $certificat = ModulOptatiuCertificat::query()->create([
            'idModuloGrupo' => 1,
            'denominacio' => 'Optativa aplicada',
            'idProfesor' => 'P1',
        ]);
        DB::table('alumno_resultados')->insert([
            'idAlumno' => 'A1',
            'idModuloGrupo' => 1,
            'nota' => 7,
        ]);

        $errors = $service->validationErrors($certificat);

        $this->assertCount(1, $errors);
        $this->assertStringContainsString('Beta', $errors[0]);
    }

    public function test_no_permet_emetre_amb_el_literal_generic_del_cvopt(): void
    {
        $service = new ModuloOptatiuCertificatService();
        $certificat = ModulOptatiuCertificat::query()->create([
            'idModuloGrupo' => 2,
            'denominacio' => 'Mòdul optatiu',
            'idProfesor' => 'P1',
        ]);
        DB::table('alumno_resultados')->insert([
            [
                'idAlumno' => 'A1',
                'idModuloGrupo' => 2,
                'nota' => 7,
            ],
            [
                'idAlumno' => 'A2',
                'idModuloGrupo' => 2,
                'nota' => 8,
            ],
        ]);

        $errors = $service->validationErrors($certificat);

        $this->assertCount(1, $errors);
        $this->assertStringContainsString('denominació real', $errors[0]);
    }

    public function test_permet_emetre_amb_denominacio_real_i_notes_completes(): void
    {
        $service = new ModuloOptatiuCertificatService();
        $certificat = ModulOptatiuCertificat::query()->create([
            'idModuloGrupo' => 2,
            'denominacio' => 'Robòtica aplicada',
            'idProfesor' => 'P1',
        ]);
        DB::table('alumno_resultados')->insert([
            [
                'idAlumno' => 'A1',
                'idModuloGrupo' => 2,
                'nota' => 7,
            ],
            [
                'idAlumno' => 'A2',
                'idModuloGrupo' => 2,
                'nota' => 8,
            ],
        ]);

        $this->assertSame([], $service->validationErrors($certificat));
    }

    public function test_reutilitza_la_nota_existent_del_mateix_modul_grup(): void
    {
        $service = new ModuloOptatiuCertificatService();
        $certificat = ModulOptatiuCertificat::query()->create([
            'idModuloGrupo' => 1,
            'denominacio' => 'Optativa aplicada',
            'idProfesor' => 'P1',
        ]);
        DB::table('alumno_resultados')->insert([
            'idAlumno' => 'A1',
            'idModuloGrupo' => 1,
            'nota' => 6,
        ]);

        $data = $service->panelData($certificat);

        $this->assertSame(6, (int) $data['resultats']->get('A1')->nota);
    }

    public function test_indica_quins_certificats_individuals_es_poden_descarregar(): void
    {
        $service = new ModuloOptatiuCertificatService();
        $certificat = ModulOptatiuCertificat::query()->create([
            'idModuloGrupo' => 2,
            'denominacio' => 'Robòtica aplicada',
            'idProfesor' => 'P1',
        ]);
        DB::table('alumno_resultados')->insert([
            'idAlumno' => 'A1',
            'idModuloGrupo' => 2,
            'nota' => 6,
        ]);

        $data = $service->panelData($certificat);

        $this->assertTrue($data['pdfDisponibles']->get('A1'));
        $this->assertFalse($data['pdfDisponibles']->get('A2'));
        $this->assertFalse($data['potEmetre']);
    }

    public function test_no_cursa_no_bloqueja_el_grup_pero_no_te_pdf_individual(): void
    {
        $service = new ModuloOptatiuCertificatService();
        $certificat = ModulOptatiuCertificat::query()->create([
            'idModuloGrupo' => 2,
            'denominacio' => 'Robòtica aplicada',
            'idProfesor' => 'P1',
        ]);
        DB::table('alumno_resultados')->insert([
            [
                'idAlumno' => 'A1',
                'idModuloGrupo' => 2,
                'nota' => 7,
            ],
            [
                'idAlumno' => 'A2',
                'idModuloGrupo' => 2,
                'nota' => 12,
            ],
        ]);

        $data = $service->panelData($certificat);

        $this->assertSame([], $service->validationErrors($certificat));
        $this->assertTrue($data['pdfDisponibles']->get('A1'));
        $this->assertFalse($data['pdfDisponibles']->get('A2'));
        $this->assertTrue($data['potEmetre']);
        $this->assertSame(
            ['Beta D Dos figura com a No cursa.'],
            $service->validationErrorsForAlumno($certificat, Alumno::query()->findOrFail('A2'))
        );
    }

    public function test_no_supera_no_bloqueja_el_grup_pero_no_te_pdf_individual(): void
    {
        $service = new ModuloOptatiuCertificatService();
        $certificat = ModulOptatiuCertificat::query()->create([
            'idModuloGrupo' => 2,
            'denominacio' => 'Robòtica aplicada',
            'idProfesor' => 'P1',
        ]);
        DB::table('alumno_resultados')->insert([
            [
                'idAlumno' => 'A1',
                'idModuloGrupo' => 2,
                'nota' => 7,
            ],
            [
                'idAlumno' => 'A2',
                'idModuloGrupo' => 2,
                'nota' => 4,
            ],
        ]);

        $data = $service->panelData($certificat);

        $this->assertSame([], $service->validationErrors($certificat));
        $this->assertTrue($data['pdfDisponibles']->get('A1'));
        $this->assertFalse($data['pdfDisponibles']->get('A2'));
        $this->assertTrue($data['potEmetre']);
        $this->assertSame(
            ['Beta D Dos figura com a No supera.'],
            $service->validationErrorsForAlumno($certificat, Alumno::query()->findOrFail('A2'))
        );
    }

    public function test_no_mostra_emissio_si_tot_lalumnat_no_cursa(): void
    {
        $service = new ModuloOptatiuCertificatService();
        $certificat = ModulOptatiuCertificat::query()->create([
            'idModuloGrupo' => 2,
            'denominacio' => 'Robòtica aplicada',
            'idProfesor' => 'P1',
        ]);
        DB::table('alumno_resultados')->insert([
            [
                'idAlumno' => 'A1',
                'idModuloGrupo' => 2,
                'nota' => 12,
            ],
            [
                'idAlumno' => 'A2',
                'idModuloGrupo' => 2,
                'nota' => 12,
            ],
        ]);

        $data = $service->panelData($certificat);

        $this->assertFalse($data['pdfDisponibles']->contains(true));
        $this->assertFalse($data['potEmetre']);
    }

    public function test_resume_emissio_detecta_certificats_pendents_amb_nota_certificable(): void
    {
        $service = new ModuloOptatiuCertificatService();
        $certificat = ModulOptatiuCertificat::query()->create([
            'idModuloGrupo' => 2,
            'denominacio' => 'Robòtica aplicada',
            'idProfesor' => 'P1',
        ]);
        DB::table('alumno_resultados')->insert([
            [
                'idAlumno' => 'A1',
                'idModuloGrupo' => 2,
                'nota' => 7,
            ],
            [
                'idAlumno' => 'A2',
                'idModuloGrupo' => 2,
                'nota' => 8,
            ],
        ]);
        DB::table('modul_optatiu_certificat_alumnes')->insert([
            'idCertificat' => $certificat->id,
            'idAlumno' => 'A1',
            'enviat_at' => now(),
            'registrat_at' => now(),
            'fitxer' => 'tmp/a1.pdf',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $summary = $service->emissionSummary(Modulo_grupo::query()->findOrFail(2));

        $this->assertSame([
            'certificables' => 2,
            'emesos' => 1,
            'pendents' => 1,
            'complet' => false,
        ], $summary);
    }

    public function test_resume_emissio_no_es_complet_si_no_hi_ha_alumnat_certificable(): void
    {
        $service = new ModuloOptatiuCertificatService();
        ModulOptatiuCertificat::query()->create([
            'idModuloGrupo' => 2,
            'denominacio' => 'Robòtica aplicada',
            'idProfesor' => 'P1',
        ]);
        DB::table('alumno_resultados')->insert([
            [
                'idAlumno' => 'A1',
                'idModuloGrupo' => 2,
                'nota' => 12,
            ],
            [
                'idAlumno' => 'A2',
                'idModuloGrupo' => 2,
                'nota' => 4,
            ],
        ]);

        $summary = $service->emissionSummary(Modulo_grupo::query()->findOrFail(2));

        $this->assertSame([
            'certificables' => 0,
            'emesos' => 0,
            'pendents' => 0,
            'complet' => false,
        ], $summary);
    }

    public function test_resume_emissio_es_complet_quan_tots_els_certificables_estan_emesos(): void
    {
        $service = new ModuloOptatiuCertificatService();
        $certificat = ModulOptatiuCertificat::query()->create([
            'idModuloGrupo' => 2,
            'denominacio' => 'Robòtica aplicada',
            'idProfesor' => 'P1',
        ]);
        DB::table('alumno_resultados')->insert([
            [
                'idAlumno' => 'A1',
                'idModuloGrupo' => 2,
                'nota' => 7,
            ],
            [
                'idAlumno' => 'A2',
                'idModuloGrupo' => 2,
                'nota' => 4,
            ],
        ]);
        DB::table('modul_optatiu_certificat_alumnes')->insert([
            'idCertificat' => $certificat->id,
            'idAlumno' => 'A1',
            'enviat_at' => now(),
            'registrat_at' => now(),
            'fitxer' => 'tmp/a1.pdf',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $summary = $service->emissionSummary(Modulo_grupo::query()->findOrFail(2));

        $this->assertSame([
            'certificables' => 1,
            'emesos' => 1,
            'pendents' => 0,
            'complet' => true,
        ], $summary);
    }

    public function test_no_permet_descarregar_certificats_individuals_sense_denominacio_real(): void
    {
        $service = new ModuloOptatiuCertificatService();
        $certificat = ModulOptatiuCertificat::query()->create([
            'idModuloGrupo' => 2,
            'denominacio' => 'Mòdul optatiu',
            'idProfesor' => 'P1',
        ]);
        DB::table('alumno_resultados')->insert([
            'idAlumno' => 'A1',
            'idModuloGrupo' => 2,
            'nota' => 6,
        ]);

        $data = $service->panelData($certificat);

        $this->assertFalse($data['pdfDisponibles']->get('A1'));
    }

    public function test_genera_un_pdf_individual_sense_registrar_ni_enviar(): void
    {
        $this->bindProfesorServiceSenseCarrecs();
        $pdfService = new ModuloOptatiuPdfServiceFake();
        $secretariaService = new ModuloOptatiuSecretariaServiceFake();
        $service = new ModuloOptatiuCertificatService(null, $pdfService, $secretariaService);
        $certificat = ModulOptatiuCertificat::query()->create([
            'idModuloGrupo' => 2,
            'denominacio' => 'Robòtica aplicada',
            'idProfesor' => 'P1',
        ]);
        DB::table('alumno_resultados')->insert([
            'idAlumno' => 'A1',
            'idModuloGrupo' => 2,
            'nota' => 7,
        ]);

        $pdf = $service->pdf($certificat, Alumno::query()->findOrFail('A1'));

        $this->assertSame($pdfService->pdf, $pdf);
        $this->assertCount(1, $pdfService->calls);
        $this->assertSame('pdf.modulOptatiu.certificat', $pdfService->calls[0]['informe']);
        $this->assertSame('A1', $pdfService->calls[0]['todos']['alumne']->nia);
        $this->assertSame(7, (int) $pdfService->calls[0]['todos']['resultat']->nota);
        $this->assertSame(0, $secretariaService->uploads);
        $this->assertDatabaseMissing('modul_optatiu_certificat_alumnes', [
            'idCertificat' => $certificat->id,
            'idAlumno' => 'A1',
        ]);
    }

    public function test_emissio_de_grup_salta_alumnat_que_no_cursa(): void
    {
        Bus::fake();
        $this->bindProfesorServiceAmbSecretari();
        $pdfService = new ModuloOptatiuPdfServiceFake();
        $secretariaService = new ModuloOptatiuSecretariaServiceFake();
        $service = new ModuloOptatiuCertificatService(null, $pdfService, $secretariaService);
        $certificat = ModulOptatiuCertificat::query()->create([
            'idModuloGrupo' => 2,
            'denominacio' => 'Robòtica aplicada',
            'idProfesor' => 'P1',
        ]);
        DB::table('alumno_resultados')->insert([
            [
                'idAlumno' => 'A1',
                'idModuloGrupo' => 2,
                'nota' => 7,
            ],
            [
                'idAlumno' => 'A2',
                'idModuloGrupo' => 2,
                'nota' => 12,
            ],
        ]);

        $result = $service->emit($certificat);

        $this->assertSame(['sent' => 1, 'errors' => []], $result);
        $this->assertSame(1, $secretariaService->uploads);
        $this->assertCount(1, $pdfService->calls);
        $this->assertSame('A1', $pdfService->calls[0]['todos']['alumne']->nia);
        $this->assertDatabaseHas('modul_optatiu_certificat_alumnes', [
            'idCertificat' => $certificat->id,
            'idAlumno' => 'A1',
        ]);
        $this->assertDatabaseMissing('modul_optatiu_certificat_alumnes', [
            'idCertificat' => $certificat->id,
            'idAlumno' => 'A2',
        ]);
        Bus::assertDispatchedTimes(SendEmail::class, 1);
    }

    public function test_emissio_de_grup_salta_alumnat_que_no_supera(): void
    {
        Bus::fake();
        $this->bindProfesorServiceAmbSecretari();
        $pdfService = new ModuloOptatiuPdfServiceFake();
        $secretariaService = new ModuloOptatiuSecretariaServiceFake();
        $service = new ModuloOptatiuCertificatService(null, $pdfService, $secretariaService);
        $certificat = ModulOptatiuCertificat::query()->create([
            'idModuloGrupo' => 2,
            'denominacio' => 'Robòtica aplicada',
            'idProfesor' => 'P1',
        ]);
        DB::table('alumno_resultados')->insert([
            [
                'idAlumno' => 'A1',
                'idModuloGrupo' => 2,
                'nota' => 7,
            ],
            [
                'idAlumno' => 'A2',
                'idModuloGrupo' => 2,
                'nota' => 4,
            ],
        ]);

        $result = $service->emit($certificat);

        $this->assertSame(['sent' => 1, 'errors' => []], $result);
        $this->assertSame(1, $secretariaService->uploads);
        $this->assertCount(1, $pdfService->calls);
        $this->assertSame('A1', $pdfService->calls[0]['todos']['alumne']->nia);
        $this->assertDatabaseMissing('modul_optatiu_certificat_alumnes', [
            'idCertificat' => $certificat->id,
            'idAlumno' => 'A2',
        ]);
        Bus::assertDispatchedTimes(SendEmail::class, 1);
    }

    public function test_valida_un_alumne_abans_de_generar_el_pdf_individual(): void
    {
        $service = new ModuloOptatiuCertificatService();
        $certificat = ModulOptatiuCertificat::query()->create([
            'idModuloGrupo' => 2,
            'denominacio' => 'Robòtica aplicada',
            'idProfesor' => 'P1',
        ]);
        DB::table('alumno_resultados')->insert([
            'idAlumno' => 'A1',
            'idModuloGrupo' => 2,
            'nota' => 7,
        ]);

        $errorsAmbNota = $service->validationErrorsForAlumno($certificat, Alumno::query()->findOrFail('A1'));
        $errorsSenseNota = $service->validationErrorsForAlumno($certificat, Alumno::query()->findOrFail('A2'));

        $this->assertSame([], $errorsAmbNota);
        $this->assertCount(1, $errorsSenseNota);
        $this->assertStringContainsString('Falta la nota de Beta', $errorsSenseNota[0]);
    }

    private function bindProfesorServiceSenseCarrecs(): void
    {
        $this->app->instance(ProfesorService::class, new class extends ProfesorService {
            public function __construct()
            {
            }

            public function find(string $dni): ?Profesor
            {
                return null;
            }
        });
    }

    private function bindProfesorServiceAmbSecretari(): void
    {
        $secretari = new Profesor();
        $secretari->dni = 'S1';
        $secretari->nombre = 'SECRETARI';
        $secretari->apellido1 = 'BATOI';
        $secretari->apellido2 = '';
        $secretari->email = 'secretaria@example.test';

        $this->app->instance(ProfesorService::class, new class ($secretari) extends ProfesorService {
            public function __construct(private Profesor $secretari)
            {
            }

            public function find(string $dni): ?Profesor
            {
                return $this->secretari;
            }
        });
    }
}

/**
 * Doble de PDF per comprovar la generació sense renderitzar documents reals.
 */
class ModuloOptatiuPdfServiceFake extends PdfService
{
    public array $calls = [];

    public ModuloOptatiuGeneratedPdfFake $pdf;

    public function __construct()
    {
        $this->pdf = new ModuloOptatiuGeneratedPdfFake();
    }

    public function hazPdf(
        $informe,
        $todos,
        $datosInforme = null,
        $orientacion = 'portrait',
        $dimensiones = 'a4',
        $marginTop = 15,
        $driver = null
    ) {
        $this->calls[] = compact('informe', 'todos', 'datosInforme', 'orientacion', 'dimensiones', 'marginTop', 'driver');

        return $this->pdf;
    }
}

/**
 * PDF generat de prova.
 */
class ModuloOptatiuGeneratedPdfFake
{
    public array $savedPaths = [];

    public function save(string $path): void
    {
        file_put_contents($path, 'pdf');
        $this->savedPaths[] = $path;
    }

    public function stream(): string
    {
        return 'pdf';
    }
}

/**
 * Doble de Secretaria per garantir que el PDF individual no puja fitxers.
 */
class ModuloOptatiuSecretariaServiceFake extends SecretariaService
{
    public int $uploads = 0;

    public function __construct()
    {
    }

    public function uploadFile($document)
    {
        $this->uploads++;

        return 1;
    }
}
