<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Intranet\Application\Convalidacio\ConvalidacioException;
use Intranet\Application\Convalidacio\ConvalidacioAccessService;
use Intranet\Application\Convalidacio\ConvalidacioAutomaticaService;
use Intranet\Application\Convalidacio\ConvalidacioAnglesCorrespondenciesService;
use Intranet\Application\Convalidacio\ConvalidacioQueryService;
use Intranet\Application\Convalidacio\ConvalidacioReglesManager;
use Intranet\Application\Convalidacio\ConvalidacioService;
use Intranet\Application\Convalidacio\ResultatsAcademicsXmlService;
use Intranet\Entities\Alumno;
use Intranet\Entities\Convalidacio;
use Intranet\Entities\CorrespondenciaCicleAngles;
use Intranet\Entities\ModulFolLoe;
use Intranet\Entities\Profesor;
use Intranet\Entities\SollicitudConvalidacio;
use Intranet\Http\Middleware\RoleMiddleware;
use Intranet\Mail\ConvalidacioEstatActualitzat;
use Intranet\Policies\ConvalidacioPolicy;
use Tests\TestCase;

/** Regressió dels escenaris de l'MVP de convalidacions. */
class ConvalidacioFlowTest extends TestCase
{
    private string $sqlitePath;
    private Alumno $alumno;
    private ConvalidacioService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->sqlitePath = tempnam(storage_path(), 'convalidacions-test-');
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => $this->sqlitePath]);
        DB::setDefaultConnection('sqlite');
        DB::purge('sqlite');
        DB::reconnect('sqlite');
        Storage::fake('convalidacions');
        Storage::fake('convalidacions_xml');
        Storage::disk('convalidacions_xml')->put('avaluacio-2025.xml', $this->academicXml());
        $this->createSchema();
        $this->seedAcademicData();
        $this->alumno = Alumno::query()->findOrFail('12345678');
        $this->service = app(ConvalidacioService::class);
    }

    protected function tearDown(): void
    {
        DB::disconnect('sqlite');
        if (is_file($this->sqlitePath)) {
            unlink($this->sqlitePath);
        }
        parent::tearDown();
    }

    public function test_tramita_varies_peticions_amb_estat_per_item_i_es_idempotent(): void
    {
        $items = [
            ['modulo_destino_id' => 'DEST1', 'origen' => Convalidacio::ORIGEN_PROPI_CENTRE, 'resultat_origen_id' => $this->resultatId()],
            [
                'modulo_destino_id' => 'DEST2',
                'origen' => Convalidacio::ORIGEN_ALTRE_CENTRE,
                'declaracio_responsable' => true,
                'document' => UploadedFile::fake()->create('certificat.pdf', 100, 'application/pdf'),
            ],
        ];

        $primera = $this->service->tramitar($this->alumno, 'token-unic', $items);
        $segona = $this->service->tramitar($this->alumno, 'token-unic', $items);

        $this->assertSame($primera->id, $segona->id);
        $this->assertSame(1, SollicitudConvalidacio::query()->count());
        $this->assertSame(2, Convalidacio::query()->count());
        $this->assertSame([Convalidacio::ESTAT_ENVIADA], Convalidacio::query()->distinct()->pluck('estat')->all());
        $peticioPropiCentre = Convalidacio::query()->where('modulo_destino_id', 'DEST1')->firstOrFail();
        $this->assertSame('ORIG1', $peticioPropiCentre->modulo_origen_codigo);
        $this->assertSame('Origen 1 val', $peticioPropiCentre->modulo_origen_nombre);
        $this->assertSame('Origen 1 val', $peticioPropiCentre->modulo_origen_nombre_val);
        $this->assertSame('Origen 1 cas', $peticioPropiCentre->modulo_origen_nombre_cas);
        $this->assertSame('ANT', $peticioPropiCentre->ciclo_origen_codigo);
        $this->assertSame('Cicle anterior', $peticioPropiCentre->ciclo_origen_nombre_val);
        $this->assertSame('Ciclo anterior', $peticioPropiCentre->ciclo_origen_nombre_cas);
        $this->assertSame('FAMILIA', $peticioPropiCentre->familia_professional_codigo);
        $this->assertSame('Família professional', $peticioPropiCentre->familia_professional_nombre_val);
        $this->assertSame('Familia profesional', $peticioPropiCentre->familia_professional_nombre_cas);
        $this->assertSame(2, $peticioPropiCentre->ciclo_matricula_id);
        $this->assertSame('ACT', $peticioPropiCentre->ciclo_matricula_codigo);
        $this->assertSame('Cicle matriculat val', $peticioPropiCentre->ciclo_matricula_nombre_val);
        $this->assertSame('Ciclo matriculado cas', $peticioPropiCentre->ciclo_matricula_nombre_cas);
        $this->assertSame(24, $peticioPropiCentre->departamento_matricula_id);
        $this->assertSame('INFORMÀTICA I COMUNICACIONS', $peticioPropiCentre->familia_matricula_nombre_val);
        $this->assertSame('INFORMÁTICA Y COMUNICACIONES', $peticioPropiCentre->familia_matricula_nombre_cas);
        $this->assertSame('3306169525', $peticioPropiCentre->familia_matricula_codigo_xml);
        $this->assertSame('190', $peticioPropiCentre->familia_matricula_abreviatura_xml);
        $this->assertSame(2, $peticioPropiCentre->ciclo_matricula_tipo);
        $this->assertSame('Cicle Formatiu de Grau Superior', $peticioPropiCentre->ciclo_matricula_tipo_nombre_val);
        $this->assertSame('Ciclo Formativo de Grado Superior', $peticioPropiCentre->ciclo_matricula_tipo_nombre_cas);
        $this->assertSame('LFP', $peticioPropiCentre->ciclo_matricula_normativa);
        $this->assertSame('ANT', $peticioPropiCentre->nivel_origen_codigo);
        $this->assertSame('Cicle anterior', $peticioPropiCentre->nivel_origen_nombre_val);
        $this->assertSame('Ciclo anterior', $peticioPropiCentre->nivel_origen_nombre_cas);
        $this->assertSame(2025, $peticioPropiCentre->any_origen);
        $this->assertSame(7.0, $peticioPropiCentre->nota_origen);
        $this->assertSame('ordinària (FI)', $peticioPropiCentre->convocatoria_origen);
        $this->assertNull($peticioPropiCentre->fol_logse);
        $this->assertNull($peticioPropiCentre->modulo_origen_es_fol);
        $externa = Convalidacio::query()->where('origen', Convalidacio::ORIGEN_ALTRE_CENTRE)->firstOrFail();
        $this->assertSame(2, $externa->ciclo_matricula_id);
        $this->assertSame(24, $externa->departamento_matricula_id);
        $this->assertSame(2, $externa->ciclo_matricula_tipo);
        $this->assertNull($externa->nivel_origen_codigo);
        Storage::disk('convalidacions')->assertExists($externa->document_path);
        $this->assertSame('certificat.pdf', $externa->document_original_name);
        $this->assertTrue($externa->declaracio_responsable);
    }

    public function test_peticions_noves_comencen_enviades_i_no_canvien_els_estats_antics(): void
    {
        $primera = $this->service->tramitar($this->alumno, 'estat-inicial-antic', [[
            'modulo_destino_id' => 'DEST1',
            'origen' => Convalidacio::ORIGEN_PROPI_CENTRE,
            'resultat_origen_id' => $this->resultatId(),
        ]]);
        $peticioAntiga = $primera->convalidacions->firstOrFail();
        $peticioAntiga->forceFill(['estat' => Convalidacio::ESTAT_EN_PROCES])->save();

        $segona = $this->service->tramitar($this->alumno, 'estat-inicial-nou', [[
            'modulo_destino_id' => 'DEST2',
            'origen' => Convalidacio::ORIGEN_PROPI_CENTRE,
            'resultat_origen_id' => $this->resultatId(),
        ]]);

        $this->assertSame(Convalidacio::ESTAT_EN_PROCES, $peticioAntiga->fresh()->estat);
        $this->assertSame(Convalidacio::ESTAT_ENVIADA, $segona->convalidacions->firstOrFail()->estat);
    }

    public function test_migracio_normalitza_resolta_sense_esborrar_registres(): void
    {
        $sollicitud = $this->service->tramitar($this->alumno, 'migracio-estats', [
            ['modulo_destino_id' => 'DEST1', 'origen' => Convalidacio::ORIGEN_PROPI_CENTRE, 'resultat_origen_id' => $this->resultatId()],
            ['modulo_destino_id' => 'DEST2', 'origen' => Convalidacio::ORIGEN_PROPI_CENTRE, 'resultat_origen_id' => $this->resultatId()],
        ]);
        $sollicitud->convalidacions[0]->forceFill(['estat' => 'resolta'])->save();
        $sollicitud->convalidacions[1]->forceFill(['estat' => Convalidacio::ESTAT_EN_PROCES])->save();
        $sollicitudsAbans = SollicitudConvalidacio::query()->count();
        $peticionsAbans = Convalidacio::query()->count();

        $migration = require database_path('migrations/2026_10_13_160000_normalize_convalidacio_states.php');
        $migration->up();

        $this->assertSame('acceptada', $sollicitud->convalidacions[0]->fresh()->estat);
        $this->assertSame(Convalidacio::ESTAT_EN_PROCES, $sollicitud->convalidacions[1]->fresh()->estat);
        $this->assertSame($sollicitudsAbans, SollicitudConvalidacio::query()->count());
        $this->assertSame($peticionsAbans, Convalidacio::query()->count());
    }

    public function test_revisar_sollicitud_canvia_nomes_les_peticions_enviades(): void
    {
        $sollicitud = $this->service->tramitar($this->alumno, 'iniciar-revisio', [
            ['modulo_destino_id' => 'DEST1', 'origen' => Convalidacio::ORIGEN_PROPI_CENTRE, 'resultat_origen_id' => $this->resultatId()],
            ['modulo_destino_id' => 'DEST2', 'origen' => Convalidacio::ORIGEN_PROPI_CENTRE, 'resultat_origen_id' => $this->resultatId()],
        ]);
        [$enviada, $jaRevisada] = $sollicitud->convalidacions;
        $jaRevisada->forceFill([
            'estat' => Convalidacio::ESTAT_REVISAR_DOCUMENTACIO,
            'observacions' => 'Aportar el document complet.',
        ])->save();
        $director = Profesor::query()->findOrFail('DIR00001');

        $canviades = $this->service->iniciarRevisio($sollicitud, $director);

        $this->assertSame([$enviada->id], $canviades->pluck('id')->all());
        $this->assertSame(Convalidacio::ESTAT_EN_PROCES, $enviada->fresh()->estat);
        $this->assertSame(Convalidacio::ESTAT_REVISAR_DOCUMENTACIO, $jaRevisada->fresh()->estat);
        $this->assertSame('Aportar el document complet.', $jaRevisada->fresh()->observacions);
    }

    public function test_panell_de_direccio_filtra_i_inicia_revisio_de_sollicituds_pendents(): void
    {
        DB::table('alumnos')->where('nia', $this->alumno->nia)->update(['email' => 'alumne@example.test']);
        $sollicitud = $this->service->tramitar($this->alumno, 'panell-pendents', [
            ['modulo_destino_id' => 'DEST1', 'origen' => Convalidacio::ORIGEN_PROPI_CENTRE, 'resultat_origen_id' => $this->resultatId()],
            ['modulo_destino_id' => 'DEST2', 'origen' => Convalidacio::ORIGEN_PROPI_CENTRE, 'resultat_origen_id' => $this->resultatId()],
        ]);
        [$enviada, $jaRevisada] = $sollicitud->convalidacions;
        $jaRevisada->forceFill(['estat' => Convalidacio::ESTAT_DENEGADA, 'observacions' => 'No correspon.'])->save();
        $director = Profesor::query()->findOrFail('DIR00001');
        Mail::fake();
        $this->withoutMiddleware(RoleMiddleware::class)->actingAs($director, 'profesor');

        $this->get(route('convalidacions.direction.index', ['pendent_direccio' => 1]))
            ->assertOk()
            ->assertSee('Només amb acció pendent de Direcció')
            ->assertSee('onchange="this.form.submit()"', false)
            ->assertDontSee('>Filtrar</button>', false)
            ->assertSee('1 mòdul')
            ->assertSee('aria-label="1 mòdul amb acció pendent de Direcció: 1 Enviada."', false)
            ->assertSee(route('convalidacions.direction.review', $sollicitud));

        $this->post(route('convalidacions.direction.review', $sollicitud))
            ->assertRedirect(route('convalidacions.direction.show', $sollicitud))
            ->assertSessionHas('success', '1 mòdul ha passat a En procés. S’ha notificat l’alumnat.');

        $this->get(route('convalidacions.direction.index'))
            ->assertOk()
            ->assertSee(
                'class="btn btn-primary btn-sm" href="' . route('convalidacions.direction.show', $sollicitud) . '"',
                false
            );

        $this->assertSame(Convalidacio::ESTAT_EN_PROCES, $enviada->fresh()->estat);
        $this->assertSame(Convalidacio::ESTAT_DENEGADA, $jaRevisada->fresh()->estat);
        Mail::assertSentTimes(ConvalidacioEstatActualitzat::class, 1);
    }

    /** Verifica la separació per sol·licitud, inclosos els estats mixtos i els recomptes. */
    public function test_panell_de_direccio_separa_sollicituds_en_seguiment_i_resoltes(): void
    {
        $crearSollicitud = function (string $token, array $estats): int {
            $ara = now();
            $sollicitudId = DB::table('sollicituds_convalidacions')->insertGetId([
                'alumno_id' => $this->alumno->nia,
                'submission_token' => $token,
                'submitted_at' => $ara,
                'created_at' => $ara,
                'updated_at' => $ara,
            ]);

            foreach ($estats as $index => $estat) {
                DB::table('convalidacions')->insert([
                    'sollicitud_convalidacio_id' => $sollicitudId,
                    'modulo_destino_id' => 'DEST' . ($index + 1),
                    'origen' => Convalidacio::ORIGEN_PROPI_CENTRE,
                    'estat' => $estat,
                    'created_at' => $ara,
                    'updated_at' => $ara,
                ]);
            }

            return $sollicitudId;
        };

        $mixta = $crearSollicitud('pestanya-mixta', [
            Convalidacio::ESTAT_ENVIADA,
            Convalidacio::ESTAT_DENEGADA,
        ]);
        $acceptada = $crearSollicitud('pestanya-acceptada', [Convalidacio::ESTAT_ACCEPTADA]);
        $denegada = $crearSollicitud('pestanya-denegada', [Convalidacio::ESTAT_DENEGADA]);
        $revisarDocumentacio = $crearSollicitud('pestanya-documentacio', [Convalidacio::ESTAT_REVISAR_DOCUMENTACIO]);
        $aportarOriginal = $crearSollicitud('pestanya-original', [Convalidacio::ESTAT_APORTAR_ORIGINAL]);

        $director = Profesor::query()->findOrFail('DIR00001');
        $this->withoutMiddleware(RoleMiddleware::class)->actingAs($director, 'profesor');

        $response = $this->get(route('convalidacions.direction.index'));
        $response
            ->assertOk()
            ->assertSee('En seguiment')
            ->assertSee('Resoltes')
            ->assertSee('<span class="badge">3</span>', false)
            ->assertSee('>#' . $mixta . '</td>', false)
            ->assertSee('>#' . $revisarDocumentacio . '</td>', false)
            ->assertSee('>#' . $aportarOriginal . '</td>', false)
            ->assertDontSee('>#' . $acceptada . '</td>', false)
            ->assertDontSee('>#' . $denegada . '</td>', false);
        $html = $response->getContent();
        $this->assertLessThan(strpos($html, 'id="automatic-action-heading"'), strpos($html, 'aria-label="Pestanyes de sol·licituds"'));
        $this->assertSame(1, preg_match('/<a[^>]*>\s*Resoltes\s*<\/a>/s', $html));

        $this->get(route('convalidacions.direction.index', [
            'pestanya' => 'seguiment',
            'pendent_direccio' => 1,
        ]))
            ->assertOk()
            ->assertSee('Només amb acció pendent de Direcció')
            ->assertSee('>#' . $mixta . '</td>', false)
            ->assertDontSee('>#' . $revisarDocumentacio . '</td>', false)
            ->assertDontSee('>#' . $aportarOriginal . '</td>', false);

        $response = $this->get(route('convalidacions.direction.index', ['pestanya' => 'resoltes']));
        $response
            ->assertOk()
            ->assertSee('aria-current="page"', false)
            ->assertDontSee('automatic-action-heading')
            ->assertDontSee('<th>Automatització</th>', false)
            ->assertSee('>#' . $acceptada . '</td>', false)
            ->assertSee('>#' . $denegada . '</td>', false)
            ->assertSee('>Veure</a>', false)
            ->assertDontSee('>#' . $mixta . '</td>', false);
        $this->assertSame(1, preg_match('/<a[^>]*aria-current="page"[^>]*>\s*Resoltes\s*<\/a>/s', $response->getContent()));
    }

    public function test_rebutja_destins_duplicats_ids_manipulats_i_externs_sense_document(): void
    {
        foreach ([
            [
                ['modulo_destino_id' => 'DEST1', 'origen' => Convalidacio::ORIGEN_PROPI_CENTRE, 'resultat_origen_id' => $this->resultatId()],
                ['modulo_destino_id' => 'DEST1', 'origen' => Convalidacio::ORIGEN_PROPI_CENTRE, 'resultat_origen_id' => $this->resultatId()],
            ],
            [['modulo_destino_id' => 'ALIEN', 'origen' => Convalidacio::ORIGEN_PROPI_CENTRE, 'resultat_origen_id' => $this->resultatId()]],
            [['modulo_destino_id' => 'DEST1', 'origen' => Convalidacio::ORIGEN_PROPI_CENTRE, 'resultat_origen_id' => str_repeat('a', 64)]],
            [['modulo_destino_id' => 'DEST1', 'origen' => Convalidacio::ORIGEN_ALTRE_CENTRE, 'declaracio_responsable' => true]],
        ] as $index => $items) {
            try {
                $this->service->tramitar($this->alumno, 'invalid-' . $index, $items);
                $this->fail('La composició invàlida havia de ser rebutjada.');
            } catch (ConvalidacioException) {
                $this->assertSame(0, SollicitudConvalidacio::query()->count());
            }
        }
    }

    public function test_fol_logse_del_propi_centre_exigix_documentacio_sense_validar_ne_el_tipus(): void
    {
        DB::table('modulos')->insert(['codigo' => '1709', 'cliteral' => 'IPE I', 'vliteral' => 'IPE I']);
        DB::table('modulo_ciclos')->insert(['id' => 4, 'idModulo' => '1709', 'idCiclo' => 2]);
        DB::table('modulo_grupos')->insert(['id' => 4, 'idGrupo' => 'ACTUAL', 'idModuloCiclo' => 4]);
        $xml = str_replace('ORIG1', '009001', $this->academicXml());
        Storage::disk('convalidacions_xml')->put('avaluacio-2025.xml', $xml);

        try {
            $this->service->tramitar($this->alumno, 'fol-logse-sense-prl', [[
                'modulo_destino_id' => '1709',
                'origen' => Convalidacio::ORIGEN_PROPI_CENTRE,
                'resultat_origen_id' => $this->resultatId(),
                'fol_logse' => true,
            ]]);
            $this->fail('La petició sense documentació havia de ser rebutjada.');
        } catch (ConvalidacioException $exception) {
            $this->assertStringContainsString('certificat', $exception->getMessage());
            $this->assertSame(0, SollicitudConvalidacio::query()->count());
        }

        $sollicitud = $this->service->tramitar($this->alumno, 'fol-logse-amb-prl', [[
            'modulo_destino_id' => '1709',
            'origen' => Convalidacio::ORIGEN_PROPI_CENTRE,
            'resultat_origen_id' => $this->resultatId(),
            'fol_logse' => true,
            'declaracio_responsable' => true,
            'documents' => [[
                'descripcio' => 'Documentació aportada',
                'fitxer' => UploadedFile::fake()->create('certificat-prl.pdf', 80, 'application/pdf'),
            ]],
        ]]);
        $peticio = $sollicitud->convalidacions->firstOrFail();

        $this->assertTrue($peticio->fol_logse);
        $peticio->load('documents');
        $this->assertSame('certificat-prl.pdf', $peticio->documents->firstOrFail()->original_name);
        $this->assertSame($peticio->documents->firstOrFail()->path, $peticio->document_path);
        $this->assertNull($peticio->document_prl_path);
        Storage::disk('convalidacions')->assertExists($peticio->documents->firstOrFail()->path);
    }

    public function test_un_altre_centre_pot_aportar_fins_a_tres_documents_descrits(): void
    {
        $documents = [];
        foreach ([['certificat.pdf', 'Certificat acadèmic'], ['prl.pdf', 'Certificat PRL'], ['notes.png', 'Certificat de notes']] as [$nom, $descripcio]) {
            $documents[] = ['descripcio' => $descripcio, 'fitxer' => UploadedFile::fake()->create($nom, 40, 'application/pdf')];
        }
        $sollicitud = $this->service->tramitar($this->alumno, 'three-docs', [[
            'modulo_destino_id' => 'DEST1',
            'origen' => Convalidacio::ORIGEN_ALTRE_CENTRE,
            'declaracio_responsable' => true,
            'fol_logse' => false,
            'documents' => $documents,
        ]]);

        $peticio = $sollicitud->convalidacions->firstOrFail()->load('documents');
        $this->assertTrue($peticio->declaracio_responsable);
        $this->assertCount(3, $peticio->documents);
        $this->assertSame(['Certificat acadèmic', 'Certificat PRL', 'Certificat de notes'], $peticio->documents->pluck('descripcio')->all());
        foreach ($peticio->documents as $document) {
            Storage::disk('convalidacions')->assertExists($document->path);
        }

        $firstDocument = $peticio->documents->firstOrFail();
        $this->withoutMiddleware(RoleMiddleware::class)->actingAs($this->alumno, 'alumno');
        auth()->shouldUse('profesor');
        $this->get(route('convalidacions.download-attachment', [$peticio, $firstDocument]))->assertOk();
        $this->actingAs(Alumno::query()->findOrFail('87654321'), 'alumno');
        $this->get(route('convalidacions.download-attachment', [$peticio, $firstDocument]))->assertForbidden();
    }

    public function test_rebutja_mes_de_tres_documents_en_una_peticio(): void
    {
        $documents = [];
        for ($index = 1; $index <= 4; $index++) {
            $documents[] = [
                'descripcio' => 'Certificat acadèmic ' . $index,
                'fitxer' => UploadedFile::fake()->create('document-' . $index . '.pdf', 10, 'application/pdf'),
            ];
        }

        try {
            $this->service->tramitar($this->alumno, 'four-docs', [[
                'modulo_destino_id' => 'DEST1',
                'origen' => Convalidacio::ORIGEN_ALTRE_CENTRE,
                'declaracio_responsable' => true,
                'fol_logse' => false,
                'documents' => $documents,
            ]]);
            $this->fail('No s’havien d’acceptar més de tres adjunts.');
        } catch (ConvalidacioException $exception) {
            $this->assertStringContainsString('màxim tres', $exception->getMessage());
        }
        $this->assertSame(0, SollicitudConvalidacio::query()->count());
    }

    public function test_fol_logse_extern_per_a_ipe_i_no_interpreta_ni_exigix_documents_separats(): void
    {
        DB::table('modulos')->insert(['codigo' => '1709', 'cliteral' => 'IPE I', 'vliteral' => 'IPE I']);
        DB::table('modulo_ciclos')->insert(['id' => 4, 'idModulo' => '1709', 'idCiclo' => 2]);
        DB::table('modulo_grupos')->insert(['id' => 4, 'idGrupo' => 'ACTUAL', 'idModuloCiclo' => 4]);
        $document = static fn (string $name, string $label): array => [
            'descripcio' => $label,
            'fitxer' => UploadedFile::fake()->create($name, 50, 'application/pdf'),
        ];

        $sollicitud = $this->service->tramitar($this->alumno, 'external-fol-logse', [[
            'modulo_destino_id' => '1709',
            'origen' => Convalidacio::ORIGEN_ALTRE_CENTRE,
            'declaracio_responsable' => true,
            'modulo_origen_es_fol' => true,
            'fol_logse' => true,
            'documents' => [
                $document('expedient.pdf', 'Carpeta de papers'),
            ],
        ]]);

        $peticio = $sollicitud->convalidacions->firstOrFail()->load('documents');
        $this->assertTrue($peticio->modulo_origen_es_fol);
        $this->assertTrue($peticio->fol_logse);
        $this->assertSame(['Carpeta de papers'], $peticio->documents->pluck('descripcio')->all());
    }

    public function test_no_tramita_si_no_pot_resoldre_la_familia_professional(): void
    {
        Storage::disk('convalidacions_xml')->put(
            'avaluacio-2025.xml',
            str_replace('codigo="FAMILIA" padre=" "', 'codigo="FAMILIA" padre="INEXISTENT"', $this->academicXml())
        );

        try {
            $this->service->tramitar($this->alumno, 'sense-familia', [[
                'modulo_destino_id' => 'DEST1',
                'origen' => Convalidacio::ORIGEN_PROPI_CENTRE,
                'resultat_origen_id' => $this->resultatId(),
            ]]);
            $this->fail('La petició havia de rebutjar-se si la família professional no és identificable.');
        } catch (ConvalidacioException $exception) {
            $this->assertStringContainsString('família professional', $exception->getMessage());
        }

        $this->assertSame(0, SollicitudConvalidacio::query()->count());
        $this->assertSame(0, Convalidacio::query()->count());
    }

    public function test_rebutja_cicle_ambigu_o_familia_de_matricula_no_configurada(): void
    {
        DB::table('modulo_ciclos')->insert(['id' => 4, 'idModulo' => 'DEST1', 'idCiclo' => 1]);
        DB::table('modulo_grupos')->insert(['id' => 4, 'idGrupo' => 'ACTUAL', 'idModuloCiclo' => 4]);

        try {
            $this->service->tramitar($this->alumno, 'cicle-ambigu', [[
                'modulo_destino_id' => 'DEST1',
                'origen' => Convalidacio::ORIGEN_PROPI_CENTRE,
                'resultat_origen_id' => $this->resultatId(),
            ]]);
            $this->fail('No s\'havia d\'escollir aleatòriament entre dos cicles.');
        } catch (ConvalidacioException $exception) {
            $this->assertStringContainsString('únic cicle de matrícula', $exception->getMessage());
        }

        $this->assertSame(0, SollicitudConvalidacio::query()->count());

        DB::table('modulo_grupos')->where('id', 4)->delete();
        DB::table('modulo_ciclos')->where('id', 4)->delete();
        DB::table('departamentos')->where('id', 24)->update(['familia_professional_val' => null]);

        try {
            $this->service->tramitar($this->alumno, 'familia-no-configurada', [[
                'modulo_destino_id' => 'DEST1',
                'origen' => Convalidacio::ORIGEN_PROPI_CENTRE,
                'resultat_origen_id' => $this->resultatId(),
            ]]);
            $this->fail('No s\'havia d\'acceptar una família de matrícula sense configurar.');
        } catch (ConvalidacioException $exception) {
            $this->assertStringContainsString('família professional del cicle de matrícula', $exception->getMessage());
        }

        $this->assertSame(0, SollicitudConvalidacio::query()->count());
        $this->assertSame(0, Convalidacio::query()->count());
    }

    public function test_direccio_revisa_una_peticio_sense_alterar_les_altres_i_acceptada_itaca_es_terminal(): void
    {
        $sollicitud = $this->service->tramitar($this->alumno, 'review', [
            ['modulo_destino_id' => 'DEST1', 'origen' => Convalidacio::ORIGEN_PROPI_CENTRE, 'resultat_origen_id' => $this->resultatId()],
            ['modulo_destino_id' => 'DEST2', 'origen' => Convalidacio::ORIGEN_PROPI_CENTRE, 'resultat_origen_id' => $this->resultatId()],
        ]);
        $direccio = Profesor::query()->findOrFail('DIR00001');
        $primera = $sollicitud->convalidacions[0];
        $segona = $sollicitud->convalidacions[1];

        $this->expectException(ConvalidacioException::class);
        try {
            $this->service->revisar($primera, $direccio, Convalidacio::ESTAT_DENEGADA, null);
        } finally {
            $this->service->revisar($primera, $direccio, Convalidacio::ESTAT_ACCEPTADA, null, 'AA', 'none');
            $this->service->registrarAcceptades([$primera->id], $direccio);
            $this->assertSame(Convalidacio::ESTAT_ENVIADA, $segona->fresh()->estat);
            $this->assertSame('DIR00001', $primera->fresh()->revisat_per);
            try {
                $this->service->revisar($primera->fresh(), $direccio, Convalidacio::ESTAT_DENEGADA, 'Canvi');
                $this->fail('Una petició terminal no ha de poder canviar.');
            } catch (ConvalidacioException) {
                $this->assertTrue(true);
            }
        }
    }

    public function test_direccio_notifica_per_correu_cada_canvi_efectiu_de_modul_sense_afectar_els_altres(): void
    {
        DB::table('alumnos')->where('nia', $this->alumno->nia)->update(['email' => 'alumne@example.test']);
        $sollicitud = $this->service->tramitar($this->alumno, 'notificacions-per-modul', [
            ['modulo_destino_id' => 'DEST1', 'origen' => Convalidacio::ORIGEN_PROPI_CENTRE, 'resultat_origen_id' => $this->resultatId()],
            ['modulo_destino_id' => 'DEST2', 'origen' => Convalidacio::ORIGEN_PROPI_CENTRE, 'resultat_origen_id' => $this->resultatId()],
        ]);
        $director = Profesor::query()->findOrFail('DIR00001');
        $primera = $sollicitud->convalidacions[0];
        $segona = $sollicitud->convalidacions[1];
        Mail::fake();
        $this->withoutMiddleware(RoleMiddleware::class)->actingAs($director, 'profesor');

        $this->from(route('convalidacions.direction.show', $sollicitud))
            ->put(route('convalidacions.direction.resolve', $primera), [
                'estat' => Convalidacio::ESTAT_DENEGADA,
                'observacions' => 'Falta documentació acadèmica.',
            ])
            ->assertRedirect(route('convalidacions.direction.show', $sollicitud))
            ->assertSessionHas('success', 'Petició actualitzada i correu enviat a l’alumne.');

        $this->assertSame(Convalidacio::ESTAT_DENEGADA, $primera->fresh()->estat);
        $this->assertSame(Convalidacio::ESTAT_ENVIADA, $segona->fresh()->estat);
        Mail::assertSent(ConvalidacioEstatActualitzat::class, function (ConvalidacioEstatActualitzat $mail): bool {
            $cos = $mail->render();

            return $mail->hasTo('alumne@example.test')
                && $mail->modul === 'Destí 1'
                && $mail->estat === 'Denegada'
                && $mail->observacions === 'Falta documentació acadèmica.'
                && str_contains($cos, 'Destí 1')
                && str_contains($cos, 'Falta documentació acadèmica.')
                && !str_contains($cos, 'Destí 2');
        });

        $this->from(route('convalidacions.direction.show', $sollicitud))
            ->put(route('convalidacions.direction.resolve', $segona), [
                'estat' => Convalidacio::ESTAT_ACCEPTADA,
                'observacions' => '',
                'resultat_resolucio' => 'AA',
                'mode_nota_resolucio' => 'none',
            ])
            ->assertSessionHas('success', 'Petició actualitzada i correu enviat a l’alumne.');
        Mail::assertSent(ConvalidacioEstatActualitzat::class, 'alumne@example.test');
        Mail::assertSentTimes(ConvalidacioEstatActualitzat::class, 2);

        $this->from(route('convalidacions.direction.show', $sollicitud))
            ->put(route('convalidacions.direction.resolve', $primera), [
                'estat' => Convalidacio::ESTAT_DENEGADA,
                'observacions' => 'Falta documentació acadèmica.',
            ])
            ->assertSessionHas('success', 'La petició no ha canviat; no s’ha enviat cap correu.');
        Mail::assertSentTimes(ConvalidacioEstatActualitzat::class, 2);
    }

    public function test_direccio_pot_assignar_els_nous_estats_a_moduls_individuals(): void
    {
        DB::table('alumnos')->where('nia', $this->alumno->nia)->update(['email' => 'alumne@example.test']);
        $sollicitud = $this->service->tramitar($this->alumno, 'nous-estats-per-modul', [
            ['modulo_destino_id' => 'DEST1', 'origen' => Convalidacio::ORIGEN_PROPI_CENTRE, 'resultat_origen_id' => $this->resultatId()],
            ['modulo_destino_id' => 'DEST2', 'origen' => Convalidacio::ORIGEN_PROPI_CENTRE, 'resultat_origen_id' => $this->resultatId()],
        ]);
        [$primera, $segona] = $sollicitud->convalidacions;
        $director = Profesor::query()->findOrFail('DIR00001');
        Mail::fake();
        $this->withoutMiddleware(RoleMiddleware::class)->actingAs($director, 'profesor');

        $this->get(route('convalidacions.direction.show', $sollicitud))
            ->assertOk()
            ->assertSee('Enviada')
            ->assertDontSee('Acceptada (Convalidada)');

        $this->put(route('convalidacions.direction.resolve', $primera), [
            'estat' => Convalidacio::ESTAT_ACCEPTADA,
            'observacions' => '',
            'resultat_resolucio' => 'CO',
            'mode_nota_resolucio' => 'preserve',
            'nota_resultat_resolucio' => 7,
        ])->assertRedirect();

        $this->from(route('convalidacions.direction.show', $sollicitud))
            ->put(route('convalidacions.direction.resolve', $segona), [
                'estat' => Convalidacio::ESTAT_ACCEPTADA_CONVALIDADA,
                'observacions' => '',
            ])
            ->assertSessionHasErrors('estat');
        $this->assertSame(Convalidacio::ESTAT_ENVIADA, $segona->fresh()->estat);

        $this->put(route('convalidacions.direction.resolve', $segona), [
            'estat' => Convalidacio::ESTAT_ACCEPTADA,
            'observacions' => '',
            'resultat_resolucio' => 'AA',
            'mode_nota_resolucio' => 'none',
        ])->assertRedirect();
        $this->assertSame(Convalidacio::ESTAT_ACCEPTADA, $segona->fresh()->estat);
        $this->service->registrarAcceptades([$segona->id], $director);

        $this->assertSame(Convalidacio::ESTAT_ACCEPTADA, $primera->fresh()->estat);
        $this->assertSame('CO', $primera->fresh()->resultat_resolucio);
        $this->assertSame('preserve', $primera->fresh()->mode_nota_resolucio);
        $this->assertSame(7.0, $primera->fresh()->nota_resultat_resolucio);
        $this->assertSame(Convalidacio::ESTAT_ACCEPTADA_CONVALIDADA, $segona->fresh()->estat);
        $this->assertTrue($segona->fresh()->esTerminal());
        Mail::assertSentTimes(ConvalidacioEstatActualitzat::class, 2);
    }

    public function test_direccio_pot_marcar_individualment_com_a_convalidada_una_acceptada_manual_o_automatica(): void
    {
        DB::table('alumnos')->where('nia', $this->alumno->nia)->update(['email' => 'alumne@example.test']);
        $sollicitud = $this->service->tramitar($this->alumno, 'convalidar-individualment', [
            ['modulo_destino_id' => 'DEST1', 'origen' => Convalidacio::ORIGEN_PROPI_CENTRE, 'resultat_origen_id' => $this->resultatId()],
            ['modulo_destino_id' => 'DEST2', 'origen' => Convalidacio::ORIGEN_PROPI_CENTRE, 'resultat_origen_id' => $this->resultatId()],
        ]);
        [$manual, $automatica] = $sollicitud->convalidacions;
        $director = Profesor::query()->findOrFail('DIR00001');
        Mail::fake();
        $this->withoutMiddleware(RoleMiddleware::class)->actingAs($director, 'profesor');

        $this->get(route('convalidacions.direction.show', $sollicitud))
            ->assertOk()
            ->assertDontSee('Acceptada (Convalidada)');

        $this->put(route('convalidacions.direction.resolve', $manual), [
            'estat' => Convalidacio::ESTAT_ACCEPTADA,
            'observacions' => '',
            'resultat_resolucio' => 'CO',
            'mode_nota_resolucio' => 'preserve',
            'nota_resultat_resolucio' => 8,
        ])->assertRedirect();

        $this->get(route('convalidacions.direction.show', $sollicitud))
            ->assertOk()
            ->assertSee('Acceptada (Convalidada)');

        $this->put(route('convalidacions.direction.resolve', $manual), [
            'estat' => Convalidacio::ESTAT_ACCEPTADA_CONVALIDADA,
            'observacions' => '',
        ])->assertRedirect();

        $automatica->forceFill([
            'estat' => Convalidacio::ESTAT_ACCEPTADA,
            'resultat_automatic' => 'AA',
            'mode_nota_automatic' => 'preserve',
            'nota_resultat_automatic' => 9,
            'resultat_resolucio' => 'AA',
            'mode_nota_resolucio' => 'preserve',
            'nota_resultat_resolucio' => 9,
        ])->save();

        $this->put(route('convalidacions.direction.resolve', $automatica), [
            'estat' => Convalidacio::ESTAT_ACCEPTADA_CONVALIDADA,
            'observacions' => '',
        ])->assertRedirect();

        $this->assertDatabaseHas('convalidacions', [
            'id' => $manual->id,
            'estat' => Convalidacio::ESTAT_ACCEPTADA_CONVALIDADA,
            'resultat_resolucio' => 'CO',
            'mode_nota_resolucio' => 'preserve',
            'nota_resultat_resolucio' => 8,
        ], 'sqlite');
        $this->assertDatabaseHas('convalidacions', [
            'id' => $automatica->id,
            'estat' => Convalidacio::ESTAT_ACCEPTADA_CONVALIDADA,
            'resultat_resolucio' => 'AA',
            'mode_nota_resolucio' => 'preserve',
            'nota_resultat_resolucio' => 9,
        ], 'sqlite');
        Mail::assertSentTimes(ConvalidacioEstatActualitzat::class, 3);
    }

    public function test_resolucio_manual_acceptada_exigix_resultat_i_guarda_si_conserva_nota(): void
    {
        DB::table('alumnos')->where('nia', $this->alumno->nia)->update(['email' => 'alumne@example.test']);
        $sollicitud = $this->service->tramitar($this->alumno, 'resolucio-manual', [[
            'modulo_destino_id' => 'DEST1',
            'origen' => Convalidacio::ORIGEN_PROPI_CENTRE,
            'resultat_origen_id' => $this->resultatId(),
        ]]);
        $peticio = $sollicitud->convalidacions->firstOrFail();
        $this->withoutMiddleware(RoleMiddleware::class)
            ->actingAs(Profesor::query()->findOrFail('DIR00001'), 'profesor');

        $this->from(route('convalidacions.direction.show', $sollicitud))
            ->put(route('convalidacions.direction.resolve', $peticio), [
                'estat' => Convalidacio::ESTAT_ACCEPTADA,
                'observacions' => '',
            ])
            ->assertSessionHasErrors(['resultat_resolucio', 'mode_nota_resolucio']);
        $this->assertSame(Convalidacio::ESTAT_ENVIADA, $peticio->fresh()->estat);

        Mail::fake();
        $this->put(route('convalidacions.direction.resolve', $peticio), [
            'estat' => Convalidacio::ESTAT_ACCEPTADA,
            'observacions' => '',
            'resultat_resolucio' => 'CO',
            'mode_nota_resolucio' => 'preserve',
            'nota_resultat_resolucio' => 7,
        ])->assertRedirect();

        $this->assertDatabaseHas('convalidacions', [
            'id' => $peticio->id,
            'estat' => Convalidacio::ESTAT_ACCEPTADA,
            'resultat_resolucio' => 'CO',
            'mode_nota_resolucio' => 'preserve',
            'nota_resultat_resolucio' => 7,
        ], 'sqlite');
        $this->get(route('convalidacions.direction.show', $sollicitud))
            ->assertOk()
            ->assertSee('Resolució:')
            ->assertSee('CO')
            ->assertSee('Nota conservada:')
            ->assertSee('7');
        $this->get(route('convalidacions.direction.modules.index', ['estat' => Convalidacio::ESTAT_ACCEPTADA]))
            ->assertOk()
            ->assertSee('Resultat CO')
            ->assertSee('Nota 7')
            ->assertDontSee('🤖');
        Mail::assertSentTimes(ConvalidacioEstatActualitzat::class, 1);
    }

    public function test_resolucio_manual_permet_transcriure_la_nota_d_un_certificat_extern(): void
    {
        $sollicitud = $this->service->tramitar($this->alumno, 'resolucio-sense-nota', [[
            'modulo_destino_id' => 'DEST1',
            'origen' => Convalidacio::ORIGEN_ALTRE_CENTRE,
            'declaracio_responsable' => true,
            'document' => UploadedFile::fake()->create('certificat-notes.pdf', 50, 'application/pdf'),
        ]]);
        $peticio = $sollicitud->convalidacions->firstOrFail();
        $this->assertNull($peticio->nota_origen);
        $this->withoutMiddleware(RoleMiddleware::class)
            ->actingAs(Profesor::query()->findOrFail('DIR00001'), 'profesor');

        $this->from(route('convalidacions.direction.show', $sollicitud))
            ->put(route('convalidacions.direction.resolve', $peticio), [
                'estat' => Convalidacio::ESTAT_ACCEPTADA,
                'observacions' => '',
                'resultat_resolucio' => 'AA',
                'mode_nota_resolucio' => 'preserve',
                'nota_resultat_resolucio' => 4,
            ])
            ->assertSessionHasErrors('nota_resultat_resolucio');

        $this->assertSame(Convalidacio::ESTAT_ENVIADA, $peticio->fresh()->estat);
        $this->assertNull($peticio->fresh()->resultat_resolucio);

        $this->get(route('convalidacions.direction.show', $sollicitud))
            ->assertOk()
            ->assertSee('Conservar la nota que consta en la documentació')
            ->assertSee('option value="preserve"', false)
            ->assertSee('min="5" max="10" step="1"', false)
            ->assertSee('name="nota_resultat_resolucio"', false);

        $this->put(route('convalidacions.direction.resolve', $peticio), [
            'estat' => Convalidacio::ESTAT_ACCEPTADA,
            'observacions' => '',
            'resultat_resolucio' => 'AA',
            'mode_nota_resolucio' => 'preserve',
            'nota_resultat_resolucio' => 8,
        ])->assertRedirect();

        $this->assertSame(Convalidacio::ESTAT_ACCEPTADA, $peticio->fresh()->estat);
        $this->assertSame(8.0, $peticio->fresh()->nota_resultat_resolucio);
    }

    public function test_canvi_de_peticio_es_guarda_si_no_hi_ha_correu_valid(): void
    {
        $sollicitud = $this->service->tramitar($this->alumno, 'sense-correu', [[
            'modulo_destino_id' => 'DEST1',
            'origen' => Convalidacio::ORIGEN_PROPI_CENTRE,
            'resultat_origen_id' => $this->resultatId(),
        ]]);
        $peticio = $sollicitud->convalidacions->firstOrFail();
        Mail::fake();
        $this->withoutMiddleware(RoleMiddleware::class)->actingAs(Profesor::query()->findOrFail('DIR00001'), 'profesor');

        $this->from(route('convalidacions.direction.show', $sollicitud))
            ->put(route('convalidacions.direction.resolve', $peticio), [
                'estat' => 'estat_inexistent',
                'observacions' => '',
            ])
            ->assertSessionHasErrors('estat');
        Mail::assertNothingSent();

        $this->from(route('convalidacions.direction.show', $sollicitud))
            ->put(route('convalidacions.direction.resolve', $peticio), [
                'estat' => Convalidacio::ESTAT_ACCEPTADA,
                'observacions' => '',
                'resultat_resolucio' => 'AA',
                'mode_nota_resolucio' => 'none',
            ])
            ->assertSessionHas('warning', 'La petició s’ha actualitzat, però l’alumne no té una adreça de correu vàlida. No s’ha enviat cap notificació.');

        $this->assertSame(Convalidacio::ESTAT_ACCEPTADA, $peticio->fresh()->estat);
        Mail::assertNothingSent();
    }

    public function test_fallada_de_correu_no_reverteix_la_resolucio_i_avisa_direccio(): void
    {
        DB::table('alumnos')->where('nia', $this->alumno->nia)->update(['email' => 'alumne@example.test']);
        $sollicitud = $this->service->tramitar($this->alumno, 'fallada-correu', [[
            'modulo_destino_id' => 'DEST1',
            'origen' => Convalidacio::ORIGEN_PROPI_CENTRE,
            'resultat_origen_id' => $this->resultatId(),
        ]]);
        $peticio = $sollicitud->convalidacions->firstOrFail();
        $this->withoutMiddleware(RoleMiddleware::class)->actingAs(Profesor::query()->findOrFail('DIR00001'), 'profesor');
        Mail::shouldReceive('to')->once()->with('alumne@example.test')->andThrow(new \RuntimeException('Error SMTP intern'));

        $this->from(route('convalidacions.direction.show', $sollicitud))
            ->put(route('convalidacions.direction.resolve', $peticio), [
                'estat' => Convalidacio::ESTAT_ACCEPTADA,
                'observacions' => '',
                'resultat_resolucio' => 'AA',
                'mode_nota_resolucio' => 'none',
            ])
            ->assertSessionHas('warning', 'La petició s’ha actualitzat, però no s’ha pogut enviar el correu a l’alumne.');

        $this->assertSame(Convalidacio::ESTAT_ACCEPTADA, $peticio->fresh()->estat);
        $this->assertSame('AA', $peticio->fresh()->resultat_resolucio);
        $this->assertNull($peticio->fresh()->nota_resultat_resolucio);
    }

    public function test_alumne_substituix_nomes_el_document_requerit(): void
    {
        $sollicitud = $this->service->tramitar($this->alumno, 'correction', [[
            'modulo_destino_id' => 'DEST1',
            'origen' => Convalidacio::ORIGEN_ALTRE_CENTRE,
            'declaracio_responsable' => true,
            'document' => UploadedFile::fake()->create('vell.pdf', 50, 'application/pdf'),
        ]]);
        $peticio = $sollicitud->convalidacions->first();
        $direccio = Profesor::query()->findOrFail('DIR00001');
        $this->service->revisar($peticio, $direccio, Convalidacio::ESTAT_REVISAR_DOCUMENTACIO, 'Falta una pàgina.');
        $anterior = $peticio->fresh()->document_path;

        $actualitzada = $this->service->corregirDocument(
            $peticio->fresh(),
            $this->alumno,
            UploadedFile::fake()->image('nou.png')
        );

        $this->assertSame(Convalidacio::ESTAT_EN_PROCES, $actualitzada->estat);
        $this->assertSame('DEST1', $actualitzada->modulo_destino_id);
        $this->assertSame(Convalidacio::ORIGEN_ALTRE_CENTRE, $actualitzada->origen);
        Storage::disk('convalidacions')->assertMissing($anterior);
        Storage::disk('convalidacions')->assertExists($actualitzada->document_path);
    }

    public function test_policy_denega_dades_alienes_i_reserva_la_revisio_a_direccio(): void
    {
        $sollicitud = $this->service->tramitar($this->alumno, 'policy', [[
            'modulo_destino_id' => 'DEST1', 'origen' => Convalidacio::ORIGEN_PROPI_CENTRE, 'resultat_origen_id' => $this->resultatId(),
        ]]);
        $peticio = $sollicitud->convalidacions->first();
        $altre = Alumno::query()->findOrFail('87654321');
        $direccio = Profesor::query()->findOrFail('DIR00001');
        $policy = new ConvalidacioPolicy();

        $this->assertTrue($policy->view($this->alumno, $sollicitud));
        $this->assertFalse($policy->view($altre, $sollicitud));
        $this->assertFalse($policy->viewPeticio($altre, $peticio));
        $this->assertTrue($policy->viewPeticio($direccio, $peticio));
        $this->assertTrue($policy->resolve($direccio, $peticio));
    }

    public function test_alumne_pot_actualitzar_un_document_enviat_sense_canviar_estat(): void
    {
        $sollicitud = $this->service->tramitar($this->alumno, 'document-enviat', [[
            'modulo_destino_id' => 'DEST1',
            'origen' => Convalidacio::ORIGEN_ALTRE_CENTRE,
            'declaracio_responsable' => true,
            'document' => UploadedFile::fake()->create('certificat-inicial.pdf', 50, 'application/pdf'),
        ]]);
        $peticio = $sollicitud->convalidacions->firstOrFail();
        $policy = new ConvalidacioPolicy();

        $this->assertTrue($policy->correct($this->alumno, $peticio));
        $this->assertFalse($policy->correct(Alumno::query()->findOrFail('87654321'), $peticio));

        $actualitzada = $this->service->corregirDocument(
            $peticio,
            $this->alumno,
            UploadedFile::fake()->create('certificat-actualitzat.pdf', 60, 'application/pdf')
        );

        $this->assertSame(Convalidacio::ESTAT_ENVIADA, $actualitzada->estat);
        $this->assertSame('certificat-actualitzat.pdf', $actualitzada->document_original_name);
    }

    public function test_alumne_afig_un_document_a_peticio_en_revisio_sense_perdre_els_existents(): void
    {
        $sollicitud = $this->service->tramitar($this->alumno, 'afegir-document-revisio', [[
            'modulo_destino_id' => 'DEST1',
            'origen' => Convalidacio::ORIGEN_ALTRE_CENTRE,
            'declaracio_responsable' => true,
            'documents' => [[
                'descripcio' => 'Certificat acadèmic',
                'fitxer' => UploadedFile::fake()->create('certificat-academic.pdf', 50, 'application/pdf'),
            ]],
        ]]);
        $peticio = $sollicitud->convalidacions->firstOrFail();
        $peticio->forceFill(['estat' => Convalidacio::ESTAT_REVISAR_DOCUMENTACIO])->save();
        $documentInicial = $peticio->documents()->firstOrFail();
        $pathInicial = $documentInicial->path;

        $this->withoutMiddleware(RoleMiddleware::class)->actingAs($this->alumno, 'alumno');
        auth()->shouldUse('profesor');
        $this->from(route('convalidacions.show', $sollicitud))
            ->post(route('convalidacions.add-attachment', $peticio), [
                'descripcio' => 'Certificat de Prevenció de Riscos Laborals',
                'fitxer' => UploadedFile::fake()->create('certificat-prl.pdf', 40, 'application/pdf'),
            ])
            ->assertRedirect(route('convalidacions.show', $sollicitud))
            ->assertSessionHas('success', 'Document afegit correctament.');

        $peticioActualitzada = $peticio->fresh()->load('documents');
        $this->assertSame(Convalidacio::ESTAT_EN_PROCES, $peticioActualitzada->estat);
        $this->assertCount(2, $peticioActualitzada->documents);
        $this->assertSame('Certificat acadèmic', $peticioActualitzada->documents->first()->descripcio);
        $this->assertSame('Certificat de Prevenció de Riscos Laborals', $peticioActualitzada->documents->last()->descripcio);
        Storage::disk('convalidacions')->assertExists($pathInicial);
        Storage::disk('convalidacions')->assertExists($peticioActualitzada->documents->last()->path);
        $this->get(route('convalidacions.show', $sollicitud))
            ->assertOk()
            ->assertSee('Certificat acadèmic')
            ->assertSee('Certificat de Prevenció de Riscos Laborals');

        $this->withoutMiddleware(RoleMiddleware::class)
            ->actingAs(Profesor::query()->findOrFail('DIR00001'), 'profesor');
        $this->get(route('convalidacions.direction.show', $sollicitud))
            ->assertOk()
            ->assertSee('Certificat acadèmic')
            ->assertSee('Certificat de Prevenció de Riscos Laborals');
    }

    public function test_alumne_afig_un_document_enviat_i_es_conserva_l_estat_i_la_declaracio(): void
    {
        $sollicitud = $this->service->tramitar($this->alumno, 'afegir-document-enviada', [[
            'modulo_destino_id' => 'DEST1',
            'origen' => Convalidacio::ORIGEN_ALTRE_CENTRE,
            'declaracio_responsable' => true,
            'document' => UploadedFile::fake()->create('certificat-inicial.pdf', 30, 'application/pdf'),
        ]]);
        $peticio = $sollicitud->convalidacions->firstOrFail();
        $pathInicial = $peticio->document_path;

        $this->withoutMiddleware(RoleMiddleware::class)->actingAs($this->alumno, 'alumno');
        auth()->shouldUse('profesor');
        $this->post(route('convalidacions.add-attachment', $peticio), [
            'descripcio' => 'Certificat complementari',
            'fitxer' => UploadedFile::fake()->create('complementari.pdf', 30, 'application/pdf'),
        ])->assertSessionHas('success');

        $peticioActualitzada = $peticio->fresh()->load('documents');
        $this->assertSame(Convalidacio::ESTAT_ENVIADA, $peticioActualitzada->estat);
        $this->assertTrue($peticioActualitzada->declaracio_responsable);
        $this->assertCount(2, $peticioActualitzada->documents);
        Storage::disk('convalidacions')->assertExists($pathInicial);
        $this->assertSame('Certificat complementari', $peticioActualitzada->documents->last()->descripcio);
        Storage::disk('convalidacions')->assertExists($peticioActualitzada->documents->last()->path);
    }

    public function test_no_es_poden_afegir_mes_de_tres_documents_inclosos_els_llegats(): void
    {
        $sollicitud = $this->service->tramitar($this->alumno, 'max-documents-llegats', [[
            'modulo_destino_id' => 'DEST1',
            'origen' => Convalidacio::ORIGEN_PROPI_CENTRE,
            'resultat_origen_id' => $this->resultatId(),
        ]]);
        $peticio = $sollicitud->convalidacions->firstOrFail();
        $rutaLlegada = $this->alumno->nia . '/' . $sollicitud->id . '/llegat.pdf';
        $rutaPrl = $this->alumno->nia . '/' . $sollicitud->id . '/llegat-prl.pdf';
        $rutaRelacionada = $this->alumno->nia . '/' . $sollicitud->id . '/relacional.pdf';
        Storage::disk('convalidacions')->put($rutaLlegada, 'legacy');
        Storage::disk('convalidacions')->put($rutaPrl, 'legacy-prl');
        Storage::disk('convalidacions')->put($rutaRelacionada, 'related');
        $peticio->forceFill([
            'document_path' => $rutaLlegada,
            'document_original_name' => 'llegat.pdf',
            'document_mime' => 'application/pdf',
            'document_prl_path' => $rutaPrl,
            'document_prl_original_name' => 'llegat-prl.pdf',
            'document_prl_mime' => 'application/pdf',
        ])->save();
        $peticio->documents()->create([
            'descripcio' => 'Certificat acadèmic',
            'path' => $rutaRelacionada,
            'original_name' => 'relacional.pdf',
            'mime' => 'application/pdf',
        ]);

        $this->withoutMiddleware(RoleMiddleware::class)->actingAs($this->alumno, 'alumno');
        auth()->shouldUse('profesor');
        $this->post(route('convalidacions.add-attachment', $peticio), [
            'descripcio' => 'Quart document',
            'fitxer' => UploadedFile::fake()->create('quart.pdf', 30, 'application/pdf'),
            'declaracio_responsable' => '1',
        ])->assertSessionHasErrors('fitxer');

        $this->assertSame(1, $peticio->documents()->count());
        Storage::disk('convalidacions')->assertExists($rutaLlegada);
        Storage::disk('convalidacions')->assertExists($rutaPrl);
        Storage::disk('convalidacions')->assertExists($rutaRelacionada);
    }

    public function test_nomes_el_propietari_pot_afegir_documents_i_la_declaracio_es_requerida(): void
    {
        $sollicitud = $this->service->tramitar($this->alumno, 'add-attachment-security', [[
            'modulo_destino_id' => 'DEST1',
            'origen' => Convalidacio::ORIGEN_PROPI_CENTRE,
            'resultat_origen_id' => $this->resultatId(),
        ]]);
        $peticio = $sollicitud->convalidacions->firstOrFail();
        $altreAlumne = Alumno::query()->findOrFail('87654321');
        $this->withoutMiddleware(RoleMiddleware::class)->actingAs($altreAlumne, 'alumno');
        auth()->shouldUse('profesor');
        $this->post(route('convalidacions.add-attachment', $peticio), [
            'descripcio' => 'Certificat',
            'fitxer' => UploadedFile::fake()->create('certificat.pdf', 30, 'application/pdf'),
            'declaracio_responsable' => '1',
        ])->assertForbidden();

        $this->actingAs($this->alumno, 'alumno');
        auth()->shouldUse('profesor');
        $this->post(route('convalidacions.add-attachment', $peticio), [
            'descripcio' => 'Certificat',
            'fitxer' => UploadedFile::fake()->create('certificat.pdf', 30, 'application/pdf'),
        ])->assertSessionHasErrors('declaracio_responsable');
        $this->assertSame(0, $peticio->documents()->count());
        $this->assertFalse($peticio->fresh()->declaracio_responsable);
    }

    public function test_una_peticio_no_denegada_bloqueja_el_modul_i_denegada_el_torna_a_habilitar(): void
    {
        $sollicitud = $this->service->tramitar($this->alumno, 'blocking', [[
            'modulo_destino_id' => 'DEST1',
            'origen' => Convalidacio::ORIGEN_PROPI_CENTRE,
            'resultat_origen_id' => $this->resultatId(),
        ]]);

        $peticio = $sollicitud->convalidacions->first();
        foreach (array_diff(array_keys(Convalidacio::estatOptions()), [Convalidacio::ESTAT_DENEGADA]) as $estat) {
            $peticio->forceFill(['estat' => $estat])->save();
            $this->assertNotContains('DEST1', app(\Intranet\Application\Convalidacio\ConvalidacioQueryService::class)
                ->modulsActuals($this->alumno)->pluck('codigo')->all());

            try {
                $this->service->tramitar($this->alumno, 'blocked-' . $estat, [[
                    'modulo_destino_id' => 'DEST1',
                    'origen' => Convalidacio::ORIGEN_PROPI_CENTRE,
                    'resultat_origen_id' => $this->resultatId(),
                ]]);
                $this->fail('Una petició no denegada havia de bloquejar el mòdul.');
            } catch (ConvalidacioException) {
                $this->assertSame(1, SollicitudConvalidacio::query()->count());
            }
        }

        $peticio->forceFill(['estat' => Convalidacio::ESTAT_DENEGADA])->save();
        $this->assertContains('DEST1', app(\Intranet\Application\Convalidacio\ConvalidacioQueryService::class)
            ->modulsActuals($this->alumno)->pluck('codigo')->all());

        $nova = $this->service->tramitar($this->alumno, 'allowed-after-denied', [[
            'modulo_destino_id' => 'DEST1',
            'origen' => Convalidacio::ORIGEN_PROPI_CENTRE,
            'resultat_origen_id' => $this->resultatId(),
        ]]);
        $this->assertNotSame($sollicitud->id, $nova->id);
    }

    public function test_formulari_i_tramitacio_http_usen_el_guard_d_alumnat(): void
    {
        $this->withoutMiddleware(RoleMiddleware::class)->actingAs($this->alumno, 'alumno');
        auth()->shouldUse('profesor');

        $formulari = $this->get('/alumno/convalidacions/create');

        $formulari
            ->assertOk()
            ->assertSee('Nova sol·licitud')
            ->assertSee('Mòduls de la sol·licitud')
            ->assertSee('Només es tramitaran els mòduls que apareguen en esta llista.')
            ->assertSee('Destí 1')
            ->assertSee('Mòdul superat')
            ->assertSee('No trobes el mòdul o necessites aportar altres estudis?')
            ->assertSee('Vols aportar més documentació?')
            ->assertSee('<strong>Si has pogut seleccionar el mòdul</strong>, normalment no cal adjuntar més documentació perquè ja consta als registres del centre.', false)
            ->assertSee('id="builder-accreditation-description"', false)
            ->assertSee('Descriu amb claredat els estudis que vols fer servir. Direcció revisarà la sol·licitud i, si ho considera necessari, et sol·licitarà més documentació.', false)
            ->assertSee('Origen 1')
            ->assertSee('Cicle anterior')
            ->assertSee('Any 2025')
            ->assertSee('Nota 7')
            ->assertDontSee('Nota 7,00')
            ->assertSee('data-cicle-nom="Cicle anterior"', false)
            ->assertDontSee('data-cicle-codi=', false)
            ->assertSee('fillModuleReference')
            ->assertSee('ensureDeclaration')
            ->assertSee('declaracio-responsable-sollicitud')
            ->assertDontSee('builder-declaracio')
            ->assertDontSee('d-flex flex-column gap-1', false)
            ->assertSee("appendLine(cell, `Any \${item.dataset.accreditationYear} · Nota \${item.dataset.accreditationNote}`, 'small mb-1');", false)
            ->assertSee('data-nota="7"', false)
            ->assertSee('id="builder-fol-logse"', false)
            ->assertDontSee('id="builder-fol-logse" required', false)
            ->assertSee('id="fol-logse-document-notice"', false)
            ->assertSee('Exemples: certificat acadèmic, expedient o certificat PRL')
            ->assertSee('const maxDocumentBytes = 5242880;', false)
            ->assertSee('selectedFile.size > maxDocumentBytes', false)
            ->assertSee('entry.file.files[0].size > maxDocumentBytes', false)
            ->assertSee("const formatCode = (code) => code.replace(/^([A-Za-z]+)(\\d+)$/, '$1 $2');", false)
            ->assertSee('fst-italic')
            ->assertDontSee('ordinària (FI)')
            ->assertDontSee('avaluacio-2025.xml')
            ->assertSee('Estudis o certificats acadèmics d&#039;un altre centre', false)
            ->assertSee('id="origen-group" class="mb-3" hidden', false)
            ->assertSee('id="afegir-modul-modal"', false)
            ->assertSee('id="revisar-sollicitud-modal"', false)
            ->assertSee('id="confirmar-presentacio-modal"', false)
            ->assertSee('modal-dialog modal-lg modal-dialog-scrollable', false)
            ->assertSee('id="resultat-detall"', false)
            ->assertSeeText("Any d'aprovació")
            ->assertSee('Mòdul a convalidar')
            ->assertSee('Acreditació')
            ->assertSee('Accions')
            ->assertSee('id="sollicitud-taula" hidden', false)
            ->assertSee('id="sollicitud-count"', false)
            ->assertSee('Total de mòduls a convalidar: 0')
            ->assertSee('id="builder-feedback" class="visually-hidden" role="status" aria-live="polite"', false)
            ->assertDontSee('class="alert alert-success"', false)
            ->assertSee('id="revisar-sollicitud"', false)
            ->assertSee('Enviar sol·licitud')
            ->assertSee('Afegir a la sol·licitud')
            ->assertSee('Tornar i modificar')
            ->assertSee('id="presentar-sollicitud" type="button"', false)
            ->assertSee('Presentar sol·licitud')
            ->assertSee('Vas a presentar la sol·licitud.')
            ->assertSee('Tornar al resum')
            ->assertSee('id="confirmar-presentacio" type="submit"', false)
            ->assertSee('Sí, presentar')
            ->assertDontSee('id="tramitar"', false)
            ->assertSee('id="cancel-sollicitud"', false)
            ->assertSee('Vols cancel·lar la sol·licitud? Es perdrà la composició actual.', false)
            ->assertSee('convalidacio-item-afegit')
            ->assertSee('replaceChildren')
            ->assertDontSee('Prepara una petició');

        $this->assertSame(1, substr_count($formulari->getContent(), 'type="submit"'));

        $response = $this->post('/alumno/convalidacions', [
            'submission_token' => 'http-token',
            'items' => [[
                'modulo_destino_id' => 'DEST1',
                'origen' => Convalidacio::ORIGEN_ALTRE_CENTRE,
                'declaracio_responsable' => '1',
                'document' => UploadedFile::fake()->create('academic.pdf', 20, 'application/pdf'),
            ]],
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect('/alumno/convalidacions/1');
        $this->assertDatabaseHas('sollicituds_convalidacions', ['alumno_id' => '12345678'], 'sqlite');
        $this->get('/alumno/convalidacions/1')
            ->assertOk()
            ->assertSee('<table class="table table-hover align-middle">', false)
            ->assertSee('Mòdul que vol convalidar')
            ->assertSee('Estudis i documents aportats')
            ->assertSee('Estat i comentari');
    }

    public function test_rebutja_fitxer_massa_gran_al_servidor_com_a_ultima_validacio(): void
    {
        $this->withoutMiddleware(RoleMiddleware::class)->actingAs($this->alumno, 'alumno');
        auth()->shouldUse('profesor');

        $this->post('/alumno/convalidacions', [
            'submission_token' => 'fitxer-massa-gran',
            'items' => [[
                'modulo_destino_id' => 'DEST2',
                'origen' => Convalidacio::ORIGEN_ALTRE_CENTRE,
                'declaracio_responsable' => '1',
                'document' => UploadedFile::fake()->create('academic.pdf', 5121, 'application/pdf'),
            ]],
        ])->assertSessionHasErrors('items.0.document');

        $this->assertSame(0, SollicitudConvalidacio::query()->count());
        $this->assertSame(0, Convalidacio::query()->count());
    }

    public function test_error_tecnic_en_tramitacio_no_exposa_el_detall_ni_deixa_dades_parcials(): void
    {
        $this->withoutMiddleware(RoleMiddleware::class)->actingAs($this->alumno, 'alumno');
        auth()->shouldUse('profesor');
        Log::spy();
        app()->instance(ConvalidacioService::class, new class extends ConvalidacioService {
            public function __construct()
            {
            }

            /** Simula una fallada d'infraestructura posterior a la validació HTTP. */
            public function tramitar(Alumno $alumno, string $token, array $items): SollicitudConvalidacio
            {
                throw new \RuntimeException('Disc privat no disponible.');
            }
        });

        $response = $this->from('/alumno/convalidacions/create')->post('/alumno/convalidacions', [
            'submission_token' => 'technical-error',
            'items' => [[
                'modulo_destino_id' => 'DEST1',
                'origen' => Convalidacio::ORIGEN_ALTRE_CENTRE,
                'declaracio_responsable' => '1',
                'fol_logse' => '0',
                'document' => UploadedFile::fake()->create('academic.pdf', 20, 'application/pdf'),
            ]],
        ]);

        $response
            ->assertRedirect('/alumno/convalidacions/create')
            ->assertSessionHasErrors('items');
        $this->assertSame(0, SollicitudConvalidacio::query()->count());
        $this->assertSame(0, Convalidacio::query()->count());
        Log::shouldHaveReceived('error')->once();
    }

    public function test_una_declaracio_responsable_cobreix_tots_els_items_externs_de_la_sollicitud(): void
    {
        $this->withoutMiddleware(RoleMiddleware::class)->actingAs($this->alumno, 'alumno');
        auth()->shouldUse('profesor');

        $response = $this->post('/alumno/convalidacions', [
            'submission_token' => 'global-declaration',
            'declaracio_responsable_sollicitud' => '1',
            'items' => [
                [
                    'modulo_destino_id' => 'DEST1',
                    'origen' => Convalidacio::ORIGEN_ALTRE_CENTRE,
                    'documents' => [[
                        'descripcio' => 'Certificat acadèmic',
                        'fitxer' => UploadedFile::fake()->create('academic-1.pdf', 20, 'application/pdf'),
                    ]],
                ],
                [
                    'modulo_destino_id' => 'DEST2',
                    'origen' => Convalidacio::ORIGEN_ALTRE_CENTRE,
                    'documents' => [[
                        'descripcio' => 'Certificat acadèmic',
                        'fitxer' => UploadedFile::fake()->create('academic-2.pdf', 20, 'application/pdf'),
                    ]],
                ],
            ],
        ]);

        $response->assertSessionHasNoErrors()->assertRedirect('/alumno/convalidacions/1');
        $this->assertSame(2, Convalidacio::query()->where('declaracio_responsable', true)->count());
        $this->assertSame(2, DB::table('documents_convalidacions')->count());
    }

    public function test_declaracio_responsable_tambe_s_exigix_i_es_guarda_amb_documents_del_propi_centre(): void
    {
        try {
            $this->service->tramitar($this->alumno, 'own-doc-without-declaration', [[
                'modulo_destino_id' => 'DEST1',
                'origen' => Convalidacio::ORIGEN_PROPI_CENTRE,
                'resultat_origen_id' => $this->resultatId(),
                'documents' => [[
                    'descripcio' => 'Document original',
                    'fitxer' => UploadedFile::fake()->create('original.pdf', 20, 'application/pdf'),
                ]],
            ]]);
            $this->fail('La petició amb adjunt sense declaració havia de ser rebutjada.');
        } catch (ConvalidacioException $exception) {
            $this->assertStringContainsString('declaració responsable', $exception->getMessage());
        }

        $this->withoutMiddleware(RoleMiddleware::class)->actingAs($this->alumno, 'alumno');
        auth()->shouldUse('profesor');
        $response = $this->post('/alumno/convalidacions', [
            'submission_token' => 'own-doc-with-declaration',
            'declaracio_responsable_sollicitud' => '1',
            'items' => [[
                'modulo_destino_id' => 'DEST1',
                'origen' => Convalidacio::ORIGEN_PROPI_CENTRE,
                'resultat_origen_id' => $this->resultatId(),
                'documents' => [[
                    'descripcio' => 'Document original',
                    'fitxer' => UploadedFile::fake()->create('original.pdf', 20, 'application/pdf'),
                ]],
            ]],
        ]);

        $response->assertSessionHasNoErrors()->assertRedirect('/alumno/convalidacions/1');
        $peticio = Convalidacio::query()->firstOrFail();
        $this->assertTrue($peticio->declaracio_responsable);
        $this->assertSame(1, DB::table('documents_convalidacions')->count());
    }

    public function test_acreditacio_alternativa_del_propi_centre_es_guarda_com_a_declarada_i_no_com_a_resultat_xml(): void
    {
        $sollicitud = $this->service->tramitar($this->alumno, 'acreditacio-alternativa-text', [[
            'modulo_destino_id' => 'DEST1',
            'origen' => Convalidacio::ORIGEN_PROPI_CENTRE,
            'acreditacio_alternativa' => true,
            'acreditacio_descripcio' => 'FOL d’un cicle anterior que no apareix en els resultats.',
            // Un identificador manipulat no ha de convertir l’acreditació en dades verificades.
            'resultat_origen_id' => str_repeat('a', 64),
        ]]);

        $peticio = $sollicitud->convalidacions->firstOrFail();
        $this->assertTrue($peticio->acreditacio_alternativa);
        $this->assertSame('FOL d’un cicle anterior que no apareix en els resultats.', $peticio->acreditacio_descripcio);
        $this->assertNull($peticio->modulo_origen_codigo);
        $this->assertNull($peticio->nota_origen);
        $this->assertFalse($peticio->declaracio_responsable);
        $this->withoutMiddleware(RoleMiddleware::class)->actingAs($this->alumno, 'alumno');
        auth()->shouldUse('profesor');
        $this->get(route('convalidacions.show', $sollicitud))
            ->assertOk()
            ->assertSee('Acreditació alternativa — pendent de revisió manual')
            ->assertSee('FOL d’un cicle anterior que no apareix en els resultats.');

        $this->withoutMiddleware(RoleMiddleware::class)->actingAs(Profesor::query()->findOrFail('DIR00001'), 'profesor');
        $this->get(route('convalidacions.direction.show', $sollicitud))
            ->assertOk()
            ->assertSee('Acreditació alternativa — revisió manual')
            ->assertSee('No correspon a un resultat verificat en l’XML.');

        try {
            $this->service->tramitar($this->alumno, 'acreditacio-alternativa-buida', [[
                'modulo_destino_id' => 'DEST2',
                'origen' => Convalidacio::ORIGEN_PROPI_CENTRE,
                'acreditacio_alternativa' => true,
                'acreditacio_descripcio' => '   ',
            ]]);
            $this->fail('La descripció alternativa no pot estar buida.');
        } catch (ConvalidacioException $exception) {
            $this->assertStringContainsString('Descriu quin mòdul', $exception->getMessage());
        }
    }

    public function test_acreditacio_alternativa_amb_adjunts_exigix_la_declaracio_responsable(): void
    {
        $this->withoutMiddleware(RoleMiddleware::class)->actingAs($this->alumno, 'alumno');
        auth()->shouldUse('profesor');

        $base = [
            'modulo_destino_id' => 'DEST1',
            'origen' => Convalidacio::ORIGEN_PROPI_CENTRE,
            'acreditacio_alternativa' => '1',
            'acreditacio_descripcio' => 'Estudis previs no disponibles en l’XML.',
            'documents' => [[
                'descripcio' => 'Certificat acadèmic',
                'fitxer' => UploadedFile::fake()->create('certificat.pdf', 20, 'application/pdf'),
            ]],
        ];

        $this->from('/alumno/convalidacions/create')->post('/alumno/convalidacions', [
            'submission_token' => 'alternative-without-declaration',
            'items' => [$base],
        ])->assertRedirect('/alumno/convalidacions/create')->assertSessionHasErrors('items');
        $this->assertSame(0, SollicitudConvalidacio::query()->count());

        $response = $this->post('/alumno/convalidacions', [
            'submission_token' => 'alternative-with-declaration',
            'declaracio_responsable_sollicitud' => '1',
            'items' => [$base],
        ]);

        $response->assertSessionHasNoErrors()->assertRedirect('/alumno/convalidacions/1');
        $peticio = Convalidacio::query()->firstOrFail();
        $this->assertTrue($peticio->acreditacio_alternativa);
        $this->assertTrue($peticio->declaracio_responsable);
        $this->assertSame(1, DB::table('documents_convalidacions')->count());
    }

    public function test_acreditacio_alternativa_conserva_l_excepcio_fol_logse_per_a_ipe_i(): void
    {
        DB::table('modulos')->insert(['codigo' => '1709', 'cliteral' => 'IPE I', 'vliteral' => 'IPE I']);
        DB::table('modulo_ciclos')->insert(['id' => 4, 'idModulo' => '1709', 'idCiclo' => 2]);
        DB::table('modulo_grupos')->insert(['id' => 4, 'idGrupo' => 'ACTUAL', 'idModuloCiclo' => 4]);
        $base = [
            'modulo_destino_id' => '1709',
            'origen' => Convalidacio::ORIGEN_PROPI_CENTRE,
            'acreditacio_alternativa' => true,
            'acreditacio_descripcio' => 'FOL d’un cicle LOGSE que no apareix en l’XML.',
            'fol_logse' => true,
            'modulo_origen_es_fol' => true,
        ];

        try {
            $this->service->tramitar($this->alumno, 'alternativa-fol-logse-sense-document', [$base]);
            $this->fail('El certificat PRL havia de ser obligatori per a FOL LOGSE en IPE I.');
        } catch (ConvalidacioException $exception) {
            $this->assertStringContainsString('Adjunta el certificat de Prevenció', $exception->getMessage());
        }

        $sollicitud = $this->service->tramitar($this->alumno, 'alternativa-fol-logse-amb-document', [[
            ...$base,
            'declaracio_responsable' => true,
            'documents' => [[
                'descripcio' => 'Certificat de prevenció de riscos laborals',
                'fitxer' => UploadedFile::fake()->create('prl.pdf', 20, 'application/pdf'),
            ]],
        ]]);

        $peticio = $sollicitud->convalidacions->firstOrFail();
        $this->assertTrue($peticio->acreditacio_alternativa);
        $this->assertTrue($peticio->fol_logse);
        $this->assertTrue($peticio->modulo_origen_es_fol);
        $this->assertNull($peticio->modulo_origen_codigo);
        $this->assertSame(1, $peticio->documents()->count());
    }

    public function test_origen_extern_sense_declaracio_retorn_a_un_error_funcional(): void
    {
        $this->withoutMiddleware(RoleMiddleware::class)->actingAs($this->alumno, 'alumno');
        auth()->shouldUse('profesor');

        $response = $this->from('/alumno/convalidacions/create')->post('/alumno/convalidacions', [
            'submission_token' => 'missing-declaration',
            'items' => [[
                'modulo_destino_id' => 'DEST1',
                'origen' => Convalidacio::ORIGEN_ALTRE_CENTRE,
                'fol_logse' => '0',
                'document' => UploadedFile::fake()->create('academic.pdf', 20, 'application/pdf'),
            ]],
        ]);

        $response
            ->assertRedirect('/alumno/convalidacions/create')
            ->assertSessionHasErrors('items');
        $this->assertSame(0, SollicitudConvalidacio::query()->count());
    }

    public function test_controlador_denega_a_un_alumne_la_sollicitud_d_un_altre(): void
    {
        $sollicitud = $this->service->tramitar($this->alumno, 'private', [[
            'modulo_destino_id' => 'DEST1', 'origen' => Convalidacio::ORIGEN_PROPI_CENTRE, 'resultat_origen_id' => $this->resultatId(),
        ]]);
        $altre = Alumno::query()->findOrFail('87654321');

        $this->withoutMiddleware(RoleMiddleware::class)
            ->actingAs($altre, 'alumno');
        auth()->shouldUse('profesor');

        $this->get('/alumno/convalidacions/' . $sollicitud->id)->assertForbidden();
    }

    public function test_bloqueig_de_direccio_requerix_contrasenya_i_invalida_accessos_anteriors(): void
    {
        $this->withoutMiddleware(RoleMiddleware::class)->actingAs($this->alumno, 'alumno');
        auth()->shouldUse('profesor');
        $access = app(ConvalidacioAccessService::class);
        $this->assertTrue($access->verifyPassword('4 8 15 16 23 42'));
        $access->setBlocked(true);

        $this->get('/alumno/convalidacions/create')->assertRedirect('/alumno/convalidacions/acces');
        $this->get('/alumno/convalidacions/acces')
            ->assertOk()
            ->assertSeeText('Actualment, l’accés a les convalidacions està restringit perquè estem fent proves.')
            ->assertDontSee((string) config('convalidacions_access.password'));
        $this->from('/alumno/convalidacions/acces')->post('/alumno/convalidacions/acces', ['password' => 'incorrecta'])
            ->assertRedirect('/alumno/convalidacions/acces')
            ->assertSessionHasErrors('password');
        $this->post('/alumno/convalidacions/acces', ['password' => '4 8 15 16 23 42'])
            ->assertRedirect('/alumno/convalidacions')
            ->assertSessionHasNoErrors();
        $this->get('/alumno/convalidacions')->assertOk();

        $access->setBlocked(true);
        $this->get('/alumno/convalidacions')->assertRedirect('/alumno/convalidacions/acces');

        $access->setBlocked(false);
        $this->get('/alumno/convalidacions')->assertOk();
    }

    public function test_panell_de_direccio_mostra_i_pot_canviar_el_bloqueig(): void
    {
        $this->withoutMiddleware(RoleMiddleware::class)
            ->actingAs(Profesor::query()->findOrFail('DIR00001'), 'profesor');

        $route = Route::getRoutes()->getByName('convalidacions.direction.access');
        $this->assertNotNull($route);
        $this->assertContains('role:direccion', $route->gatherMiddleware());

        $this->get(route('convalidacions.direction.index'))
            ->assertOk()
            ->assertSee('role="switch"', false)
            ->assertSee('Entorn de proves')
            ->assertSee('Accés obert')
            ->assertSee('Contrasenya de proves:')
            ->assertSee((string) config('convalidacions_access.password'))
            ->assertSee('Convalidacions per mòdul');

        $this->put(route('convalidacions.direction.access'), ['blocked' => '1'])
            ->assertRedirect(route('convalidacions.direction.index'))
            ->assertSessionHas('success', 'Accés de l\'alumnat bloquejat.');
        $this->assertTrue(app(ConvalidacioAccessService::class)->isBlocked());

        $this->get(route('convalidacions.direction.index'))
            ->assertOk()
            ->assertSee('Accés restringit')
            ->assertSee('Bloquejat')
            ->assertSee((string) config('convalidacions_access.password'));

        $this->put(route('convalidacions.direction.access'), ['blocked' => '0'])
            ->assertRedirect(route('convalidacions.direction.index'));
        $this->get(route('convalidacions.direction.index'))
            ->assertOk()
            ->assertSee('Obert')
            ->assertSee('Accés obert');
    }

    public function test_llistat_per_modul_filtra_cada_peticio_i_no_les_seues_germanes(): void
    {
        $sollicitud = $this->service->tramitar($this->alumno, 'llistat-per-modul', [
            ['modulo_destino_id' => 'DEST1', 'origen' => Convalidacio::ORIGEN_PROPI_CENTRE, 'resultat_origen_id' => $this->resultatId()],
            ['modulo_destino_id' => 'DEST2', 'origen' => Convalidacio::ORIGEN_PROPI_CENTRE, 'resultat_origen_id' => $this->resultatId()],
        ]);
        $peticions = $sollicitud->convalidacions;
        $peticions[0]->forceFill(['estat' => Convalidacio::ESTAT_ACCEPTADA])->save();
        $peticions[1]->forceFill(['estat' => Convalidacio::ESTAT_DENEGADA])->save();
        $this->withoutMiddleware(RoleMiddleware::class)
            ->actingAs(Profesor::query()->findOrFail('DIR00001'), 'profesor');

        $this->get(route('convalidacions.direction.modules.index', [
            'estat' => Convalidacio::ESTAT_ACCEPTADA,
            'origen' => Convalidacio::ORIGEN_ALTRE_CENTRE,
            'nia' => 'NIA-QUE-NO-CORRESPON',
            'modul' => 'CODI-QUE-NO-CORRESPON',
        ]))
            ->assertOk()
            ->assertSee('Convalidacions per mòdul')
            ->assertSee('En generar l’informe, marcaré ara aquestes peticions com a')
            ->assertSee('no es registra res automàticament en ITACA')
            ->assertSee('Acceptada (Convalidada)')
            ->assertDontSee('Funcionalitat pendent de definir.')
            ->assertDontSee('register-accepted')
            ->assertSee('DEST1')
            ->assertDontSee('🤖')
            ->assertDontSee('DEST2')
            ->assertSee('#' . $sollicitud->id)
            ->assertSee('pdf-convalidacions-modal')
            ->assertSee('pdf-estat')
            ->assertSee('Tots els estats')
            ->assertSee('Cancel·lar')
            ->assertDontSee('filter-origen')
            ->assertDontSee('filter-nia')
            ->assertDontSee('filter-modul');

        $this->get(route('convalidacions.direction.modules.index', [
            'origen' => Convalidacio::ORIGEN_ALTRE_CENTRE,
            'nia' => 'NIA-QUE-NO-CORRESPON',
            'modul' => 'CODI-QUE-NO-CORRESPON',
        ]))
            ->assertOk()
            ->assertSee('DEST1')
            ->assertSee('DEST2');
        $this->get(route('convalidacions.direction.index', ['pestanya' => 'resoltes']))
            ->assertOk()
            ->assertSee('#' . $sollicitud->id)
            ->assertSee('Mòduls')
            ->assertDontSee('Automatització');
    }

    public function test_pdf_per_modul_filtra_per_estat_i_no_inclou_adjunts(): void
    {
        $sollicitud = $this->service->tramitar($this->alumno, 'pdf-per-modul', [[
            'modulo_destino_id' => 'DEST2',
            'origen' => Convalidacio::ORIGEN_ALTRE_CENTRE,
            'declaracio_responsable' => true,
            'document' => UploadedFile::fake()->create('confidencial.pdf', 20, 'application/pdf'),
        ]]);
        $sollicitudAlt = $this->service->tramitar($this->alumno, 'pdf-per-modul-alt', [[
            'modulo_destino_id' => 'DEST1',
            'origen' => Convalidacio::ORIGEN_PROPI_CENTRE,
            'acreditacio_alternativa' => true,
            'acreditacio_descripcio' => 'Estudis previs que no apareixen en els resultats del centre.',
        ]]);
        $sollicitudAlt->convalidacions->first()->forceFill(['estat' => Convalidacio::ESTAT_DENEGADA])->save();
        $this->withoutMiddleware(RoleMiddleware::class)
            ->actingAs(Profesor::query()->findOrFail('DIR00001'), 'profesor');

        $this->assertCount(1, app(ConvalidacioQueryService::class)->peticionsDireccion([
            'estat' => Convalidacio::ESTAT_ENVIADA,
        ]));
        $this->assertCount(1, app(ConvalidacioQueryService::class)->peticionsDireccion([
            'estat' => Convalidacio::ESTAT_DENEGADA,
        ]));
        $this->assertCount(2, app(ConvalidacioQueryService::class)->peticionsDireccion());

        $response = $this->get(route('convalidacions.direction.modules.pdf', [
            'estat' => Convalidacio::ESTAT_ENVIADA,
            'nia' => 'NIA-QUE-NO-CORRESPON',
            'modul' => 'CODI-QUE-NO-CORRESPON',
        ]))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');
        $this->assertStringStartsWith('%PDF', $response->getContent());

        $this->from(route('convalidacions.direction.modules.index'))
            ->get(route('convalidacions.direction.modules.pdf', ['estat' => 'estat-no-valid']))
            ->assertRedirect(route('convalidacions.direction.modules.index'))
            ->assertSessionHasErrors('estat');

        $this->get(route('convalidacions.direction.modules.pdf'))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');

        $this->assertDatabaseHas('convalidacions', [
            'sollicitud_convalidacio_id' => $sollicitud->id,
            'document_original_name' => 'confidencial.pdf',
        ], 'sqlite');
        $this->assertStringNotContainsString('confidencial.pdf', $response->getContent());

        $vistaPdf = view('intranet.convalidacions.direccion.moduls-pdf', [
            'peticions' => collect([$sollicitudAlt->convalidacions->firstOrFail()->fresh()->load(['sollicitud.alumno', 'moduloDestino'])]),
            'generat' => now(),
            'generatPer' => Profesor::query()->findOrFail('DIR00001'),
            'registreItacaConfirmat' => false,
            'estatInforme' => null,
            'errorsCorreu' => 0,
        ])->render();
        $this->assertStringContainsString('Estudis indicats per l’alumne:', $vistaPdf);
        $this->assertStringContainsString('Estudis previs que no apareixen en els resultats del centre.', $vistaPdf);
        $this->assertStringNotContainsString('confidencial.pdf', $vistaPdf);
    }

    public function test_pdf_ordena_les_peticions_pel_nom_visible_de_l_alumne(): void
    {
        DB::table('alumnos')->where('nia', '12345678')->update([
            'nombre' => 'Zara', 'apellido1' => 'Zeta', 'apellido2' => '',
        ]);
        DB::table('alumnos')->where('nia', '87654321')->update([
            'nombre' => 'Aina', 'apellido1' => 'Alba', 'apellido2' => '',
        ]);
        DB::table('alumnos_grupos')->insert(['idAlumno' => '87654321', 'idGrupo' => 'ACTUAL']);

        $sollicitudZ = $this->service->tramitar($this->alumno, 'pdf-ordre-z', [[
            'modulo_destino_id' => 'DEST1',
            'origen' => Convalidacio::ORIGEN_PROPI_CENTRE,
            'resultat_origen_id' => $this->resultatId(),
        ]]);
        $alumneA = Alumno::query()->findOrFail('87654321');
        $sollicitudA = $this->service->tramitar($alumneA, 'pdf-ordre-a', [[
            'modulo_destino_id' => 'DEST2',
            'origen' => Convalidacio::ORIGEN_PROPI_CENTRE,
            'resultat_origen_id' => app(ResultatsAcademicsXmlService::class)->aprovats('87654321')[0]['id'],
        ]]);

        $ordenades = app(ConvalidacioQueryService::class)->ordenarPerNomAlumne(
            app(ConvalidacioQueryService::class)->peticionsDireccion()
        );

        $this->assertSame([
            $sollicitudA->convalidacions->first()->id,
            $sollicitudZ->convalidacions->first()->id,
        ], $ordenades->pluck('id')->all());
    }

    public function test_pdf_d_acceptades_marca_només_les_peticions_incloses_com_a_convalidades_en_itaca(): void
    {
        Mail::fake();
        $sollicitud = $this->service->tramitar($this->alumno, 'registre-itaca', [
            ['modulo_destino_id' => 'DEST1', 'origen' => Convalidacio::ORIGEN_PROPI_CENTRE, 'resultat_origen_id' => $this->resultatId()],
            ['modulo_destino_id' => 'DEST2', 'origen' => Convalidacio::ORIGEN_PROPI_CENTRE, 'resultat_origen_id' => $this->resultatId()],
        ]);
        $peticions = $sollicitud->convalidacions;
        $peticions[0]->forceFill([
            'estat' => Convalidacio::ESTAT_ACCEPTADA,
            'resultat_automatic' => 'AA',
            'nota_resultat_automatic' => 9,
            'resultat_resolucio' => 'AA',
            'mode_nota_resolucio' => 'preserve',
            'nota_resultat_resolucio' => 9,
        ])->save();
        $peticions[1]->forceFill(['estat' => Convalidacio::ESTAT_DENEGADA])->save();
        $this->alumno->forceFill(['email' => 'alumne@example.test'])->save();
        $dataAnterior = $peticions[0]->fresh()->estat_canviat_at;
        $this->withoutMiddleware(RoleMiddleware::class)
            ->actingAs(Profesor::query()->findOrFail('DIR00001'), 'profesor');

        $response = $this->post(route('convalidacions.direction.modules.pdf.post'), [
            'estat' => Convalidacio::ESTAT_ACCEPTADA,
            'marcar_itaca' => '1',
        ])->assertOk()->assertHeader('content-type', 'application/pdf');

        $this->assertStringStartsWith('%PDF', $response->getContent());
        $this->assertSame(Convalidacio::ESTAT_ACCEPTADA_CONVALIDADA, $peticions[0]->fresh()->estat);
        $this->assertSame(Convalidacio::ESTAT_DENEGADA, $peticions[1]->fresh()->estat);
        $this->assertSame('DIR00001', $peticions[0]->fresh()->revisat_per);
        $this->assertNotNull($peticions[0]->fresh()->estat_canviat_at);
        $this->assertGreaterThanOrEqual($dataAnterior->getTimestamp(), $peticions[0]->fresh()->estat_canviat_at->getTimestamp());
        Mail::assertSentTimes(ConvalidacioEstatActualitzat::class, 1);

        $vistaPdf = view('intranet.convalidacions.direccion.moduls-pdf', [
            'peticions' => collect([$peticions[0]->fresh()->load(['sollicitud.alumno', 'moduloDestino'])]),
            'generat' => now(),
            'generatPer' => Profesor::query()->findOrFail('DIR00001'),
            'registreItacaConfirmat' => true,
            'estatInforme' => Convalidacio::ESTAT_ACCEPTADA,
            'errorsCorreu' => 0,
        ])->render();
        $this->assertStringContainsString('AA', $vistaPdf);
        $this->assertStringContainsString('9', $vistaPdf);
        $this->assertStringContainsString('Marcades com a «Acceptada (Convalidada)» en la intranet per:', $vistaPdf);
        $this->assertStringContainsString('responsable de registrar-les en ITACA', $vistaPdf);
    }

    public function test_pdf_només_permet_marcar_itaca_si_el_filtre_es_acceptada(): void
    {
        $sollicitud = $this->service->tramitar($this->alumno, 'registre-itaca-concurrent', [
            ['modulo_destino_id' => 'DEST1', 'origen' => Convalidacio::ORIGEN_PROPI_CENTRE, 'resultat_origen_id' => $this->resultatId()],
            ['modulo_destino_id' => 'DEST2', 'origen' => Convalidacio::ORIGEN_PROPI_CENTRE, 'resultat_origen_id' => $this->resultatId()],
        ]);
        $peticions = $sollicitud->convalidacions;
        $peticions[0]->forceFill(['estat' => Convalidacio::ESTAT_ACCEPTADA])->save();
        $peticions[1]->forceFill(['estat' => Convalidacio::ESTAT_DENEGADA])->save();
        $this->withoutMiddleware(RoleMiddleware::class)
            ->actingAs(Profesor::query()->findOrFail('DIR00001'), 'profesor');

        $this->from(route('convalidacions.direction.modules.index'))
            ->post(route('convalidacions.direction.modules.pdf.post'), [
                'estat' => '',
                'marcar_itaca' => '1',
            ])->assertRedirect(route('convalidacions.direction.modules.index'))
            ->assertSessionHasErrors('estat');

        $this->assertSame(Convalidacio::ESTAT_ACCEPTADA, $peticions[0]->fresh()->estat);
        $this->assertSame(Convalidacio::ESTAT_DENEGADA, $peticions[1]->fresh()->estat);
    }

    public function test_data_d_estat_canvia_només_quan_canvia_l_estat(): void
    {
        $sollicitud = $this->service->tramitar($this->alumno, 'data-estat', [[
            'modulo_destino_id' => 'DEST1',
            'origen' => Convalidacio::ORIGEN_PROPI_CENTRE,
            'resultat_origen_id' => $this->resultatId(),
        ]]);
        $peticio = $sollicitud->convalidacions->first();
        $dataInicial = $peticio->fresh()->estat_canviat_at;
        $director = Profesor::query()->findOrFail('DIR00001');

        \Illuminate\Support\Carbon::setTestNow($dataInicial->copy()->addDay());
        try {
            $this->service->revisar($peticio, $director, Convalidacio::ESTAT_ENVIADA, 'Observació sense canvi d’estat.');
            $this->assertSame(
                $dataInicial->getTimestamp(),
                $peticio->fresh()->estat_canviat_at->getTimestamp()
            );

            \Illuminate\Support\Carbon::setTestNow($dataInicial->copy()->addDays(2));
            $this->service->revisar($peticio->fresh(), $director, Convalidacio::ESTAT_EN_PROCES, '');
            $this->assertSame(
                $dataInicial->copy()->addDays(2)->getTimestamp(),
                $peticio->fresh()->estat_canviat_at->getTimestamp()
            );
        } finally {
            \Illuminate\Support\Carbon::setTestNow();
        }
    }

    public function test_secretaria_configurada_accedix_al_llistat_per_modul_pero_no_a_la_configuracio_de_direccio(): void
    {
        DB::table('profesores')->insert([
            'dni' => 'SEC00001',
            'nombre' => 'Secretaria',
            'rol' => (int) config('roles.rol.profesor'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        config(['avisos.secretario' => 'SEC00001']);
        $this->actingAs(Profesor::query()->findOrFail('SEC00001'), 'profesor');

        $this->get(route('convalidacions.direction.modules.index'))
            ->assertOk()
            ->assertSee(route('convalidacions.direction.modules.index'), false);
        $this->get(route('convalidacions.direction.rules.index'))->assertForbidden();
    }

    public function test_panell_de_direccio_permet_carregar_i_consultar_el_cataleg_yaml(): void
    {
        $director = Profesor::query()->findOrFail('DIR00001');
        $this->withoutMiddleware(RoleMiddleware::class)->actingAs($director, 'profesor');

        $this->get(route('convalidacions.direction.rules.index'))
            ->assertOk()
            ->assertSee('Regles de convalidació')
            ->assertSee('versió 0.4')
            ->assertSee('catàleg inicial de l’aplicació')
            ->assertSee('Anglés professional GM LFP')
            ->assertSee('Descarregar YAML actual');

        $this->post(route('convalidacions.direction.rules.store'), [
            'yaml' => UploadedFile::fake()->createWithContent('regles.yaml', $this->automaticRulesYaml()),
        ])->assertRedirect(route('convalidacions.direction.rules.index'))
            ->assertSessionHas('success');

        $this->get(route('convalidacions.direction.rules.index'))
            ->assertOk()
            ->assertSee('versió 0.4')
            ->assertSee('Destí automàtic')
            ->assertSee('Aplicar convalidacions');

        $resposta = $this->get(route('convalidacions.direction.rules.download'))
            ->assertOk()
            ->assertHeader('content-type', 'application/yaml; charset=UTF-8')
            ->assertHeader('content-disposition', 'attachment; filename="convalidacions.yaml"');
        $this->assertSame($this->automaticRulesYaml(), $resposta->getContent());
    }

    public function test_descàrrega_de_regles_usa_el_yaml_inicial_si_no_hi_ha_substitut(): void
    {
        $this->withoutMiddleware(RoleMiddleware::class)
            ->actingAs(Profesor::query()->findOrFail('DIR00001'), 'profesor');
        $contingutInicial = file_get_contents(resource_path('convalidacions/regles-lfp.yaml'));

        $resposta = $this->get(route('convalidacions.direction.rules.download'))
            ->assertOk()
            ->assertHeader('content-disposition', 'attachment; filename="convalidacions.yaml"');

        $this->assertSame($contingutInicial, $resposta->getContent());
    }

    public function test_descàrrega_de_regles_està_protegida_i_no_revela_el_yaml_a_un_alumne(): void
    {
        Storage::disk('convalidacions')->put('regles-automatiques/convalidacions.yaml', $this->automaticRulesYaml());
        $this->actingAs($this->alumno, 'alumno');

        $ruta = Route::getRoutes()->getByName('convalidacions.direction.rules.download');
        $this->assertNotNull($ruta);
        $this->assertContains('role:direccion', $ruta->gatherMiddleware());
        $this->get(route('convalidacions.direction.rules.download'))
            ->assertForbidden()
            ->assertDontSee('regla-orig1-desti-auto');
    }

    public function test_descàrrega_de_regles_informa_si_no_hi_ha_cap_catàleg(): void
    {
        $this->withoutMiddleware(RoleMiddleware::class)
            ->actingAs(Profesor::query()->findOrFail('DIR00001'), 'profesor');
        Storage::disk('convalidacions')->put('regles-automatiques/convalidacions.yaml', '   ');
        $this->app->instance(ConvalidacioReglesManager::class, new class extends ConvalidacioReglesManager {
            /** Impedix que el recurs inicial del repositori participe en este escenari. */
            public function contingutActual(): ?string
            {
                return null;
            }
        });

        $this->from(route('convalidacions.direction.rules.index'))
            ->get(route('convalidacions.direction.rules.download'))
            ->assertRedirect(route('convalidacions.direction.rules.index'))
            ->assertSessionHasErrors('yaml');
    }

    public function test_panell_principal_mostra_elegibilitat_parcial_i_permet_aplicar_la_part_elegible(): void
    {
        DB::table('modulos')->insert(['codigo' => 'DEST_AUTO', 'cliteral' => 'Destí automàtic', 'vliteral' => 'Destí automàtic']);
        DB::table('modulo_ciclos')->insert(['id' => 4, 'idModulo' => 'DEST_AUTO', 'idCiclo' => 2]);
        DB::table('modulo_grupos')->insert(['id' => 4, 'idGrupo' => 'ACTUAL', 'idModuloCiclo' => 4]);
        Storage::disk('convalidacions')->put('regles-automatiques/convalidacions.yaml', $this->automaticRulesYaml());
        $sollicitud = $this->service->tramitar($this->alumno, 'sollicitud-automatica-parcial', [
            [
                'modulo_destino_id' => 'DEST_AUTO',
                'origen' => Convalidacio::ORIGEN_PROPI_CENTRE,
                'resultat_origen_id' => $this->resultatId(),
            ],
            [
                'modulo_destino_id' => 'DEST2',
                'origen' => Convalidacio::ORIGEN_ALTRE_CENTRE,
                'declaracio_responsable' => true,
                'document' => UploadedFile::fake()->create('certificat.pdf', 50, 'application/pdf'),
            ],
        ]);
        $director = Profesor::query()->findOrFail('DIR00001');
        $this->withoutMiddleware(RoleMiddleware::class)->actingAs($director, 'profesor');

        $this->get(route('convalidacions.direction.index'))
            ->assertOk()
            ->assertSee('Aplicar ara 1 convalidació automàtica')
            ->assertSee('Resolució automàtica')
            ->assertSee('Configuració de convalidacions')
            ->assertDontSee('Gestionar regles automàtiques')
            ->assertSee('🤖✍️')
            ->assertSee('title="Resolució mixta: 1 de 2 peticions pendents es poden resoldre automàticament; 1 requerix revisió manual."', false)
            ->assertSee('class="label label-primary" role="img"', false)
            ->assertSee('name="return_to" value="index"', false);

        $this->get(route('convalidacions.direction.show', $sollicitud))
            ->assertOk()
            ->assertSee('Disponible per a resolució automàtica')
            ->assertSee('Regla: regla-orig1-desti-auto')
            ->assertSee('Sense regla automàtica aplicable ara; requerix revisió manual.');

        $this->post(route('convalidacions.direction.rules.apply'), ['return_to' => 'index'])
            ->assertRedirect(route('convalidacions.direction.index'))
            ->assertSessionHas('resultatAutomatic.aplicats', 1);

        $this->assertDatabaseHas('convalidacions', [
            'sollicitud_convalidacio_id' => $sollicitud->id,
            'modulo_destino_id' => 'DEST_AUTO',
            'estat' => Convalidacio::ESTAT_ACCEPTADA,
            'regla_automatica_id' => 'regla-orig1-desti-auto',
        ], 'sqlite');
        $this->assertDatabaseHas('convalidacions', [
            'sollicitud_convalidacio_id' => $sollicitud->id,
            'modulo_destino_id' => 'DEST2',
            'estat' => Convalidacio::ESTAT_ENVIADA,
            'regla_automatica_id' => null,
        ], 'sqlite');
    }

    public function test_acreditacio_alternativa_no_apareix_com_a_elegible_per_a_una_regla_automatica(): void
    {
        DB::table('modulos')->insert(['codigo' => 'DEST_AUTO', 'cliteral' => 'Destí automàtic', 'vliteral' => 'Destí automàtic']);
        DB::table('modulo_ciclos')->insert(['id' => 4, 'idModulo' => 'DEST_AUTO', 'idCiclo' => 2]);
        DB::table('modulo_grupos')->insert(['id' => 4, 'idGrupo' => 'ACTUAL', 'idModuloCiclo' => 4]);
        Storage::disk('convalidacions')->put('regles-automatiques/convalidacions.yaml', $this->automaticRulesYaml());
        $this->service->tramitar($this->alumno, 'alternativa-no-automatica', [[
            'modulo_destino_id' => 'DEST_AUTO',
            'origen' => Convalidacio::ORIGEN_PROPI_CENTRE,
            'acreditacio_alternativa' => true,
            'acreditacio_descripcio' => 'Estudis que no apareixen en l’XML.',
        ]]);

        $preview = app(ConvalidacioAutomaticaService::class)->previsualitzar();

        $this->assertCount(0, $preview['casos']);
        $this->assertSame('L’acreditació alternativa requerix revisió manual.', $preview['bloquejats']['regla-orig1-desti-auto']);
        $this->assertDatabaseHas('convalidacions', [
            'modulo_destino_id' => 'DEST_AUTO',
            'acreditacio_alternativa' => true,
            'regla_automatica_id' => null,
            'estat' => Convalidacio::ESTAT_ENVIADA,
        ], 'sqlite');
    }

    public function test_llistat_de_direccio_deixa_buides_les_accions_i_automatitzacio_sense_pendents(): void
    {
        $sollicitud = $this->service->tramitar($this->alumno, 'sense-pendents-llistat', [[
            'modulo_destino_id' => 'DEST1',
            'origen' => Convalidacio::ORIGEN_PROPI_CENTRE,
            'resultat_origen_id' => $this->resultatId(),
        ]]);
        $sollicitud->convalidacions->firstOrFail()->forceFill([
            'estat' => Convalidacio::ESTAT_DENEGADA,
            'observacions' => 'No correspon.',
        ])->save();
        $this->withoutMiddleware(RoleMiddleware::class)
            ->actingAs(Profesor::query()->findOrFail('DIR00001'), 'profesor');

        $this->get(route('convalidacions.direction.index'))
            ->assertOk()
            ->assertDontSee('Cap acció pendent de Direcció')
            ->assertDontSee('Sense peticions pendents')
            ->assertDontSee('Revisió manual:');
    }

    public function test_llistat_de_direccio_mostra_la_icona_de_revisio_manual_amb_hint(): void
    {
        $sollicitud = $this->service->tramitar($this->alumno, 'revisio-manual-llistat', [[
            'modulo_destino_id' => 'DEST2',
            'origen' => Convalidacio::ORIGEN_ALTRE_CENTRE,
            'declaracio_responsable' => true,
            'document' => UploadedFile::fake()->create('certificat.pdf', 50, 'application/pdf'),
        ]]);
        $sollicitud->convalidacions->firstOrFail()->forceFill(['estat' => Convalidacio::ESTAT_EN_PROCES])->save();
        $this->withoutMiddleware(RoleMiddleware::class)
            ->actingAs(Profesor::query()->findOrFail('DIR00001'), 'profesor');

        $this->get(route('convalidacions.direction.index'))
            ->assertOk()
            ->assertSee('✍️')
            ->assertSee('title="Revisió manual: no hi ha cap regla automàtica aplicable a 1 petició pendent."', false);
    }

    public function test_direccio_pot_eliminar_una_sollicitud_i_els_documents_privats(): void
    {
        $sollicitud = $this->service->tramitar($this->alumno, 'delete-test', [[
            'modulo_destino_id' => 'DEST1',
            'origen' => Convalidacio::ORIGEN_ALTRE_CENTRE,
            'declaracio_responsable' => true,
            'document' => UploadedFile::fake()->create('prova.pdf', 50, 'application/pdf'),
        ]]);
        $peticio = $sollicitud->convalidacions->firstOrFail();
        $prlPath = '12345678/' . $sollicitud->id . '/prl.pdf';
        Storage::disk('convalidacions')->put($prlPath, 'contingut de prova');
        $peticio->forceFill(['document_prl_path' => $prlPath])->save();
        $this->withoutMiddleware(RoleMiddleware::class)
            ->actingAs(Profesor::query()->findOrFail('DIR00001'), 'profesor');

        $deleteRoute = Route::getRoutes()->getByName('convalidacions.direction.destroy');
        $this->assertNotNull($deleteRoute);
        $this->assertContains('role:direccion', $deleteRoute->gatherMiddleware());
        $this->get(route('convalidacions.direction.show', $sollicitud))
            ->assertOk()
            ->assertSee('<table class="table table-hover align-middle">', false)
            ->assertSee('Mòdul que vol convalidar')
            ->assertSee('Estudis i documents aportats')
            ->assertSee('Estat i comentari')
            ->assertSee('Eliminar sol·licitud de prova')
            ->assertSee('elimina tota la traçabilitat', false);

        $this->delete(route('convalidacions.direction.destroy', $sollicitud))
            ->assertRedirect(route('convalidacions.direction.index'))
            ->assertSessionHasNoErrors();

        $this->assertDatabaseMissing('sollicituds_convalidacions', ['id' => $sollicitud->id], 'sqlite');
        $this->assertDatabaseMissing('convalidacions', ['id' => $peticio->id], 'sqlite');
        Storage::disk('convalidacions')->assertMissing($peticio->document_path);
        Storage::disk('convalidacions')->assertMissing($prlPath);
    }

    public function test_regla_yaml_coincident_crea_una_convalidacio_acceptada_idempotent(): void
    {
        DB::table('modulos')->insert(['codigo' => 'DEST_AUTO', 'cliteral' => 'Destí automàtic', 'vliteral' => 'Destí automàtic']);
        DB::table('modulo_ciclos')->insert(['id' => 4, 'idModulo' => 'DEST_AUTO', 'idCiclo' => 2]);
        DB::table('modulo_grupos')->insert(['id' => 4, 'idGrupo' => 'ACTUAL', 'idModuloCiclo' => 4]);
        Storage::disk('convalidacions')->put('regles-automatiques/convalidacions.yaml', $this->automaticRulesYaml());
        $sollicitud = $this->service->tramitar($this->alumno, 'sollicitud-automatica', [[
            'modulo_destino_id' => 'DEST_AUTO',
            'origen' => Convalidacio::ORIGEN_PROPI_CENTRE,
            'resultat_origen_id' => $this->resultatId(),
        ]]);
        $peticioOriginal = $sollicitud->convalidacions()->firstOrFail();

        $service = app(ConvalidacioAutomaticaService::class);
        $preview = $service->previsualitzar();
        $this->assertCount(1, $preview['casos']);
        $this->assertSame('regla-orig1-desti-auto', $preview['casos'][0]['regla']['id']);

        $director = Profesor::query()->findOrFail('DIR00001');
        $first = $service->aplicar($director);
        $second = $service->aplicar($director);

        $this->assertSame(['aplicats' => 1, 'ja_existien' => 0, 'errors' => []], $first);
        $this->assertSame(['aplicats' => 0, 'ja_existien' => 0, 'errors' => []], $second);
        $peticio = Convalidacio::query()->where('modulo_destino_id', 'DEST_AUTO')->firstOrFail();
        $this->assertSame($peticioOriginal->id, $peticio->id);
        $this->assertSame(Convalidacio::ESTAT_ACCEPTADA, $peticio->estat);
        $this->assertSame(Convalidacio::ORIGEN_PROPI_CENTRE, $peticio->origen);
        $this->assertSame($sollicitud->id, $peticio->sollicitud_convalidacio_id);
        $this->assertSame(1, SollicitudConvalidacio::query()->count());
        $this->assertSame('CO', $peticio->resultat_automatic);
        $this->assertSame(7.0, $peticio->nota_resultat_automatic);
        $this->assertSame('CO', $peticio->resultat_resolucio);
        $this->assertSame('preserve', $peticio->mode_nota_resolucio);
        $this->assertSame(7.0, $peticio->nota_resultat_resolucio);
        $this->assertSame('art. 126.3.b', $peticio->base_normativa_automatica[0]['reference']);
        $this->assertSame('regla-orig1-desti-auto', $peticio->regla_automatica_snapshot['rule']['id']);
        $this->assertSame('ORIG1', $peticio->evidencia_automatica_snapshot['resultat_origen']['modul']);

        $this->withoutMiddleware(RoleMiddleware::class)->actingAs($director, 'profesor');
        $this->get(route('convalidacions.direction.modules.index'))
            ->assertOk()
            ->assertSee('🤖')
            ->assertSee('aria-label="Resolució automàtica"', false)
            ->assertSee('Resultat CO');
        $this->assertSame('ACT', $peticio->evidencia_automatica_snapshot['matricula_destinacio']['ciclo_matricula_codigo']);
    }

    public function test_regles_equivalents_no_bloquegen_una_convalidacio_i_queden_a_la_instantania(): void
    {
        DB::table('modulos')->insert(['codigo' => 'DEST_AUTO', 'cliteral' => 'Destí automàtic', 'vliteral' => 'Destí automàtic']);
        DB::table('modulo_ciclos')->insert(['id' => 4, 'idModulo' => 'DEST_AUTO', 'idCiclo' => 2]);
        DB::table('modulo_grupos')->insert(['id' => 4, 'idGrupo' => 'ACTUAL', 'idModuloCiclo' => 4]);
        $duplicada = <<<'YAML'
  - id: regla-orig1-desti-auto-equivalent
    target:
      code: DEST_AUTO
      name: Destí automàtic
      level: GS
    source:
      type: module
      code: ORIG1
      name: Origen de prova amb un altre àlies
    proposal:
      action: convalidate
    resolution:
      authority: centre
    result:
      status: CO
      grade:
        mode: preserve
    legal_basis:
      - reference: art. 126.3.b
YAML;
        Storage::disk('convalidacions')->put(
            'regles-automatiques/convalidacions.yaml',
            $this->automaticRulesYaml() . "\n" . $duplicada
        );
        $this->service->tramitar($this->alumno, 'sollicitud-regles-equivalents', [[
            'modulo_destino_id' => 'DEST_AUTO',
            'origen' => Convalidacio::ORIGEN_PROPI_CENTRE,
            'resultat_origen_id' => $this->resultatId(),
        ]]);

        $service = app(ConvalidacioAutomaticaService::class);
        $preview = $service->previsualitzar();
        $this->assertCount(1, $preview['casos']);
        $this->assertSame([1, 1], array_column($preview['regles'], 'casos'));

        $service->aplicar(Profesor::query()->findOrFail('DIR00001'));
        $peticio = Convalidacio::query()->where('modulo_destino_id', 'DEST_AUTO')->firstOrFail();
        $this->assertCount(2, $peticio->regla_automatica_snapshot['equivalent_matching_rules']);
    }

    public function test_regles_verificables_del_cataleg_inicial_es_poden_aplicar(): void
    {
        DB::table('modulos')->insert([
            ['codigo' => '0156', 'cliteral' => 'Anglés professional GM', 'vliteral' => 'Anglés professional GM'],
            ['codigo' => '0179', 'cliteral' => 'Anglés professional GS', 'vliteral' => 'Anglés professional GS'],
            ['codigo' => '1708', 'cliteral' => 'Sostenibilitat', 'vliteral' => 'Sostenibilitat'],
        ]);
        DB::table('ciclos')->insert([
            'id' => 3,
            'ciclo' => 'GM-ACT',
            'cliteral' => 'Cicle GM matriculat cas',
            'vliteral' => 'Cicle GM matriculat val',
            'departamento' => 24,
            'tipo' => 1,
            'normativa' => 'LFP',
        ]);
        DB::table('departamentos')->where('id', 24)->update([
            'codigo_xml' => 'FAMILIA',
            'abreviatura_xml' => '130',
        ]);
        DB::table('grupos')->insert(['codigo' => 'GM-ACT', 'nombre' => 'Grup GM']);
        DB::table('alumnos_grupos')->insert([
            ['idAlumno' => $this->alumno->nia, 'idGrupo' => 'GM-ACT'],
            ['idAlumno' => '87654321', 'idGrupo' => 'ACTUAL'],
            ['idAlumno' => '87654321', 'idGrupo' => 'GM-ACT'],
        ]);
        DB::table('modulo_ciclos')->insert([
            ['id' => 4, 'idModulo' => '0179', 'idCiclo' => 2],
            ['id' => 5, 'idModulo' => '0156', 'idCiclo' => 3],
            ['id' => 6, 'idModulo' => '1708', 'idCiclo' => 3],
        ]);
        DB::table('modulo_grupos')->insert([
            ['id' => 4, 'idGrupo' => 'ACTUAL', 'idModuloCiclo' => 4],
            ['id' => 5, 'idGrupo' => 'GM-ACT', 'idModuloCiclo' => 5],
            ['id' => 6, 'idGrupo' => 'GM-ACT', 'idModuloCiclo' => 6],
        ]);

        Storage::disk('convalidacions_xml')->put('avaluacio-2025.xml', <<<'XML'
<?xml version="1.0"?>
<centro curso="2025">
  <cursos>
    <curso codigo="FAMILIA" padre=" " nombre_val="Família" nombre_cas="Familia"/>
    <curso codigo="CICLE-GS" padre="FAMILIA" nombre_val="Cicle GS" nombre_cas="Ciclo GS"/>
    <curso codigo="CICLE-GM" padre="FAMILIA" nombre_val="Cicle GM" nombre_cas="Ciclo GM"/>
    <curso codigo="CURS-GS" padre="CICLE-GS" nombre_val="Primer GS" nombre_cas="Primero GS"/>
    <curso codigo="CURS-GM" padre="CICLE-GM" nombre_val="Primer GM" nombre_cas="Primero GM"/>
  </cursos>
  <contenidos>
    <contenido curso="CURS-GS" codigo="0179" nombre_val="Anglés professional GS" nombre_cas="Inglés profesional GS"/>
    <contenido curso="CURS-GS" codigo="CV0003" nombre_val="Anglés tècnic" nombre_cas="Inglés técnico"/>
    <contenido curso="CURS-GM" codigo="0156" nombre_val="Anglés professional GM" nombre_cas="Inglés profesional GM"/>
    <contenido curso="CURS-GM" codigo="CV0001" nombre_val="Anglés tècnic" nombre_cas="Inglés técnico"/>
    <contenido curso="CURS-GM" codigo="1708130" nombre_val="Sostenibilitat" nombre_cas="Sostenibilidad"/>
  </contenidos>
  <calificaciones>
    <calificacion alumno="12345678" curso="CURS-GS" contenido="0179" evaluacion="FI" nota_numerica="7"/>
    <calificacion alumno="12345678" curso="CURS-GM" contenido="0156" evaluacion="FI" nota_numerica="8"/>
    <calificacion alumno="12345678" curso="CURS-GM" contenido="1708130" evaluacion="FI" nota_numerica="9"/>
    <calificacion alumno="87654321" curso="CURS-GS" contenido="CV0003" evaluacion="FI" nota_numerica="6"/>
    <calificacion alumno="87654321" curso="CURS-GM" contenido="CV0001" evaluacion="FI" nota_numerica="8"/>
  </calificaciones>
</centro>
XML);

        $resultatsJo = app(ResultatsAcademicsXmlService::class)->aprovats('12345678');
        $this->service->tramitar($this->alumno, 'sollicitud-automatica-jo', [
            ['modulo_destino_id' => '0156', 'origen' => Convalidacio::ORIGEN_PROPI_CENTRE, 'resultat_origen_id' => $this->idResultat($resultatsJo, '0156')],
            ['modulo_destino_id' => '0179', 'origen' => Convalidacio::ORIGEN_PROPI_CENTRE, 'resultat_origen_id' => $this->idResultat($resultatsJo, '0179')],
            ['modulo_destino_id' => '1708', 'origen' => Convalidacio::ORIGEN_PROPI_CENTRE, 'resultat_origen_id' => $this->idResultat($resultatsJo, '1708130')],
        ]);
        $resultatsAltreAlumne = app(ResultatsAcademicsXmlService::class)->aprovats('87654321');
        $altreAlumne = Alumno::query()->findOrFail('87654321');
        $this->service->tramitar($altreAlumne, 'sollicitud-automatica-segon-alumne', [
            ['modulo_destino_id' => '0156', 'origen' => Convalidacio::ORIGEN_PROPI_CENTRE, 'resultat_origen_id' => $this->idResultat($resultatsAltreAlumne, 'CV0001')],
            ['modulo_destino_id' => '0179', 'origen' => Convalidacio::ORIGEN_PROPI_CENTRE, 'resultat_origen_id' => $this->idResultat($resultatsAltreAlumne, 'CV0003')],
        ]);

        $preview = app(ConvalidacioAutomaticaService::class)->previsualitzar();
        $this->assertSame('0.4', $preview['catalog']['version']);
        $this->assertCount(5, $preview['casos']);
        $ids = array_column(array_column($preview['casos'], 'regla'), 'id');
        $this->assertContains('0156-from-0156', $ids);
        $this->assertContains('0156-from-cv0001', $ids);
        $this->assertContains('1708-from-1708-gm-same-family', $ids);
        $this->assertContains('0179-from-0179-loe', $ids);
        $this->assertContains('0179-from-cv0003', $ids);

        $peticioFamilia = Convalidacio::query()->where('modulo_destino_id', '1708')->firstOrFail();
        $this->assertSame('1708130', $peticioFamilia->modulo_origen_codigo);
        DB::table('departamentos')->where('id', 24)->update(['abreviatura_xml' => '190']);
        $previewAbreviaturaIncorrecta = app(ConvalidacioAutomaticaService::class)->previsualitzar();
        $this->assertNotContains(
            '1708-from-1708-gm-same-family',
            array_column(array_column($previewAbreviaturaIncorrecta['casos'], 'regla'), 'id')
        );
        DB::table('departamentos')->where('id', 24)->update(['abreviatura_xml' => '130']);
        DB::table('departamentos')->insert([
            'id' => 3,
            'cliteral' => 'Departamento de otra familia',
            'vliteral' => 'Departament d’altra família',
            'familia_professional_val' => 'ALTRA FAMÍLIA',
            'familia_professional_cas' => 'OTRA FAMILIA',
            'codigo_xml' => 'ALTRA-FAMILIA',
            'abreviatura_xml' => '130',
        ]);
        $peticioFamilia->forceFill(['familia_professional_codigo' => 'ALTRA-FAMILIA'])->save();
        $previewFamiliaDiferent = app(ConvalidacioAutomaticaService::class)->previsualitzar();
        $this->assertNotContains(
            '1708-from-1708-gm-same-family',
            array_column(array_column($previewFamiliaDiferent['casos'], 'regla'), 'id')
        );
        $peticioFamilia->forceFill(['familia_professional_codigo' => 'FAMILIA'])->save();

        $director = Profesor::query()->findOrFail('DIR00001');
        $resultat = app(ConvalidacioAutomaticaService::class)->aplicar($director);
        $this->assertSame(['aplicats' => 5, 'ja_existien' => 0, 'errors' => []], $resultat);
        $this->assertSame(5, Convalidacio::query()->where('estat', Convalidacio::ESTAT_ACCEPTADA)->count());
        $this->assertSame(2, SollicitudConvalidacio::query()->count());
        $peticioFamilia = Convalidacio::query()->where('modulo_destino_id', '1708')->firstOrFail();
        $this->assertSame('1708-from-1708-gm-same-family', $peticioFamilia->regla_automatica_id);
        $this->assertSame('FAMILIA', $peticioFamilia->familia_professional_codigo);
        $this->assertSame('1708130', $peticioFamilia->evidencia_automatica_snapshot['resultat_origen']['modul']);
        $this->assertSame([
            'tipus' => 'codi_base_i_abreviatura_familia',
            'codi_regla' => '1708',
            'codi_origen' => '1708130',
            'abreviatura_familia' => '130',
        ], $peticioFamilia->evidencia_automatica_snapshot['resultat_origen']['coincidencia_codi_origen']);
        $peticioAnglesGs = Convalidacio::query()->where('modulo_origen_codigo', '0179')->firstOrFail();
        $this->assertCount(2, $peticioAnglesGs->regla_automatica_snapshot['equivalent_matching_rules']);
    }

    public function test_eie_loe_es_reconeix_pel_nom_i_s_aplica_a_ipe_ii_en_gm_i_gs(): void
    {
        $contingutRegles = file_get_contents(resource_path('convalidacions/regles-lfp.yaml'));
        $this->assertIsString($contingutRegles);
        Storage::disk('convalidacions')->put('regles-automatiques/convalidacions.yaml', $contingutRegles);

        $casos = [
            ['alumne' => '12345678', 'token' => 'eie-gm-cap-a-gm', 'nivell_origen' => 'GM', 'nivell_desti' => 1, 'codi' => '1557', 'nota' => 6],
            ['alumne' => '12345678', 'token' => 'eie-gm-cap-a-gs', 'nivell_origen' => 'GM', 'nivell_desti' => 2, 'codi' => '0756', 'nota' => 7],
            ['alumne' => '87654321', 'token' => 'eie-gs-cap-a-gm', 'nivell_origen' => 'GS', 'nivell_desti' => 1, 'codi' => '0381', 'nota' => 8],
            ['alumne' => '87654321', 'token' => 'eie-gs-cap-a-gs', 'nivell_origen' => 'GS', 'nivell_desti' => 2, 'codi' => '1134', 'nota' => 9],
        ];

        foreach ($casos as $cas) {
            $sollicitud = SollicitudConvalidacio::query()->create([
                'alumno_id' => $cas['alumne'],
                'submission_token' => $cas['token'],
                'submitted_at' => now(),
            ]);
            $nomValencia = $cas['nota'] === 9
                ? 'EMPRESA I INICIATIVA EMPRENEDORA'
                : 'Empresa i iniciativa emprenedora';
            $nomCastella = $cas['nota'] === 9
                ? 'EMPRESA E INICIATIVA EMPRENDEDORA'
                : 'Empresa e iniciativa emprendedora';
            Convalidacio::query()->create([
                'sollicitud_convalidacio_id' => $sollicitud->id,
                'modulo_destino_id' => '1710',
                'origen' => Convalidacio::ORIGEN_PROPI_CENTRE,
                'modulo_origen_codigo' => $cas['codi'],
                'modulo_origen_nombre' => $nomCastella,
                'modulo_origen_nombre_val' => $nomValencia,
                'modulo_origen_nombre_cas' => $nomCastella,
                'nivel_origen_codigo' => $cas['nivell_origen'],
                'ciclo_matricula_tipo' => $cas['nivell_desti'],
                'nota_origen' => $cas['nota'],
                'estat' => Convalidacio::ESTAT_ENVIADA,
            ]);
        }

        $sollicitudExtern = SollicitudConvalidacio::query()->create([
            'alumno_id' => '12345678',
            'submission_token' => 'eie-extern-manual',
            'submitted_at' => now(),
        ]);
        Convalidacio::query()->create([
            'sollicitud_convalidacio_id' => $sollicitudExtern->id,
            'modulo_destino_id' => '1710',
            'origen' => Convalidacio::ORIGEN_ALTRE_CENTRE,
            'modulo_origen_nombre_val' => 'Empresa i iniciativa emprenedora',
            'modulo_origen_nombre_cas' => 'Empresa e iniciativa emprendedora',
            'ciclo_matricula_tipo' => 2,
            'estat' => Convalidacio::ESTAT_ENVIADA,
        ]);

        $sollicitudNomDesconegut = SollicitudConvalidacio::query()->create([
            'alumno_id' => '12345678',
            'submission_token' => 'eie-nom-desconegut',
            'submitted_at' => now(),
        ]);
        Convalidacio::query()->create([
            'sollicitud_convalidacio_id' => $sollicitudNomDesconegut->id,
            'modulo_destino_id' => '1710',
            'origen' => Convalidacio::ORIGEN_PROPI_CENTRE,
            'modulo_origen_codigo' => '9999',
            'modulo_origen_nombre_val' => 'Emprenedoria',
            'modulo_origen_nombre_cas' => 'Emprendimiento',
            'ciclo_matricula_tipo' => 2,
            'estat' => Convalidacio::ESTAT_ENVIADA,
        ]);

        $service = app(ConvalidacioAutomaticaService::class);
        $preview = $service->previsualitzar();

        $this->assertCount(4, $preview['casos']);
        $this->assertSame(
            ['1710-from-eie-loe-cas', '1710-from-eie-loe-val'],
            array_column($preview['casos'][0]['regles_coincidents'], 'id')
        );
        $reglesEie = array_filter(
            $preview['regles'],
            static fn (array $item): bool => in_array($item['regla']['id'], [
                '1710-from-eie-loe-cas',
                '1710-from-eie-loe-val',
            ], true)
        );
        $this->assertSame([4, 4], array_values(array_map(
            static fn (array $item): int => $item['casos'],
            $reglesEie
        )));

        $resultat = $service->aplicar(Profesor::query()->findOrFail('DIR00001'));

        $this->assertSame(['aplicats' => 4, 'ja_existien' => 0, 'errors' => []], $resultat);
        $this->assertSame(4, Convalidacio::query()->where('estat', Convalidacio::ESTAT_ACCEPTADA)->count());
        foreach ($casos as $cas) {
            $peticio = Convalidacio::query()
                ->whereHas('sollicitud', static fn ($query) => $query->where('submission_token', $cas['token']))
                ->firstOrFail();
            $this->assertSame('CO', $peticio->resultat_automatic);
            $this->assertSame('preserve', $peticio->mode_nota_automatic);
            $this->assertSame((float) $cas['nota'], $peticio->nota_resultat_automatic);
        }
    }

    public function test_ipe_i_i_ipe_ii_de_mateix_codi_s_apliquen_automaticament(): void
    {
        DB::table('modulos')->insert([
            ['codigo' => '1709', 'cliteral' => 'IPE I GS', 'vliteral' => 'IPE I GS'],
            ['codigo' => '1710', 'cliteral' => 'IPE II GS', 'vliteral' => 'IPE II GS'],
        ]);
        DB::table('modulo_ciclos')->insert([
            ['id' => 4, 'idModulo' => '1709', 'idCiclo' => 2],
            ['id' => 5, 'idModulo' => '1710', 'idCiclo' => 2],
        ]);
        DB::table('modulo_grupos')->insert([
            ['id' => 4, 'idGrupo' => 'ACTUAL', 'idModuloCiclo' => 4],
            ['id' => 5, 'idGrupo' => 'ACTUAL', 'idModuloCiclo' => 5],
        ]);
        $xml = str_replace(
            '    <contenido curso="ANT-1" codigo="SUSPES" nombre_val="Mòdul suspés" nombre_cas="Módulo suspenso"/>',
            "    <contenido curso=\"ANT-1\" codigo=\"SUSPES\" nombre_val=\"Mòdul suspés\" nombre_cas=\"Módulo suspenso\"/>\n"
                . "    <contenido curso=\"ANT-1\" codigo=\"1709\" nombre_val=\"IPE I\" nombre_cas=\"IPE I\"/>\n"
                . "    <contenido curso=\"ANT-1\" codigo=\"1710\" nombre_val=\"IPE II\" nombre_cas=\"IPE II\"/>",
            $this->academicXml()
        );
        $xml = str_replace(
            '    <calificacion alumno="12345678" curso="ANT-1" contenido="SUSPES" evaluacion="FI" nota_numerica="4"/>',
            "    <calificacion alumno=\"12345678\" curso=\"ANT-1\" contenido=\"SUSPES\" evaluacion=\"FI\" nota_numerica=\"4\"/>\n"
                . "    <calificacion alumno=\"12345678\" curso=\"ANT-1\" contenido=\"1709\" evaluacion=\"FI\" nota_numerica=\"8\"/>\n"
                . "    <calificacion alumno=\"12345678\" curso=\"ANT-1\" contenido=\"1710\" evaluacion=\"FI\" nota_numerica=\"9\"/>",
            $xml
        );
        Storage::disk('convalidacions_xml')->put('avaluacio-2025.xml', $xml);
        $resultats = app(ResultatsAcademicsXmlService::class)->aprovats('12345678');

        $this->service->tramitar($this->alumno, 'sollicitud-ipe-mateix-codi', [
            ['modulo_destino_id' => '1709', 'origen' => Convalidacio::ORIGEN_PROPI_CENTRE, 'resultat_origen_id' => $this->idResultat($resultats, '1709'), 'fol_logse' => false],
            ['modulo_destino_id' => '1710', 'origen' => Convalidacio::ORIGEN_PROPI_CENTRE, 'resultat_origen_id' => $this->idResultat($resultats, '1710')],
        ]);

        $preview = app(ConvalidacioAutomaticaService::class)->previsualitzar();
        $this->assertCount(2, $preview['casos']);
        $this->assertSame(['1709-from-1709', '1710-from-1710'], array_column(array_column($preview['casos'], 'regla'), 'id'));

        app(ConvalidacioAutomaticaService::class)->aplicar(Profesor::query()->findOrFail('DIR00001'));

        foreach ([['1709', '1709-from-1709', 8.0], ['1710', '1710-from-1710', 9.0]] as [$code, $rule, $grade]) {
            $peticio = Convalidacio::query()->where('modulo_destino_id', $code)->firstOrFail();
            $this->assertSame(Convalidacio::ESTAT_ACCEPTADA, $peticio->estat);
            $this->assertSame('AA', $peticio->resultat_automatic);
            $this->assertSame($grade, $peticio->nota_resultat_automatic);
            $this->assertSame($rule, $peticio->regla_automatica_id);
            $this->assertSame('art. 126.5', $peticio->base_normativa_automatica[0]['reference']);
        }
    }

    public function test_sufix_de_familia_compost_es_restringix_a_sostenibilitat_i_digitalitzacio(): void
    {
        DB::table('departamentos')->where('id', 24)->update([
            'codigo_xml' => 'FAMILIA',
            'abreviatura_xml' => '130',
        ]);
        DB::table('ciclos')->insert([
            'id' => 3,
            'ciclo' => 'GM-ACT',
            'cliteral' => 'Cicle GM matriculat cas',
            'vliteral' => 'Cicle GM matriculat val',
            'departamento' => 24,
            'tipo' => 1,
            'normativa' => 'LFP',
        ]);
        DB::table('grupos')->insert(['codigo' => 'GM-ACT', 'nombre' => 'Grup GM']);
        DB::table('alumnos_grupos')->insert([
            'idAlumno' => $this->alumno->nia,
            'idGrupo' => 'GM-ACT',
        ]);

        $moduls = ['1708', '1664', '1665', '1709'];
        DB::table('modulos')->insert(array_map(
            static fn (string $codigo): array => ['codigo' => $codigo, 'cliteral' => $codigo, 'vliteral' => $codigo],
            $moduls
        ));
        DB::table('modulo_ciclos')->insert([
            ['id' => 4, 'idModulo' => '1708', 'idCiclo' => 3],
            ['id' => 5, 'idModulo' => '1664', 'idCiclo' => 3],
            ['id' => 6, 'idModulo' => '1665', 'idCiclo' => 2],
            ['id' => 7, 'idModulo' => '1709', 'idCiclo' => 2],
        ]);
        DB::table('modulo_grupos')->insert([
            ['id' => 4, 'idGrupo' => 'GM-ACT', 'idModuloCiclo' => 4],
            ['id' => 5, 'idGrupo' => 'GM-ACT', 'idModuloCiclo' => 5],
            ['id' => 6, 'idGrupo' => 'ACTUAL', 'idModuloCiclo' => 6],
            ['id' => 7, 'idGrupo' => 'ACTUAL', 'idModuloCiclo' => 7],
        ]);
        Storage::disk('convalidacions_xml')->put('avaluacio-2025.xml', <<<'XML'
<?xml version="1.0"?>
<centro curso="2025">
  <cursos>
    <curso codigo="FAMILIA" padre=" " nombre_val="Família" nombre_cas="Familia"/>
    <curso codigo="ANT" padre="FAMILIA" nombre_val="Cicle anterior" nombre_cas="Ciclo anterior"/>
    <curso codigo="ANT-1" padre="ANT" nombre_val="Primer" nombre_cas="Primero"/>
  </cursos>
  <contenidos>
    <contenido curso="ANT-1" codigo="1708130" nombre_val="Sostenibilitat" nombre_cas="Sostenibilidad"/>
    <contenido curso="ANT-1" codigo="1664130" nombre_val="Digitalització GM" nombre_cas="Digitalización GM"/>
    <contenido curso="ANT-1" codigo="1665130" nombre_val="Digitalització GS" nombre_cas="Digitalización GS"/>
    <contenido curso="ANT-1" codigo="1709130" nombre_val="IPE I" nombre_cas="IPE I"/>
  </contenidos>
  <calificaciones>
    <calificacion alumno="12345678" curso="ANT-1" contenido="1708130" evaluacion="FI" nota_numerica="8"/>
    <calificacion alumno="12345678" curso="ANT-1" contenido="1664130" evaluacion="FI" nota_numerica="7"/>
    <calificacion alumno="12345678" curso="ANT-1" contenido="1665130" evaluacion="FI" nota_numerica="6"/>
    <calificacion alumno="12345678" curso="ANT-1" contenido="1709130" evaluacion="FI" nota_numerica="9"/>
  </calificaciones>
</centro>
XML);
        Storage::disk('convalidacions')->put('regles-automatiques/convalidacions.yaml', <<<'YAML'
version: '0.4'
metadata:
  title: Regles de prova de codis compostos
convalidations:
  - id: sostenibilitat-gm
    target: {code: '1708', name: Sostenibilitat GM, level: GM}
    source: {type: module, code: '1708', name: Sostenibilitat, conditions: {same_professional_family: true}}
    proposal: {action: convalidate}
    resolution: {authority: centre}
    result: {status: AA, grade: {mode: preserve}}
    legal_basis: [{reference: art. 126.3.c}]
  - id: digitalitzacio-gm
    target: {code: '1664', name: Digitalització GM, level: GM}
    source: {type: module, code: '1664', name: Digitalització GM, conditions: {same_professional_family: true}}
    proposal: {action: convalidate}
    resolution: {authority: centre}
    result: {status: AA, grade: {mode: preserve}}
    legal_basis: [{reference: art. 126.3.b}]
  - id: digitalitzacio-gs
    target: {code: '1665', name: Digitalització GS, level: GS}
    source: {type: module, code: '1665', name: Digitalització GS, conditions: {same_professional_family: true}}
    proposal: {action: convalidate}
    resolution: {authority: centre}
    result: {status: AA, grade: {mode: preserve}}
    legal_basis: [{reference: art. 126.3.b}]
  - id: ipe-i-no-sufix
    target: {code: '1709', name: IPE I, level: GS}
    source: {type: module, code: '1709', name: IPE I}
    proposal: {action: convalidate}
    resolution: {authority: centre}
    result: {status: AA, grade: {mode: preserve}}
    legal_basis: [{reference: art. 126.5}]
YAML);

        $resultats = app(ResultatsAcademicsXmlService::class)->aprovats($this->alumno->nia);
        $this->service->tramitar($this->alumno, 'codis-compostos-permesos', array_map(
            function (string $modul) use ($resultats): array {
                $item = [
                    'modulo_destino_id' => $modul,
                    'origen' => Convalidacio::ORIGEN_PROPI_CENTRE,
                    'resultat_origen_id' => $this->idResultat($resultats, $modul . '130'),
                ];
                if ($modul === '1709') {
                    $item['fol_logse'] = false;
                }

                return $item;
            },
            $moduls
        ));

        $preview = app(ConvalidacioAutomaticaService::class)->previsualitzar();
        $ids = array_column(array_column($preview['casos'], 'regla'), 'id');

        $this->assertEqualsCanonicalizing(['sostenibilitat-gm', 'digitalitzacio-gm', 'digitalitzacio-gs'], $ids);
        $this->assertNotContains('ipe-i-no-sufix', $ids);
    }

    public function test_sostenibilitat_amb_sufix_de_familia_tambe_coincideix_en_grau_superior(): void
    {
        DB::table('departamentos')->where('id', 24)->update([
            'codigo_xml' => 'FAMILIA',
            'abreviatura_xml' => '130',
        ]);
        DB::table('modulos')->insert([
            'codigo' => '1708',
            'cliteral' => 'Sostenibilitat',
            'vliteral' => 'Sostenibilitat',
        ]);
        DB::table('modulo_ciclos')->insert(['id' => 4, 'idModulo' => '1708', 'idCiclo' => 2]);
        DB::table('modulo_grupos')->insert(['id' => 4, 'idGrupo' => 'ACTUAL', 'idModuloCiclo' => 4]);

        $xml = str_replace(
            ['codigo="ORIG1"', 'contenido="ORIG1"'],
            ['codigo="1708130"', 'contenido="1708130"'],
            $this->academicXml()
        );
        Storage::disk('convalidacions_xml')->put('avaluacio-2025.xml', $xml);
        Storage::disk('convalidacions')->put('regles-automatiques/convalidacions.yaml', <<<'YAML'
version: '0.4'
metadata:
  title: Regla de Sostenibilitat GS
convalidations:
  - id: sostenibilitat-gs
    target: {code: '1708', name: Sostenibilitat GS, level: GS}
    source: {type: module, code: '1708', name: Sostenibilitat, conditions: {same_professional_family: true}}
    proposal: {action: convalidate}
    resolution: {authority: centre}
    result: {status: AA, grade: {mode: preserve}}
    legal_basis: [{reference: art. 126.3.c}]
YAML);

        $resultats = app(ResultatsAcademicsXmlService::class)->aprovats($this->alumno->nia);
        $this->service->tramitar($this->alumno, 'sostenibilitat-composta-gs', [[
            'modulo_destino_id' => '1708',
            'origen' => Convalidacio::ORIGEN_PROPI_CENTRE,
            'resultat_origen_id' => $this->idResultat($resultats, '1708130'),
        ]]);

        $preview = app(ConvalidacioAutomaticaService::class)->previsualitzar();

        $this->assertSame(['sostenibilitat-gs'], array_column(array_column($preview['casos'], 'regla'), 'id'));
    }

    public function test_regles_digitalitzacio_permeten_les_direccions_confirmades_i_rebutgen_gm_a_gs(): void
    {
        DB::table('departamentos')->where('id', 24)->update([
            'codigo_xml' => 'FAMILIA',
            'abreviatura_xml' => '130',
        ]);
        DB::table('ciclos')->insert([
            'id' => 3,
            'ciclo' => 'GM-ACT',
            'cliteral' => 'Cicle GM matriculat cas',
            'vliteral' => 'Cicle GM matriculat val',
            'departamento' => 24,
            'tipo' => 1,
            'normativa' => 'LFP',
        ]);
        DB::table('grupos')->insert(['codigo' => 'GM-ACT', 'nombre' => 'Grup GM']);
        DB::table('alumnos_grupos')->insert([
            ['idAlumno' => '12345678', 'idGrupo' => 'GM-ACT'],
            ['idAlumno' => '87654321', 'idGrupo' => 'ACTUAL'],
            ['idAlumno' => '87654321', 'idGrupo' => 'GM-ACT'],
        ]);
        DB::table('modulos')->insert([
            ['codigo' => '1664', 'cliteral' => 'Digitalització GM', 'vliteral' => 'Digitalització GM'],
            ['codigo' => '1665', 'cliteral' => 'Digitalització GS', 'vliteral' => 'Digitalització GS'],
        ]);
        DB::table('modulo_ciclos')->insert([
            ['id' => 4, 'idModulo' => '1664', 'idCiclo' => 3],
            ['id' => 5, 'idModulo' => '1665', 'idCiclo' => 2],
        ]);
        DB::table('modulo_grupos')->insert([
            ['id' => 4, 'idGrupo' => 'GM-ACT', 'idModuloCiclo' => 4],
            ['id' => 5, 'idGrupo' => 'ACTUAL', 'idModuloCiclo' => 5],
        ]);
        Storage::disk('convalidacions_xml')->put('avaluacio-2025.xml', <<<'XML'
<?xml version="1.0"?>
<centro curso="2025">
  <cursos>
    <curso codigo="FAMILIA" padre=" " nombre_val="Família" nombre_cas="Familia"/>
    <curso codigo="SRC-GM" padre="FAMILIA" nombre_val="Cicle GM origen" nombre_cas="Ciclo GM origen"/>
    <curso codigo="SRC-GS" padre="FAMILIA" nombre_val="Cicle GS origen" nombre_cas="Ciclo GS origen"/>
    <curso codigo="SRC-GM-1" padre="SRC-GM" nombre_val="Primer GM" nombre_cas="Primero GM"/>
    <curso codigo="SRC-GS-1" padre="SRC-GS" nombre_val="Primer GS" nombre_cas="Primero GS"/>
  </cursos>
  <contenidos>
    <contenido curso="SRC-GM-1" codigo="1664130" nombre_val="Digitalització GM" nombre_cas="Digitalización GM"/>
    <contenido curso="SRC-GS-1" codigo="1665130" nombre_val="Digitalització GS" nombre_cas="Digitalización GS"/>
  </contenidos>
  <calificaciones>
    <calificacion alumno="12345678" curso="SRC-GM-1" contenido="1664130" evaluacion="FI" nota_numerica="7"/>
    <calificacion alumno="12345678" curso="SRC-GS-1" contenido="1665130" evaluacion="FI" nota_numerica="8"/>
    <calificacion alumno="87654321" curso="SRC-GM-1" contenido="1664130" evaluacion="FI" nota_numerica="9"/>
    <calificacion alumno="87654321" curso="SRC-GS-1" contenido="1665130" evaluacion="FI" nota_numerica="6"/>
  </calificaciones>
</centro>
XML);

        $resultatsJo = app(ResultatsAcademicsXmlService::class)->aprovats('12345678');
        $sollicitudJo = $this->service->tramitar($this->alumno, 'digitalitzacio-regles-jo', [
            ['modulo_destino_id' => '1664', 'origen' => Convalidacio::ORIGEN_PROPI_CENTRE, 'resultat_origen_id' => $this->idResultat($resultatsJo, '1664130')],
            ['modulo_destino_id' => '1665', 'origen' => Convalidacio::ORIGEN_PROPI_CENTRE, 'resultat_origen_id' => $this->idResultat($resultatsJo, '1665130')],
        ]);
        $alumneAltre = Alumno::query()->findOrFail('87654321');
        $resultatsAltre = app(ResultatsAcademicsXmlService::class)->aprovats('87654321');
        $sollicitudAltre = $this->service->tramitar($alumneAltre, 'digitalitzacio-regles-altre', [
            ['modulo_destino_id' => '1664', 'origen' => Convalidacio::ORIGEN_PROPI_CENTRE, 'resultat_origen_id' => $this->idResultat($resultatsAltre, '1665130')],
            ['modulo_destino_id' => '1665', 'origen' => Convalidacio::ORIGEN_PROPI_CENTRE, 'resultat_origen_id' => $this->idResultat($resultatsAltre, '1664130')],
        ]);

        $automatitzacions = app(ConvalidacioAutomaticaService::class);
        $preview = $automatitzacions->previsualitzar();
        $this->assertEqualsCanonicalizing(
            ['1664-from-1664-same-family', '1665-from-1665-same-family', '1664-from-1665-same-family'],
            array_column(array_column($preview['casos'], 'regla'), 'id')
        );

        foreach ([
            [$sollicitudJo, '1664'],
            [$sollicitudJo, '1665'],
            [$sollicitudAltre, '1664'],
        ] as [$sollicitud, $destinacio]) {
            $sollicitud->convalidacions()
                ->where('modulo_destino_id', $destinacio)
                ->firstOrFail()
                ->forceFill(['familia_professional_codigo' => 'ALTRA-FAMILIA'])
                ->save();
        }

        $previewFamiliaDiferent = $automatitzacions->previsualitzar();
        $this->assertSame([], $previewFamiliaDiferent['casos']);

        foreach ([
            [$sollicitudJo, '1664'],
            [$sollicitudJo, '1665'],
            [$sollicitudAltre, '1664'],
        ] as [$sollicitud, $destinacio]) {
            $sollicitud->convalidacions()
                ->where('modulo_destino_id', $destinacio)
                ->firstOrFail()
                ->forceFill(['familia_professional_codigo' => 'FAMILIA'])
                ->save();
        }

        $this->assertSame(['aplicats' => 3, 'ja_existien' => 0, 'errors' => []], $automatitzacions->aplicar(Profesor::query()->findOrFail('DIR00001')));

        foreach ([
            [$sollicitudJo, '1664', '1664-from-1664-same-family', 7.0],
            [$sollicitudJo, '1665', '1665-from-1665-same-family', 8.0],
            [$sollicitudAltre, '1664', '1664-from-1665-same-family', 6.0],
        ] as [$sollicitud, $destinacio, $regla, $nota]) {
            $peticio = $sollicitud->convalidacions()->where('modulo_destino_id', $destinacio)->firstOrFail();
            $this->assertSame(Convalidacio::ESTAT_ACCEPTADA, $peticio->estat);
            $this->assertSame('AA', $peticio->resultat_automatic);
            $this->assertSame($nota, $peticio->nota_resultat_automatic);
            $this->assertSame($regla, $peticio->regla_automatica_id);
            $this->assertSame('art. 126.3.b', $peticio->base_normativa_automatica[0]['reference']);
        }

        $noValida = $sollicitudAltre->convalidacions()->where('modulo_destino_id', '1665')->firstOrFail();
        $this->assertSame(Convalidacio::ESTAT_ENVIADA, $noValida->estat);
        $this->assertNull($noValida->regla_automatica_id);
    }

    public function test_cataleg_yaml_invalid_no_substituix_el_cataleg_actiu(): void
    {
        $manager = app(ConvalidacioReglesManager::class);
        $manager->guardar(UploadedFile::fake()->createWithContent('regles.yaml', $this->automaticRulesYaml()));
        $versionActiva = $manager->actual()['version'];

        try {
            $manager->guardar(UploadedFile::fake()->createWithContent('incorrecte.yaml', "version: '0.5'\nconvalidations: ["));
            $this->fail('El YAML mal format havia de ser rebutjat.');
        } catch (ConvalidacioException) {
            $this->assertSame($versionActiva, $manager->actual()['version']);
        }
    }

    public function test_condicio_horaria_queda_pendent_sense_correspondencia_de_cicles(): void
    {
        DB::table('modulos')->insert(['codigo' => 'DEST_AUTO', 'cliteral' => 'Destí automàtic', 'vliteral' => 'Destí automàtic']);
        DB::table('modulo_ciclos')->insert(['id' => 4, 'idModulo' => 'DEST_AUTO', 'idCiclo' => 2]);
        DB::table('modulo_grupos')->insert(['id' => 4, 'idGrupo' => 'ACTUAL', 'idModuloCiclo' => 4]);
        $yaml = str_replace(
            "      name: Origen de prova\n",
            "      name: Origen de prova\n      conditions:\n        minimum_weekly_hours: 5\n",
            $this->automaticRulesYaml()
        );
        Storage::disk('convalidacions')->put('regles-automatiques/convalidacions.yaml', $yaml);
        $this->service->tramitar($this->alumno, 'sollicitud-regla-hores', [[
            'modulo_destino_id' => 'DEST_AUTO',
            'origen' => Convalidacio::ORIGEN_PROPI_CENTRE,
            'resultat_origen_id' => $this->resultatId(),
        ]]);

        $preview = app(ConvalidacioAutomaticaService::class)->previsualitzar();

        $this->assertSame([], $preview['casos']);
        $this->assertStringContainsString('No hi ha una correspondència validada', $preview['regles'][0]['motiu']);
        $this->assertSame(1, Convalidacio::query()->where('estat', Convalidacio::ESTAT_ENVIADA)->count());
    }

    public function test_import_csv_reemplaça_només_els_cicles_contenidors_inclosos_i_preserva_zeros(): void
    {
        CorrespondenciaCicleAngles::query()->create([
            'codi_cicle_angles' => '0001',
            'nom_cicle_angles_val' => 'Antiga val',
            'nom_cicle_angles_cas' => 'Antigua cas',
            'codi_cicle_contenidor' => 'CONT-A',
            'nom_cicle_contenidor_val' => 'Contenidor A val',
            'nom_cicle_contenidor_cas' => 'Contenedor A cas',
            'es_grau_superior' => false,
        ]);
        CorrespondenciaCicleAngles::query()->create([
            'codi_cicle_angles' => '0009',
            'nom_cicle_angles_val' => 'Sense canvis val',
            'nom_cicle_angles_cas' => 'Sin cambios cas',
            'codi_cicle_contenidor' => 'CONT-B',
            'nom_cicle_contenidor_val' => 'Contenidor B val',
            'nom_cicle_contenidor_cas' => 'Contenedor B cas',
            'es_grau_superior' => true,
        ]);

        $csv = implode("\n", [
            'codi_cicle_angles,nom_cicle_angles_val,nom_cicle_angles_cas,codi_cicle_contenidor,nom_cicle_contenidor_val,nom_cicle_contenidor_cas,es_grau_superior',
            '0017,Anglés nou,Inglés nuevo,CONT-A,Cicle A,Ciclo A,si',
            '0018,Anglés altre,Inglés otro,CONT-A,Cicle A,Ciclo A,no',
        ]);
        $filesImportats = app(ConvalidacioAnglesCorrespondenciesService::class)->importar(
            UploadedFile::fake()->createWithContent('correspondencies.csv', $csv)
        );

        $this->assertSame(2, $filesImportats);
        $this->assertSame(['0017', '0018'], CorrespondenciaCicleAngles::query()
            ->where('codi_cicle_contenidor', 'CONT-A')
            ->orderBy('codi_cicle_angles')
            ->pluck('codi_cicle_angles')
            ->all());
        $this->assertSame('0009', CorrespondenciaCicleAngles::query()
            ->where('codi_cicle_contenidor', 'CONT-B')
            ->value('codi_cicle_angles'));
    }

    public function test_import_csv_invalid_no_modifica_cap_correspondencia(): void
    {
        CorrespondenciaCicleAngles::query()->create([
            'codi_cicle_angles' => 'ANT',
            'nom_cicle_angles_val' => 'Anglés existent',
            'nom_cicle_angles_cas' => 'Inglés existente',
            'codi_cicle_contenidor' => 'CONT-A',
            'nom_cicle_contenidor_val' => 'Cicle existent',
            'nom_cicle_contenidor_cas' => 'Ciclo existente',
            'es_grau_superior' => true,
        ]);
        $csv = implode("\n", [
            'codi_cicle_angles,nom_cicle_angles_val,nom_cicle_angles_cas,codi_cicle_contenidor,nom_cicle_contenidor_val,nom_cicle_contenidor_cas,es_grau_superior',
            'NOU,Anglés nou,Inglés nuevo,CONT-A,Cicle A,Ciclo A,potser',
        ]);

        try {
            app(ConvalidacioAnglesCorrespondenciesService::class)->importar(
                UploadedFile::fake()->createWithContent('correspondencies.csv', $csv)
            );
            $this->fail('El CSV amb un indicador desconegut havia de ser rebutjat.');
        } catch (ConvalidacioException) {
            $this->assertSame(['ANT'], CorrespondenciaCicleAngles::query()->pluck('codi_cicle_angles')->all());
        }
    }

    public function test_correspondencies_angles_es_gestiona_des_de_direccio_i_el_crud_guarda_codis(): void
    {
        $director = Profesor::query()->findOrFail('DIR00001');
        $this->withoutMiddleware(RoleMiddleware::class)->actingAs($director, 'profesor');

        $this->get(route('convalidacions.direction.index'))
            ->assertOk()
            ->assertSee('Configuració de convalidacions')
            ->assertDontSee('Gestionar correspondències d’anglés');
        $this->get(route('convalidacions.direction.configuracio'))
            ->assertOk()
            ->assertSee('Regles automàtiques')
            ->assertSee('Correspondències d’anglés')
            ->assertSee('Avaluacions d’ITACA')
            ->assertSee('Configurar regles')
            ->assertSee('Configurar correspondències')
            ->assertSee('Gestionar avaluacions');
        $this->get(route('convalidacions.direction.angles.index'))
            ->assertOk()
            ->assertSee('Importar CSV');
        $importRoute = Route::getRoutes()->getByName('convalidacions.direction.angles.import');
        $this->assertNotNull($importRoute);
        $this->assertContains('role:direccion', $importRoute->gatherMiddleware());
        $this->post(route('convalidacions.direction.angles.import'), [
            'csv' => UploadedFile::fake()->createWithContent('correspondencies.csv', implode("\n", [
                'codi_cicle_angles,nom_cicle_angles_val,nom_cicle_angles_cas,codi_cicle_contenidor,nom_cicle_contenidor_val,nom_cicle_contenidor_cas,es_grau_superior',
                '00017,Anglés professional,Inglés profesional,00042,Sistemes,Sistemas,1',
            ])),
        ])->assertRedirect(route('convalidacions.direction.angles.index'));
        $this->post(route('convalidacions.direction.angles.store'), [
            'codi_cicle_angles' => '00018',
            'nom_cicle_angles_val' => 'Anglés afegit',
            'nom_cicle_angles_cas' => 'Inglés añadido',
            'codi_cicle_contenidor' => '00042',
            'nom_cicle_contenidor_val' => 'Sistemes',
            'nom_cicle_contenidor_cas' => 'Sistemas',
            'es_grau_superior' => '0',
        ])->assertRedirect(route('convalidacions.direction.angles.index'));

        $editada = CorrespondenciaCicleAngles::query()->where('codi_cicle_angles', '00018')->firstOrFail();
        $this->put(route('convalidacions.direction.angles.update', $editada), [
            'codi_cicle_angles' => '00018',
            'nom_cicle_angles_val' => 'Anglés editat',
            'nom_cicle_angles_cas' => 'Inglés editado',
            'codi_cicle_contenidor' => '00042',
            'nom_cicle_contenidor_val' => 'Sistemes',
            'nom_cicle_contenidor_cas' => 'Sistemas',
            'es_grau_superior' => '1',
        ])->assertRedirect(route('convalidacions.direction.angles.index'));

        $this->assertDatabaseHas('convalidacions_correspondencies_angles', [
            'codi_cicle_angles' => '00017',
            'codi_cicle_contenidor' => '00042',
            'es_grau_superior' => true,
        ]);
        $this->assertDatabaseHas('convalidacions_correspondencies_angles', [
            'codi_cicle_angles' => '00018',
            'nom_cicle_angles_val' => 'Anglés editat',
            'es_grau_superior' => true,
        ]);
        $this->delete(route('convalidacions.direction.angles.destroy', $editada))
            ->assertRedirect(route('convalidacions.direction.angles.index'));
        $this->assertDatabaseMissing('convalidacions_correspondencies_angles', ['id' => $editada->id]);
    }

    public function test_cataleg_fol_loe_es_gestiona_des_de_direccio_i_rebutja_codis_naturalment_duplicats(): void
    {
        $director = Profesor::query()->findOrFail('DIR00001');
        $this->withoutMiddleware(RoleMiddleware::class)->actingAs($director, 'profesor');

        $this->get(route('convalidacions.direction.configuracio'))
            ->assertOk()
            ->assertSee('Mòduls FOL LOE')
            ->assertSee('Gestionar catàleg FOL LOE');
        $this->get(route('convalidacions.direction.fol-loe.index'))
            ->assertOk()
            ->assertSee('Cicle en castellà');
        $ruta = Route::getRoutes()->getByName('convalidacions.direction.fol-loe.store');
        $this->assertNotNull($ruta);
        $this->assertContains('role:direccion', $ruta->gatherMiddleware());

        $this->post(route('convalidacions.direction.fol-loe.store'), [
            'codigo' => ' 0021 ',
            'cicle_cas' => 'Educación Infantil',
            'cicle_val' => 'Educació Infantil',
            'nivell' => 'GS',
        ])->assertRedirect(route('convalidacions.direction.fol-loe.index'));

        $modul = ModulFolLoe::query()->firstOrFail();
        $this->assertSame('0021', $modul->codigo);
        $this->assertSame('21', $modul->codigo_normalitzat);
        $this->assertSame('12345678901234567890123', ModulFolLoe::normalitzarCodi('00012345678901234567890123'));
        $this->assertSame('0', ModulFolLoe::normalitzarCodi('0000'));

        $this->from(route('convalidacions.direction.fol-loe.index'))
            ->post(route('convalidacions.direction.fol-loe.store'), [
                'codigo' => '00021',
                'cicle_cas' => 'Un altre cicle',
                'cicle_val' => 'Un altre cicle',
                'nivell' => 'GM',
            ])->assertSessionHasErrors('codigo_normalitzat');

        $this->put(route('convalidacions.direction.fol-loe.update', $modul), [
            'codigo' => '21',
            'cicle_cas' => 'Educación Infantil editada',
            'cicle_val' => 'Educació Infantil editada',
            'nivell' => 'GS',
        ])->assertRedirect(route('convalidacions.direction.fol-loe.index'));
        $this->assertDatabaseHas('convalidacions_moduls_fol_loe', [
            'id' => $modul->id,
            'codigo' => '21',
            'codigo_normalitzat' => '21',
            'cicle_val' => 'Educació Infantil editada',
        ]);

        $this->delete(route('convalidacions.direction.fol-loe.destroy', $modul))
            ->assertRedirect(route('convalidacions.direction.fol-loe.index'));
        $this->assertDatabaseMissing('convalidacions_moduls_fol_loe', ['id' => $modul->id]);
    }

    public function test_migracio_inicialitza_el_cataleg_fol_loe_amb_les_dades_contrastades(): void
    {
        Schema::drop('convalidacions_moduls_fol_loe');
        $migracio = require database_path('migrations/2026_10_16_000002_create_convalidacions_moduls_fol_loe_table.php');
        $migracio->up();

        $this->assertSame(22, DB::table('convalidacions_moduls_fol_loe')->count());
        $this->assertDatabaseHas('convalidacions_moduls_fol_loe', [
            'codigo' => '0106',
            'codigo_normalitzat' => '106',
            'cicle_cas' => 'Farmacia y Parafarmacia',
            'nivell' => 'GM',
        ]);
        $migracio->down();
    }

    public function test_regla_fol_loe_usa_el_cataleg_i_compara_codis_sense_zeros_inicials(): void
    {
        DB::table('modulos')->insert(['codigo' => '1709', 'cliteral' => 'IPE I', 'vliteral' => 'IPE I']);
        DB::table('modulo_ciclos')->insert(['id' => 4, 'idModulo' => '1709', 'idCiclo' => 2]);
        DB::table('modulo_grupos')->insert(['id' => 4, 'idGrupo' => 'ACTUAL', 'idModuloCiclo' => 4]);
        DB::table('convalidacions_moduls_fol_loe')->insert([
            'codigo' => '21', 'codigo_normalitzat' => '21',
            'cicle_cas' => 'Educación Infantil', 'cicle_val' => 'Educació Infantil',
            'nivell' => 'GS', 'created_at' => now(), 'updated_at' => now(),
        ]);
        Storage::disk('convalidacions')->put('regles-automatiques/convalidacions.yaml', <<<'YAML'
version: '0.4'
metadata: {title: Prova FOL LOE}
convalidations:
  - id: 1709-from-fol-loe
    target: {code: '1709', name: IPE I, level: GM_GS}
    source: {type: module, catalog: fol_loe}
    proposal: {action: convalidate}
    resolution: {authority: centre, automatic: true}
    result: {status: CO, grade: {mode: preserve}}
    legal_basis: [{reference: art. 127.5}]
YAML);
        Storage::disk('convalidacions_xml')->put('avaluacio-2025.xml', str_replace('ORIG1', '0021', $this->academicXml()));

        $resultat = app(ResultatsAcademicsXmlService::class)->aprovats('12345678')[0];
        $this->assertSame('0021', $resultat['modul']);
        $this->service->tramitar($this->alumno, 'sollicitud-fol-loe-ipe-i', [[
            'modulo_destino_id' => '1709',
            'origen' => Convalidacio::ORIGEN_PROPI_CENTRE,
            'resultat_origen_id' => $resultat['id'],
            'fol_logse' => false,
        ]]);

        $preview = app(ConvalidacioAutomaticaService::class)->previsualitzar();
        $this->assertCount(1, $preview['casos']);
        $this->assertSame('1709-from-fol-loe', $preview['casos'][0]['regla']['id']);
        $this->assertSame('21', $preview['casos'][0]['resultat_origen']['coincidencia_codi_origen']['codi_normalitzat']);

        DB::table('convalidacions')->where('modulo_destino_id', '1709')->update(['modulo_origen_codigo' => '00021']);
        $this->assertCount(1, app(ConvalidacioAutomaticaService::class)->previsualitzar()['casos']);
        DB::table('convalidacions')->where('modulo_destino_id', '1709')->update(['modulo_origen_codigo' => '21']);
        $this->assertCount(1, app(ConvalidacioAutomaticaService::class)->previsualitzar()['casos']);
        DB::table('convalidacions')->where('modulo_destino_id', '1709')->update(['ciclo_matricula_tipo' => 1]);
        $this->assertCount(1, app(ConvalidacioAutomaticaService::class)->previsualitzar()['casos']);
        DB::table('convalidacions')->where('modulo_destino_id', '1709')->update(['modulo_origen_codigo' => '9999']);
        $this->assertCount(0, app(ConvalidacioAutomaticaService::class)->previsualitzar()['casos']);
        DB::table('convalidacions')->where('modulo_destino_id', '1709')->update(['modulo_origen_codigo' => '0021']);
        $aplicat = app(ConvalidacioAutomaticaService::class)->aplicar(Profesor::query()->findOrFail('DIR00001'));
        $this->assertSame(1, $aplicat['aplicats']);
        $peticio = Convalidacio::query()->where('modulo_destino_id', '1709')->firstOrFail();
        $this->assertSame(Convalidacio::ESTAT_ACCEPTADA, $peticio->estat);
        $this->assertSame('CO', $peticio->resultat_resolucio);
        $this->assertSame('preserve', $peticio->mode_nota_resolucio);
        $this->assertSame(7.0, (float) $peticio->nota_resultat_resolucio);
        $this->assertSame('0021', $peticio->evidencia_automatica_snapshot['resultat_origen']['modul']);
    }

    public function test_condicio_horaria_s_aplica_amb_correspondencia_i_guarda_la_seua_evidencia(): void
    {
        DB::table('modulos')->insert(['codigo' => 'DEST_AUTO', 'cliteral' => 'Destí automàtic', 'vliteral' => 'Destí automàtic']);
        DB::table('modulo_ciclos')->insert(['id' => 4, 'idModulo' => 'DEST_AUTO', 'idCiclo' => 2]);
        DB::table('modulo_grupos')->insert(['id' => 4, 'idGrupo' => 'ACTUAL', 'idModuloCiclo' => 4]);
        Storage::disk('convalidacions')->put('regles-automatiques/convalidacions.yaml', str_replace(
            "      name: Origen de prova\n",
            "      name: Origen de prova\n      conditions:\n        minimum_weekly_hours: 5\n",
            $this->automaticRulesYaml()
        ));
        CorrespondenciaCicleAngles::query()->create([
            'codi_cicle_angles' => 'ANT',
            'nom_cicle_angles_val' => 'Anglés del cicle',
            'nom_cicle_angles_cas' => 'Inglés del ciclo',
            'codi_cicle_contenidor' => 'ACT',
            'nom_cicle_contenidor_val' => 'Cicle contenidor',
            'nom_cicle_contenidor_cas' => 'Ciclo contenedor',
            'es_grau_superior' => true,
        ]);
        $this->service->tramitar($this->alumno, 'sollicitud-regla-hores-validada', [[
            'modulo_destino_id' => 'DEST_AUTO',
            'origen' => Convalidacio::ORIGEN_PROPI_CENTRE,
            'resultat_origen_id' => $this->resultatId(),
        ]]);

        $preview = app(ConvalidacioAutomaticaService::class)->previsualitzar();

        $this->assertCount(1, $preview['casos']);
        $this->assertSame('ANT', $preview['casos'][0]['correspondencia_angles']['codi_cicle_angles']);
        $director = Profesor::query()->findOrFail('DIR00001');
        app(ConvalidacioAutomaticaService::class)->aplicar($director);
        $peticio = Convalidacio::query()->where('modulo_destino_id', 'DEST_AUTO')->firstOrFail();
        $this->assertSame('Inglés del ciclo', $peticio->evidencia_automatica_snapshot['correspondencia_angles']['nom_cicle_angles_cas']);
        $this->assertTrue($peticio->evidencia_automatica_snapshot['correspondencia_angles']['es_grau_superior']);
    }

    public function test_la_copia_del_resultat_es_conserva_si_desapareix_l_xml(): void
    {
        $sollicitud = $this->service->tramitar($this->alumno, 'snapshot', [[
            'modulo_destino_id' => 'DEST1',
            'origen' => Convalidacio::ORIGEN_PROPI_CENTRE,
            'resultat_origen_id' => $this->resultatId(),
        ]]);

        Storage::disk('convalidacions_xml')->delete('avaluacio-2025.xml');
        DB::table('ciclos')->where('id', 2)->update(['ciclo' => 'CANVIAT', 'vliteral' => 'Canvi val', 'cliteral' => 'Cambio cas']);
        DB::table('departamentos')->where('id', 24)->update([
            'familia_professional_val' => 'Canvi família val',
            'familia_professional_cas' => 'Cambio familia cas',
        ]);
        $peticio = $sollicitud->convalidacions()->firstOrFail()->fresh();
        $peticio->forceFill(['observacions' => 'Documentació revisada'])->save();

        $this->assertSame('ORIG1', $peticio->modulo_origen_codigo);
        $this->assertSame('Cicle anterior', $peticio->ciclo_origen_nombre);
        $this->assertSame(2025, $peticio->any_origen);
        $this->assertSame(7.0, $peticio->nota_origen);
        $this->assertSame(2, $peticio->ciclo_matricula_id);
        $this->assertSame('ACT', $peticio->ciclo_matricula_codigo);
        $this->assertSame('Cicle matriculat val', $peticio->ciclo_matricula_nombre_val);
        $this->assertSame('Ciclo matriculado cas', $peticio->ciclo_matricula_nombre_cas);
        $this->assertSame('INFORMÀTICA I COMUNICACIONS', $peticio->familia_matricula_nombre_val);
        $this->assertSame('INFORMÁTICA Y COMUNICACIONES', $peticio->familia_matricula_nombre_cas);

        $this->withoutMiddleware(RoleMiddleware::class)
            ->actingAs($this->alumno, 'alumno');
        auth()->shouldUse('profesor');
        $detallAlumne = $this->get('/alumno/convalidacions/' . $sollicitud->id);
        $detallAlumne
            ->assertOk()
            ->assertSeeInOrder([
                'Mòdul que vol convalidar',
                'Estudis i documents aportats',
                'Estat i comentari',
                'Destí 1',
                'Cicle de matrícula:',
                '#2 · ACT',
                'Origen:',
                'Mòdul superat:',
                'Estat actual:',
                'Comentari de Direcció:',
            ])
            ->assertSeeText('Any d’aprovació:')
            ->assertSeeText('2025')
            ->assertSeeText('Cicle anterior')
            ->assertSeeText('Cicle de matrícula: #2 · ACT — Cicle matriculat val / Ciclo matriculado cas')
            ->assertSeeText('Família professional: INFORMÀTICA I COMUNICACIONS')
            ->assertSeeText('Nivell: Grau Superior')
            ->assertSeeText('Família professional: Família professional')
            ->assertSeeText('Nivell: Cicle anterior')
            ->assertSeeText('Comentari de Direcció:')
            ->assertSeeText('Documentació revisada')
            ->assertDontSeeText('Departament #24')
            ->assertDontSeeText('ITACA 3306169525')
            ->assertDontSeeText('FAMILIA —')
            ->assertDontSeeText('ANT — Cicle anterior')
            ->assertDontSeeText('Familia profesional')
            ->assertDontSeeText('Nivell formatiu d’origen')
            ->assertSeeText('Nota:')
            ->assertSeeText('7')
            ->assertDontSee('7,00')
            ->assertDontSee('ordinària (FI)');

        $direccio = Profesor::query()->findOrFail('DIR00001');
        $this->actingAs($direccio, 'profesor');
        $detallDireccio = $this->get('/direccion/convalidacions/' . $sollicitud->id);
        $detallDireccio
            ->assertOk()
            ->assertSeeInOrder([
                'Mòdul que vol convalidar',
                'Estudis i documents aportats',
                'Estat i comentari',
                'Destí 1',
                'Cicle de matrícula:',
                '#2 · ACT',
                'Origen:',
                'Mòdul superat:',
                'Estat actual:',
                'Nou estat',
                'Comentari de Direcció',
            ])
            ->assertSeeText('Any d’aprovació:')
            ->assertSeeText('2025')
            ->assertSeeText('Cicle anterior')
            ->assertSeeText('Família professional')
            ->assertSeeText('Família professional: INFORMÀTICA I COMUNICACIONS')
            ->assertSeeText('Nivell: Grau Superior')
            ->assertSeeText('Família professional: Família professional')
            ->assertSeeText('Nivell: Cicle anterior')
            ->assertSeeText('Documentació revisada')
            ->assertDontSeeText('Departament #24')
            ->assertDontSeeText('ITACA 3306169525')
            ->assertDontSeeText('3162079904')
            ->assertDontSeeText('FAMILIA —')
            ->assertDontSeeText('ANT — Cicle anterior')
            ->assertDontSeeText('Familia profesional')
            ->assertDontSeeText('Nivell formatiu d’origen')
            ->assertSeeText('Cicle de matrícula: #2 · ACT — Cicle matriculat val / Ciclo matriculado cas')
            ->assertSeeText('Nota:')
            ->assertSeeText('7')
            ->assertDontSee('7,00')
            ->assertDontSee('ordinària (FI)');
    }

    /** Retorna un catàleg YAML coherent per a les proves d'aplicació automàtica. */
    private function automaticRulesYaml(): string
    {
        return <<<'YAML'
version: '0.4'
metadata:
  title: Regles de prova
convalidations:
  - id: regla-orig1-desti-auto
    target:
      code: DEST_AUTO
      name: Destí automàtic
      level: GS
    source:
      type: module
      code: ORIG1
      name: Origen de prova
    proposal:
      action: convalidate
    resolution:
      authority: centre
      automatic: false
    result:
      status: CO
      grade:
        mode: preserve
    legal_basis:
      - reference: art. 126.3.b
YAML;
    }

    /** Retorna l'identificador opac del primer resultat sintètic. */
    private function resultatId(): string
    {
        return app(ResultatsAcademicsXmlService::class)->aprovats('12345678')[0]['id'];
    }

    /** Retorna l'identificador opac del resultat amb el codi indicat. */
    private function idResultat(array $resultats, string $modul): string
    {
        foreach ($resultats as $resultat) {
            if ($resultat['modul'] === $modul) {
                return $resultat['id'];
            }
        }

        $this->fail('No s’ha trobat el mòdul d’origen ' . $modul . ' en les dades de prova.');
    }

    public function test_previsualitzacio_usa_les_dades_guardades_i_no_rellig_els_xml(): void
    {
        DB::table('modulos')->insert(['codigo' => 'DEST_AUTO', 'cliteral' => 'Destí automàtic', 'vliteral' => 'Destí automàtic']);
        DB::table('modulo_ciclos')->insert(['id' => 4, 'idModulo' => 'DEST_AUTO', 'idCiclo' => 2]);
        DB::table('modulo_grupos')->insert(['id' => 4, 'idGrupo' => 'ACTUAL', 'idModuloCiclo' => 4]);
        Storage::disk('convalidacions')->put('regles-automatiques/convalidacions.yaml', $this->automaticRulesYaml());
        $this->service->tramitar($this->alumno, 'sollicitud-sense-xml-en-preview', [[
            'modulo_destino_id' => 'DEST_AUTO',
            'origen' => Convalidacio::ORIGEN_PROPI_CENTRE,
            'resultat_origen_id' => $this->resultatId(),
        ]]);
        Storage::disk('convalidacions_xml')->delete('avaluacio-2025.xml');

        $preview = app(ConvalidacioAutomaticaService::class)->previsualitzar();

        $this->assertCount(1, $preview['casos']);
        $this->assertSame(1, $preview['regles'][0]['casos']);
    }

    /** XML sintètic que evita usar exportacions o dades personals reals. */
    private function academicXml(): string
    {
        return <<<'XML'
<?xml version="1.0"?>
<centro curso="2025">
  <cursos>
    <curso codigo="FAMILIA" padre=" " nombre_val="Família professional" nombre_cas="Familia profesional"/>
    <curso codigo="ANT" padre="FAMILIA" nombre_val="Cicle anterior" nombre_cas="Ciclo anterior"/>
    <curso codigo="ANT-1" padre="ANT" nombre_val="Primer" nombre_cas="Primero"/>
  </cursos>
  <contenidos>
    <contenido curso="ANT-1" codigo="ORIG1" nombre_val="Origen 1 val" nombre_cas="Origen 1 cas"/>
    <contenido curso="ANT-1" codigo="SUSPES" nombre_val="Mòdul suspés" nombre_cas="Módulo suspenso"/>
  </contenidos>
  <calificaciones>
    <calificacion alumno="12345678" curso="ANT-1" contenido="ORIG1" evaluacion="FI" nota_numerica="7"/>
    <calificacion alumno="12345678" curso="ANT-1" contenido="SUSPES" evaluacion="FI" nota_numerica="4"/>
    <calificacion alumno="87654321" curso="ANT-1" contenido="ORIG1" evaluacion="FI" nota_numerica="9"/>
  </calificaciones>
</centro>
XML;
    }

    private function createSchema(): void
    {
        Schema::create('alumnos', function (Blueprint $table) {
            $table->string('nia')->primary();
            $table->string('dni')->nullable();
            $table->string('nombre')->nullable();
            $table->string('apellido1')->nullable();
            $table->string('apellido2')->nullable();
            $table->string('email')->nullable();
            $table->string('password')->nullable();
            $table->unsignedBigInteger('rol')->default(1);
            $table->timestamps();
        });
        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('url')->default('');
            $table->string('class')->nullable();
            $table->unsignedBigInteger('rol')->default(1);
            $table->string('menu')->default('general');
            $table->string('submenu')->default('');
            $table->boolean('activo')->default(true);
            $table->integer('orden')->default(0);
            $table->string('ajuda')->default('');
        });
        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type');
            $table->string('notifiable_type');
            $table->string('notifiable_id');
            $table->text('data');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });
        Schema::create('settings', function (Blueprint $table) {
            $table->increments('id');
            $table->string('collection');
            $table->string('key');
            $table->text('value');
        });
        Schema::create('profesores', function (Blueprint $table) {
            $table->string('dni')->primary();
            $table->string('nombre')->nullable();
            $table->string('password')->nullable();
            $table->unsignedBigInteger('departamento')->nullable();
            $table->unsignedBigInteger('rol');
            $table->timestamps();
        });
        Schema::create('faltas_profesores', function (Blueprint $table) {
            $table->id();
            $table->string('idProfesor');
            $table->date('dia');
            $table->time('entrada')->nullable();
            $table->time('salida')->nullable();
        });
        Schema::create('grupos', function (Blueprint $table) {
            $table->string('codigo')->primary();
            $table->string('nombre')->nullable();
            $table->timestamps();
        });
        Schema::create('ciclos', function (Blueprint $table) {
            $table->increments('id');
            $table->string('ciclo')->nullable();
            $table->string('cliteral');
            $table->string('vliteral');
            $table->unsignedTinyInteger('departamento')->nullable();
            $table->unsignedTinyInteger('tipo')->default(2);
            $table->string('normativa', 10)->default('LFP');
        });
        Schema::create('departamentos', function (Blueprint $table) {
            $table->unsignedTinyInteger('id')->primary();
            $table->string('cliteral');
            $table->string('vliteral');
            $table->string('familia_professional_val')->nullable();
            $table->string('familia_professional_cas')->nullable();
            $table->string('codigo_xml', 50)->nullable();
            $table->string('abreviatura_xml', 50)->nullable();
        });
        Schema::create('alumnos_grupos', function (Blueprint $table) {
            $table->string('idAlumno');
            $table->string('idGrupo');
        });
        Schema::create('modulos', function (Blueprint $table) {
            $table->string('codigo')->primary();
            $table->string('cliteral');
            $table->string('vliteral');
        });
        Schema::create('modulo_ciclos', function (Blueprint $table) {
            $table->id();
            $table->string('idModulo');
            $table->unsignedInteger('idCiclo')->nullable();
        });
        Schema::create('modulo_grupos', function (Blueprint $table) {
            $table->id();
            $table->string('idGrupo');
            $table->unsignedBigInteger('idModuloCiclo');
        });
        Schema::create('alumno_resultados', function (Blueprint $table) {
            $table->id();
            $table->string('idAlumno');
            $table->unsignedBigInteger('idModuloGrupo');
            $table->integer('nota')->default(0);
        });
        Schema::create('sollicituds_convalidacions', function (Blueprint $table) {
            $table->id();
            $table->string('alumno_id');
            $table->string('submission_token');
            $table->timestamp('submitted_at');
            $table->timestamps();
            $table->unique(['alumno_id', 'submission_token']);
        });
        Schema::create('convalidacions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sollicitud_convalidacio_id');
            $table->string('modulo_destino_id');
            $table->string('origen');
            $table->boolean('acreditacio_alternativa')->default(false);
            $table->text('acreditacio_descripcio')->nullable();
            $table->string('modulo_origen_codigo')->nullable();
            $table->string('modulo_origen_nombre')->nullable();
            $table->string('modulo_origen_nombre_val')->nullable();
            $table->string('modulo_origen_nombre_cas')->nullable();
            $table->string('ciclo_origen_codigo')->nullable();
            $table->string('ciclo_origen_nombre')->nullable();
            $table->string('ciclo_origen_nombre_val')->nullable();
            $table->string('ciclo_origen_nombre_cas')->nullable();
            $table->string('familia_professional_codigo')->nullable();
            $table->string('familia_professional_nombre_val')->nullable();
            $table->string('familia_professional_nombre_cas')->nullable();
            $table->unsignedInteger('ciclo_matricula_id')->nullable();
            $table->string('ciclo_matricula_codigo', 50)->nullable();
            $table->string('ciclo_matricula_nombre_val')->nullable();
            $table->string('ciclo_matricula_nombre_cas')->nullable();
            $table->unsignedTinyInteger('departamento_matricula_id')->nullable();
            $table->string('familia_matricula_nombre_val')->nullable();
            $table->string('familia_matricula_nombre_cas')->nullable();
            $table->string('familia_matricula_codigo_xml', 50)->nullable();
            $table->string('familia_matricula_abreviatura_xml', 50)->nullable();
            $table->unsignedTinyInteger('ciclo_matricula_tipo')->nullable();
            $table->string('ciclo_matricula_tipo_nombre_val')->nullable();
            $table->string('ciclo_matricula_tipo_nombre_cas')->nullable();
            $table->string('ciclo_matricula_normativa', 20)->nullable();
            $table->string('nivel_origen_codigo', 50)->nullable();
            $table->string('nivel_origen_nombre_val')->nullable();
            $table->string('nivel_origen_nombre_cas')->nullable();
            $table->unsignedSmallInteger('any_origen')->nullable();
            $table->decimal('nota_origen', 5, 2)->nullable();
            $table->string('convocatoria_origen')->nullable();
            $table->string('document_path')->nullable();
            $table->string('document_original_name')->nullable();
            $table->string('document_mime')->nullable();
            $table->string('document_prl_path')->nullable();
            $table->string('document_prl_original_name')->nullable();
            $table->string('document_prl_mime')->nullable();
            $table->boolean('declaracio_responsable')->default(false);
            $table->boolean('fol_logse')->nullable();
            $table->boolean('modulo_origen_es_fol')->nullable();
            $table->string('fol_logse_cicle')->nullable();
            $table->string('fol_logse_nivell', 2)->nullable();
            $table->string('regla_automatica_id', 120)->nullable();
            $table->string('regla_automatica_version', 40)->nullable();
            $table->string('regla_automatica_hash', 64)->nullable();
            $table->longText('regla_automatica_snapshot')->nullable();
            $table->longText('evidencia_automatica_snapshot')->nullable();
            $table->longText('base_normativa_automatica')->nullable();
            $table->string('resultat_automatic', 2)->nullable();
            $table->string('mode_nota_automatic', 20)->nullable();
            $table->decimal('nota_resultat_automatic', 5, 2)->nullable();
            $table->string('resultat_resolucio', 2)->nullable();
            $table->string('mode_nota_resolucio', 20)->nullable();
            $table->decimal('nota_resultat_resolucio', 5, 2)->nullable();
            $table->string('estat');
            $table->text('observacions')->nullable();
            $table->string('revisat_per')->nullable();
            $table->timestamp('revisat_at')->nullable();
            $table->timestamp('estat_canviat_at')->nullable();
            $table->timestamps();
            $table->unique(['sollicitud_convalidacio_id', 'modulo_destino_id']);
        });
        Schema::create('convalidacions_moduls_fol_logse', function (Blueprint $table) {
            $table->string('codigo', 6)->primary();
            $table->string('modul');
            $table->string('cicle');
            $table->string('nivell', 2);
            $table->string('sistema', 10);
        });
        DB::table('convalidacions_moduls_fol_logse')->insert([
            ['codigo' => '009001', 'modul' => 'Formació i orientació laboral', 'cicle' => 'Desenvolupament d\'Aplicacions Informàtiques', 'nivell' => 'GS', 'sistema' => 'LOGSE'],
            ['codigo' => '028001', 'modul' => 'Formació i orientació laboral', 'cicle' => 'Cures Auxiliars d\'Infermeria', 'nivell' => 'GM', 'sistema' => 'LOGSE'],
        ]);
        Schema::create('convalidacions_moduls_fol_loe', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 30)->unique();
            $table->string('codigo_normalitzat', 30)->unique();
            $table->string('cicle_cas', 255);
            $table->string('cicle_val', 255);
            $table->string('nivell', 2);
            $table->timestamps();
        });
        Schema::create('convalidacions_correspondencies_angles', function (Blueprint $table) {
            $table->id();
            $table->string('codi_cicle_angles', 50);
            $table->string('nom_cicle_angles_val', 255);
            $table->string('nom_cicle_angles_cas', 255);
            $table->string('codi_cicle_contenidor', 50);
            $table->string('nom_cicle_contenidor_val', 255);
            $table->string('nom_cicle_contenidor_cas', 255);
            $table->boolean('es_grau_superior');
            $table->timestamps();
            $table->unique(['codi_cicle_angles', 'codi_cicle_contenidor']);
        });
        Schema::create('documents_convalidacions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('convalidacio_id');
            $table->string('descripcio', 120);
            $table->string('path');
            $table->string('original_name');
            $table->string('mime', 100);
            $table->timestamps();
        });
    }

    private function seedAcademicData(): void
    {
        DB::table('alumnos')->insert([
            ['nia' => '12345678', 'dni' => '11111111A', 'email' => null, 'rol' => config('roles.rol.alumno'), 'created_at' => now(), 'updated_at' => now()],
            ['nia' => '87654321', 'dni' => '22222222B', 'email' => null, 'rol' => config('roles.rol.alumno'), 'created_at' => now(), 'updated_at' => now()],
        ]);
        DB::table('profesores')->insert([
            'dni' => 'DIR00001',
            'nombre' => 'Direcció',
            'rol' => (int) config('roles.rol.profesor') * (int) config('roles.rol.direccion'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('grupos')->insert([['codigo' => 'ACTUAL', 'nombre' => 'Actual'], ['codigo' => 'ANTERIOR', 'nombre' => 'Anterior']]);
        DB::table('departamentos')->insert([
            ['id' => 6, 'cliteral' => 'Departamento sanitario', 'vliteral' => 'Departament sanitari', 'familia_professional_val' => 'SANITAT', 'familia_professional_cas' => 'SANIDAD', 'codigo_xml' => '3306170441', 'abreviatura_xml' => '061'],
            ['id' => 24, 'cliteral' => 'Departamento informática', 'vliteral' => 'Departament informàtica', 'familia_professional_val' => 'INFORMÀTICA I COMUNICACIONS', 'familia_professional_cas' => 'INFORMÁTICA Y COMUNICACIONES', 'codigo_xml' => '3306169525', 'abreviatura_xml' => '190'],
        ]);
        DB::table('ciclos')->insert([
            ['id' => 1, 'ciclo' => 'ANT', 'cliteral' => 'Cicle anterior', 'vliteral' => 'Cicle anterior', 'departamento' => 6, 'tipo' => 1, 'normativa' => 'LOE'],
            ['id' => 2, 'ciclo' => 'ACT', 'cliteral' => 'Ciclo matriculado cas', 'vliteral' => 'Cicle matriculat val', 'departamento' => 24, 'tipo' => 2, 'normativa' => 'LFP'],
        ]);
        DB::table('alumnos_grupos')->insert(['idAlumno' => '12345678', 'idGrupo' => 'ACTUAL']);
        DB::table('modulos')->insert([
            ['codigo' => 'DEST1', 'cliteral' => 'Destí 1', 'vliteral' => 'Destí 1'],
            ['codigo' => 'DEST2', 'cliteral' => 'Destí 2', 'vliteral' => 'Destí 2'],
            ['codigo' => 'ORIG1', 'cliteral' => 'Origen 1', 'vliteral' => 'Origen 1'],
            ['codigo' => 'ALIEN', 'cliteral' => 'Alié', 'vliteral' => 'Alié'],
        ]);
        DB::table('modulo_ciclos')->insert([
            ['id' => 1, 'idModulo' => 'DEST1', 'idCiclo' => 2],
            ['id' => 2, 'idModulo' => 'DEST2', 'idCiclo' => 2],
            ['id' => 3, 'idModulo' => 'ORIG1', 'idCiclo' => 1],
        ]);
        DB::table('modulo_grupos')->insert([
            ['id' => 1, 'idGrupo' => 'ACTUAL', 'idModuloCiclo' => 1],
            ['id' => 2, 'idGrupo' => 'ACTUAL', 'idModuloCiclo' => 2],
            ['id' => 3, 'idGrupo' => 'ANTERIOR', 'idModuloCiclo' => 3],
        ]);
        DB::table('alumno_resultados')->insert(['idAlumno' => '12345678', 'idModuloGrupo' => 3, 'nota' => 5]);
    }
}
