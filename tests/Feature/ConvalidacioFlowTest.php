<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Intranet\Application\Convalidacio\ConvalidacioException;
use Intranet\Application\Convalidacio\ConvalidacioService;
use Intranet\Entities\Alumno;
use Intranet\Entities\Convalidacio;
use Intranet\Entities\Profesor;
use Intranet\Entities\SollicitudConvalidacio;
use Intranet\Http\Middleware\RoleMiddleware;
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
            ['modulo_destino_id' => 'DEST1', 'origen' => Convalidacio::ORIGEN_PROPI_CENTRE, 'ciclo_origen_id' => 1],
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
        $this->assertSame([Convalidacio::ESTAT_EN_PROCES], Convalidacio::query()->distinct()->pluck('estat')->all());
        $this->assertSame(1, Convalidacio::query()->where('modulo_destino_id', 'DEST1')->value('ciclo_origen_id'));
        $externa = Convalidacio::query()->where('origen', Convalidacio::ORIGEN_ALTRE_CENTRE)->firstOrFail();
        Storage::disk('convalidacions')->assertExists($externa->document_path);
        $this->assertSame('certificat.pdf', $externa->document_original_name);
    }

    public function test_rebutja_destins_duplicats_ids_manipulats_i_externs_sense_document(): void
    {
        foreach ([
            [
                ['modulo_destino_id' => 'DEST1', 'origen' => Convalidacio::ORIGEN_PROPI_CENTRE, 'ciclo_origen_id' => 1],
                ['modulo_destino_id' => 'DEST1', 'origen' => Convalidacio::ORIGEN_PROPI_CENTRE, 'ciclo_origen_id' => 1],
            ],
            [['modulo_destino_id' => 'ALIEN', 'origen' => Convalidacio::ORIGEN_PROPI_CENTRE, 'ciclo_origen_id' => 1]],
            [['modulo_destino_id' => 'DEST1', 'origen' => Convalidacio::ORIGEN_PROPI_CENTRE, 'ciclo_origen_id' => 999]],
            [['modulo_destino_id' => 'DEST1', 'origen' => Convalidacio::ORIGEN_EOI, 'declaracio_responsable' => true]],
        ] as $index => $items) {
            try {
                $this->service->tramitar($this->alumno, 'invalid-' . $index, $items);
                $this->fail('La composició invàlida havia de ser rebutjada.');
            } catch (ConvalidacioException) {
                $this->assertSame(0, SollicitudConvalidacio::query()->count());
            }
        }
    }

    public function test_direccio_revisa_una_peticio_sense_alterar_les_altres_i_realitzada_es_terminal(): void
    {
        $sollicitud = $this->service->tramitar($this->alumno, 'review', [
            ['modulo_destino_id' => 'DEST1', 'origen' => Convalidacio::ORIGEN_PROPI_CENTRE, 'ciclo_origen_id' => 1],
            ['modulo_destino_id' => 'DEST2', 'origen' => Convalidacio::ORIGEN_PROPI_CENTRE, 'ciclo_origen_id' => 1],
        ]);
        $direccio = Profesor::query()->findOrFail('DIR00001');
        $primera = $sollicitud->convalidacions[0];
        $segona = $sollicitud->convalidacions[1];

        $this->expectException(ConvalidacioException::class);
        try {
            $this->service->revisar($primera, $direccio, Convalidacio::ESTAT_DENEGADA, null);
        } finally {
            $this->service->revisar($primera, $direccio, Convalidacio::ESTAT_REALITZADA, null);
            $this->assertSame(Convalidacio::ESTAT_EN_PROCES, $segona->fresh()->estat);
            $this->assertSame('DIR00001', $primera->fresh()->revisat_per);
            try {
                $this->service->revisar($primera->fresh(), $direccio, Convalidacio::ESTAT_DENEGADA, 'Canvi');
                $this->fail('Una petició terminal no ha de poder canviar.');
            } catch (ConvalidacioException) {
                $this->assertTrue(true);
            }
        }
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
            'modulo_destino_id' => 'DEST1', 'origen' => Convalidacio::ORIGEN_PROPI_CENTRE, 'ciclo_origen_id' => 1,
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

    public function test_una_peticio_no_denegada_bloqueja_el_modul_i_denegada_el_torna_a_habilitar(): void
    {
        $sollicitud = $this->service->tramitar($this->alumno, 'blocking', [[
            'modulo_destino_id' => 'DEST1',
            'origen' => Convalidacio::ORIGEN_PROPI_CENTRE,
            'ciclo_origen_id' => 1,
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
                    'ciclo_origen_id' => 1,
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
            'ciclo_origen_id' => 1,
        ]]);
        $this->assertNotSame($sollicitud->id, $nova->id);
    }

    public function test_formulari_i_tramitacio_http_usen_el_guard_d_alumnat(): void
    {
        $this->withoutMiddleware(RoleMiddleware::class)->actingAs($this->alumno, 'alumno');
        auth()->shouldUse('profesor');

        $this->get('/alumno/convalidacions/create')
            ->assertOk()
            ->assertSee('Nova sol·licitud de convalidació')
            ->assertSee('Destí 1')
            ->assertSee('Estudi previ')
            ->assertSee('Cicle anterior')
            ->assertSee('Estudis o certificats acadèmics d&#039;un altre centre', false)
            ->assertSee('id="origen-group" class="mb-3" hidden', false)
            ->assertSee('id="convalidacio-layout"', false)
            ->assertSee('id="resum-count"', false)
            ->assertSee('Afegir a la sol·licitud')
            ->assertSee('convalidacio-item-afegit')
            ->assertSee('el resum conserva els anteriors');

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
    }

    public function test_controlador_denega_a_un_alumne_la_sollicitud_d_un_altre(): void
    {
        $sollicitud = $this->service->tramitar($this->alumno, 'private', [[
            'modulo_destino_id' => 'DEST1', 'origen' => Convalidacio::ORIGEN_PROPI_CENTRE, 'ciclo_origen_id' => 1,
        ]]);
        $altre = Alumno::query()->findOrFail('87654321');

        $this->withoutMiddleware(RoleMiddleware::class)
            ->actingAs($altre, 'alumno');
        auth()->shouldUse('profesor');

        $this->get('/alumno/convalidacions/' . $sollicitud->id)->assertForbidden();
    }

    private function createSchema(): void
    {
        Schema::create('alumnos', function (Blueprint $table) {
            $table->string('nia')->primary();
            $table->string('dni')->nullable();
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
        Schema::create('profesores', function (Blueprint $table) {
            $table->string('dni')->primary();
            $table->string('nombre')->nullable();
            $table->string('password')->nullable();
            $table->unsignedBigInteger('rol');
            $table->timestamps();
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
            $table->unsignedInteger('ciclo_origen_id')->nullable();
            $table->string('document_path')->nullable();
            $table->string('document_original_name')->nullable();
            $table->string('document_mime')->nullable();
            $table->boolean('declaracio_responsable')->default(false);
            $table->string('estat');
            $table->text('observacions')->nullable();
            $table->string('revisat_per')->nullable();
            $table->timestamp('revisat_at')->nullable();
            $table->timestamps();
            $table->unique(['sollicitud_convalidacio_id', 'modulo_destino_id']);
        });
    }

    private function seedAcademicData(): void
    {
        DB::table('alumnos')->insert([
            ['nia' => '12345678', 'dni' => '11111111A', 'rol' => config('roles.rol.alumno'), 'created_at' => now(), 'updated_at' => now()],
            ['nia' => '87654321', 'dni' => '22222222B', 'rol' => config('roles.rol.alumno'), 'created_at' => now(), 'updated_at' => now()],
        ]);
        DB::table('profesores')->insert([
            'dni' => 'DIR00001',
            'nombre' => 'Direcció',
            'rol' => (int) config('roles.rol.profesor') * (int) config('roles.rol.direccion'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('grupos')->insert([['codigo' => 'ACTUAL', 'nombre' => 'Actual'], ['codigo' => 'ANTERIOR', 'nombre' => 'Anterior']]);
        DB::table('ciclos')->insert([
            ['id' => 1, 'ciclo' => 'ANT', 'cliteral' => 'Cicle anterior', 'vliteral' => 'Cicle anterior'],
            ['id' => 2, 'ciclo' => 'ACT', 'cliteral' => 'Cicle actual', 'vliteral' => 'Cicle actual'],
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
