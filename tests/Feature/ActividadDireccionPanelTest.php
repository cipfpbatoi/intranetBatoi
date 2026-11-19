<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Intranet\Entities\Actividad;
use Intranet\Entities\Profesor;
use Intranet\Livewire\ActividadDireccionPanel;
use Intranet\Services\Document\PdfService;
use Intranet\Services\General\AutorizacionPrintService;
use Intranet\Services\School\ActividadItacaPrintService;
use Livewire\Livewire;
use Mockery;
use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use RuntimeException;
use Tests\TestCase;

/**
 * Prova el panell de Direcció i els lots d'impressió d'autorització i ITACA.
 */
class ActividadDireccionPanelTest extends TestCase
{
    private string $sqlitePath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sqlitePath = storage_path('actividad_direccion_panel_testing.sqlite');
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
        $this->seedBaseData();
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        Schema::connection('sqlite')->dropIfExists('activities');
        Schema::connection('sqlite')->dropIfExists('documentos');
        Schema::connection('sqlite')->dropIfExists('horarios');
        Schema::connection('sqlite')->dropIfExists('horas');
        Schema::connection('sqlite')->dropIfExists('actividad_grupo');
        Schema::connection('sqlite')->dropIfExists('grupos');
        Schema::connection('sqlite')->dropIfExists('actividad_profesor');
        Schema::connection('sqlite')->dropIfExists('tipo_actividad');
        Schema::connection('sqlite')->dropIfExists('departamentos');
        Schema::connection('sqlite')->dropIfExists('actividades');
        Schema::connection('sqlite')->dropIfExists('profesores');

        if (file_exists($this->sqlitePath)) {
            @unlink($this->sqlitePath);
        }

        parent::tearDown();
    }

    public function test_renderitza_llistat_i_textos_clau(): void
    {
        $component = Livewire::actingAs($this->direccionUser(), 'profesor')
            ->test(ActividadDireccionPanel::class);

        $component
            ->assertDontSee('Pilot funcional')
            ->assertSee('Imprimir activitats autoritzades (1)')
            ->assertSee('Autoritzar activitats pendents (1)')
            ->assertSee('Jordi Marti Perez')
            ->assertSee('Visita museu');

        $actividades = $component->get('actividades');

        $this->assertCount(4, $actividades);
        $this->assertTrue(collect($actividades)->contains(fn (array $actividad): bool => (bool) $actividad['hasDocument']));
    }

    public function test_filtra_per_professor_nom_d_activitat_estat_i_departament(): void
    {
        $component = Livewire::actingAs($this->direccionUser(), 'profesor')
            ->test(ActividadDireccionPanel::class);

        $component->set('filterProfessor', 'Jordi');

        $actividades = $component->get('actividades');
        $this->assertCount(2, $actividades);

        $component->set('filterProfessor', '');
        $component->set('filterEstat', '3');

        $actividades = $component->get('actividades');
        $this->assertCount(1, $actividades);
        $this->assertSame(3, $actividades[0]['estado']);

        $component->set('filterEstat', '');
        $component->set('filterProfessor', 'museu');

        $actividades = $component->get('actividades');
        $this->assertCount(1, $actividades);
        $this->assertSame('Visita museu', $actividades[0]['name']);

        $component->set('filterProfessor', '');
        $component->set('filterDepartament', '11');

        $actividades = $component->get('actividades');
        $this->assertCount(1, $actividades);
        $this->assertSame(3, $actividades[0]['id']);
    }

    public function test_acceptar_i_desautoritzar_actualitzen_estat(): void
    {
        $component = Livewire::actingAs($this->direccionUser(), 'profesor')
            ->test(ActividadDireccionPanel::class);

        $component->call('acceptar', 1)
            ->assertSet('error', '')
            ->assertSet('message', 'Activitat autoritzada correctament.');

        $this->assertSame(2, (int) DB::connection('sqlite')->table('actividades')->where('id', 1)->value('estado'));

        $component->call('desautoritzar', 3)
            ->assertSet('error', '')
            ->assertSet('message', 'Activitat retornada a l\'estat anterior.');

        $this->assertSame(2, (int) DB::connection('sqlite')->table('actividades')->where('id', 3)->value('estado'));
    }

    public function test_marcar_itaca_actualitza_estat(): void
    {
        $component = Livewire::actingAs($this->direccionUser(), 'profesor')
            ->test(ActividadDireccionPanel::class);

        $component->call('marcarItaca', 4)
            ->assertSet('error', '')
            ->assertSet('message', 'Activitat marcada com a tramitada en ITACA.');

        $this->assertSame(5, (int) DB::connection('sqlite')->table('actividades')->where('id', 4)->value('estado'));
    }

    public function test_mostrar_carrega_activitat_seleccionada(): void
    {
        $component = Livewire::actingAs($this->direccionUser(), 'profesor')
            ->test(ActividadDireccionPanel::class);

        $component->call('mostrar', 2)
            ->assertSet('selectedActividad.id', 2)
            ->assertSet('selectedActividad.name', 'Jornada convivencia')
            ->assertSet('selectedActividad.coordinador', 'Jordi Marti Perez');

        $selected = $component->get('selectedActividad');

        $this->assertSame('Memòria activitat', $selected['objetivos']);
        $this->assertSame("Activitat per a consolidar aprenentatges.", $selected['justificacioRa']);
        $this->assertSame(['2CFMLFPJ', '1CFMLFPG'], $selected['participants'][0]['grupsAfectats']);
        $this->assertSame(['1r CFGM Estètica'], $selected['grups']);
    }

    public function test_mostra_els_comentaris_del_professor_abans_d_autoritzar(): void
    {
        $component = Livewire::actingAs($this->direccionUser(), 'profesor')
            ->test(ActividadDireccionPanel::class)
            ->call('mostrar', 1)
            ->assertSet('selectedActividad.comentarios', 'Cal confirmar el nombre final d\'assistents.')
            ->assertSee('Comentaris:')
            ->assertSee('Cal confirmar el nombre final d\'assistents.');

        $this->assertSame(1, (int) DB::connection('sqlite')->table('actividades')->where('id', 1)->value('estado'));
    }

    public function test_mostra_un_guio_quan_l_activitat_no_te_comentaris(): void
    {
        Livewire::actingAs($this->direccionUser(), 'profesor')
            ->test(ActividadDireccionPanel::class)
            ->call('mostrar', 2)
            ->assertSet('selectedActividad.comentarios', '')
            ->assertSeeHtml('<strong>Comentaris:</strong> -');
    }

    public function test_mostra_tipus_complementaria_i_extraescolar_en_llistat_i_detall(): void
    {
        $component = Livewire::actingAs($this->direccionUser(), 'profesor')
            ->test(ActividadDireccionPanel::class)
            ->assertSee('Tipus')
            ->assertSee('Complementària')
            ->assertSee('Extraescolar');

        $actividades = collect($component->get('actividades'))->keyBy('id');

        $this->assertSame('Complementària', $actividades[1]['tipo']);
        $this->assertSame('Extraescolar', $actividades[4]['tipo']);

        $component
            ->call('mostrar', 4)
            ->assertSet('selectedActividad.tipo', 'Extraescolar')
            ->assertDontSee('Justificació RA');
    }

    public function test_mostra_ubicacio_en_el_llistat_i_el_detall(): void
    {
        DB::connection('sqlite')->table('actividades')->where('id', 1)->update([
            'fueraCentro' => 0,
            'transport' => 0,
        ]);
        DB::connection('sqlite')->table('actividades')->where('id', 2)->update([
            'fueraCentro' => 1,
            'transport' => 1,
        ]);

        $component = Livewire::actingAs($this->direccionUser(), 'profesor')
            ->test(ActividadDireccionPanel::class)
            ->assertSee('Ubicació')
            ->assertSee('Dins del centre')
            ->assertSee('Fora del centre amb transport')
            ->assertSee('Fora del centre sense transport');

        $actividades = collect($component->get('actividades'))->keyBy('id');

        $this->assertSame('Dins del centre', $actividades[1]['ubicacio']);
        $this->assertSame('Fora del centre amb transport', $actividades[2]['ubicacio']);
        $this->assertSame('Fora del centre sense transport', $actividades[3]['ubicacio']);

        $component->call('mostrar', 1)
            ->assertSet('selectedActividad.ubicacio', 'Dins del centre');
    }

    public function test_mostrar_usa_el_pivot_per_a_identificar_el_coordinador(): void
    {
        DB::connection('sqlite')->table('profesores')->insert([
            'dni' => 'ACT050',
            'nombre' => 'Marta',
            'apellido1' => 'Andreu',
            'apellido2' => 'Gil',
            'email' => 'act050@test.local',
            'rol' => config('roles.rol.profesor'),
            'activo' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::connection('sqlite')->table('actividades')->insert([
            'id' => 5,
            'name' => 'Taller valorat',
            'tipo_actividad_id' => 7,
            'descripcion' => 'Activitat amb diversos participants',
            'extraescolar' => 1,
            'desde' => '2026-03-25 09:00:00',
            'hasta' => '2026-03-25 12:00:00',
            'complementaria' => 0,
            'fueraCentro' => 1,
            'transport' => 0,
            'objetivos' => null,
            'idDocumento' => null,
            'estado' => 4,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::connection('sqlite')->table('actividad_profesor')->insert([
            ['idActividad' => 5, 'idProfesor' => 'ACT050', 'coordinador' => 0],
            ['idActividad' => 5, 'idProfesor' => 'ACT200', 'coordinador' => 1],
        ]);

        Livewire::actingAs($this->direccionUser(), 'profesor')
            ->test(ActividadDireccionPanel::class)
            ->call('mostrar', 5)
            ->assertSet('selectedActividad.coordinador', 'Anna Soler Lopez');
    }

    /**
     * El recompte i el botó només inclouen valorades que ja han finalitzat.
     */
    public function test_boto_itaca_nomes_actiu_amb_valorades_finalitzades(): void
    {
        Carbon::setTestNow('2026-03-05 12:30:00');
        $component = Livewire::actingAs($this->direccionUser(), 'profesor')
            ->test(ActividadDireccionPanel::class)
            ->assertSet('pendingItacaCount', 0);

        $html = $component->html();
        $this->assertMatchesRegularExpression('/data-bulk-action="print"\s+disabled/', $html);

        Carbon::setTestNow('2026-03-05 12:30:01');
        $component->set('filterProfessor', 'Cap coincidència')
            ->assertSet('pendingItacaCount', 1);
        $this->assertDoesNotMatchRegularExpression('/data-bulk-action="print"\s+disabled/', $component->html());
    }

    /**
     * La descàrrega inclou la categoria i el coordinador real, i només marca el lot.
     */
    public function test_llistat_itaca_selecciona_finalitzades_i_conserva_data_autoritzacio(): void
    {
        Carbon::setTestNow('2026-03-20 12:00:00');
        $this->actingAs($this->direccionUser(), 'profesor');
        DB::table('actividades')->where('id', 4)->update(['fecha_impresion' => '2026-03-01', 'idDocumento' => 99]);
        DB::table('actividad_profesor')->insert(['idActividad' => 4, 'idProfesor' => 'ACT100', 'coordinador' => 0]);
        DB::table('actividad_grupo')->insert(['idActividad' => 4, 'idGrupo' => 'GRP001']);
        DB::table('actividades')->where('id', 2)->update(['estado' => 4]);

        $document = Mockery::mock();
        $document->shouldReceive('download')->once()->with('activitats-itaca.pdf')
            ->andReturnUsing(function (): Response {
                $this->assertSame(4, (int) DB::table('actividades')->where('id', 4)->value('estado'));

                return new Response('pdf-itaca');
            });
        $pdf = Mockery::mock(PdfService::class);
        $pdf->shouldReceive('hazPdf')->once()->withArgs(function ($vista, $todos, $datos, $orientacion): bool {
            $this->assertSame('pdf.actividadesItaca', $vista);
            $this->assertSame([4], $todos->modelKeys());
            $this->assertSame('landscape', $orientacion);
            $html = view($vista, ['todos' => $todos, 'datosInforme' => '20-03-2026'])->render();
            $this->assertStringContainsString('Eixida cultural', $html);
            $this->assertStringContainsString('Anna Soler Lopez', $html);
            $this->assertMatchesRegularExpression('/<td>Anna Soler Lopez<\/td>/', $html);
            $this->assertStringContainsString('Jordi Marti Perez', $html);
            $this->assertStringContainsString('1r CFGM Estètica', $html);
            $this->assertStringContainsString('01-03-2026', $html);
            $this->assertStringContainsString('Fora del centre amb transport', $html);
            $this->assertStringNotContainsString('Jornada convivencia', $html);

            return true;
        })->andReturn($document);
        $this->app->instance(PdfService::class, $pdf);

        $this->post(route('actividad.direccion.itaca.pdf'))->assertOk()->assertSee('pdf-itaca');

        $this->assertSame(5, (int) DB::table('actividades')->where('id', 4)->value('estado'));
        $this->assertSame('2026-03-01', DB::table('actividades')->where('id', 4)->value('fecha_impresion'));
        $this->assertSame(99, (int) DB::table('actividades')->where('id', 4)->value('idDocumento'));
        $this->assertSame(4, (int) DB::table('actividades')->where('id', 2)->value('estado'));
        $this->assertSame(3, (int) DB::table('actividades')->where('id', 3)->value('estado'));
        $this->assertSame(1, (int) DB::table('actividades')->where('id', 1)->value('estado'));

        $this->post(route('actividad.direccion.itaca.pdf'))->assertRedirect();
    }

    /**
     * Les dades antigues sense data o coordinador es representen sense inventar-les.
     */
    public function test_pdf_itaca_mostra_dades_absents_sense_inferir_coordinador(): void
    {
        DB::table('actividad_profesor')->where('idActividad', 4)->update(['coordinador' => 0]);
        DB::table('actividades')->where('id', 4)->update(['tipo_actividad_id' => null]);
        $actividad = Actividad::with(['profesores', 'grupos', 'tipoActividad'])->findOrFail(4);

        $html = view('pdf.actividadesItaca', ['todos' => collect([$actividad]), 'datosInforme' => '20-03-2026'])->render();

        $this->assertSame(2, substr_count($html, '<td>Sense assignar</td>'));
        $this->assertStringContainsString('<td>Sense data</td>', $html);
    }

    /**
     * Un error en generar el document no modifica cap activitat.
     */
    public function test_error_de_pdf_itaca_conserva_estat_i_data(): void
    {
        Carbon::setTestNow('2026-03-20 12:00:00');
        $document = Mockery::mock();
        $document->shouldReceive('download')->once()->andThrow(new RuntimeException('Error PDF de prova'));
        $pdf = Mockery::mock(PdfService::class);
        $pdf->shouldReceive('hazPdf')->once()->andReturn($document);

        try {
            (new ActividadItacaPrintService($pdf))->imprimir();
            $this->fail('La generació havia de fallar.');
        } catch (RuntimeException $exception) {
            $this->assertSame('Error PDF de prova', $exception->getMessage());
        }

        $this->assertSame(4, (int) DB::table('actividades')->where('id', 4)->value('estado'));
        $this->assertNull(DB::table('actividades')->where('id', 4)->value('fecha_impresion'));
    }

    /**
     * Els estats ja tramitats, les activitats futures i les alienes al flux no entren.
     */
    public function test_seleccio_itaca_exclou_altres_estats_i_activitats_no_extraescolars(): void
    {
        Carbon::setTestNow('2026-03-20 12:00:00');

        foreach ([0, 1, 2, 3, 5] as $estado) {
            DB::table('actividades')->where('id', 4)->update(['estado' => $estado]);
            $this->assertSame(0, Actividad::pendientesItaca()->count());
        }

        DB::table('actividades')->where('id', 4)->update(['estado' => 4, 'extraescolar' => 0]);
        $this->assertSame(0, Actividad::pendientesItaca()->count());
        DB::table('actividades')->where('id', 4)->update(['extraescolar' => 1]);
        $this->assertSame([4], Actividad::pendientesItaca()->get()->modelKeys());
    }

    /**
     * La ruta torna sense PDF ni canvis si s'esgota el lot abans de prémer el botó.
     */
    public function test_llistat_itaca_buit_no_genera_pdf_ni_modifica_activitats(): void
    {
        Carbon::setTestNow('2026-03-01 12:00:00');
        $this->actingAs($this->direccionUser(), 'profesor');
        $pdf = Mockery::mock(PdfService::class);
        $pdf->shouldNotReceive('hazPdf');
        $this->app->instance(PdfService::class, $pdf);

        $this->post(route('actividad.direccion.itaca.pdf'))->assertRedirect()
            ->assertSessionHas('info', __('models.Actividad.itaca_empty'));
        $this->assertSame(4, (int) DB::table('actividades')->where('id', 4)->value('estado'));
    }

    /**
     * Només Direcció pot executar la ruta que genera el lot.
     */
    public function test_professor_sense_rol_de_direccio_no_pot_imprimir_itaca(): void
    {
        $this->actingAs(Profesor::findOrFail('ACT100'), 'profesor');
        $service = Mockery::mock(ActividadItacaPrintService::class);
        $service->shouldNotReceive('imprimir');
        $this->app->instance(ActividadItacaPrintService::class, $service);

        $this->post(route('actividad.direccion.itaca.pdf'))->assertForbidden();
    }

    /**
     * L'autorització impresa desa la data del lot i l'actualitza en una reimpressió.
     * S'aïlla el gestor real dels tests que el substituïxen amb Mockery overload.
     */
    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function test_impressio_autoritzades_guarda_data_en_activitat(): void
    {
        Carbon::setTestNow('2026-03-20 12:00:00');
        $this->actingAs($this->direccionUser(), 'profesor');
        config(['modelos.Actividad.avisos' => []]);
        $this->createDocumentSchema();
        DB::table('actividades')->where('id', 2)->update(['fecha_impresion' => '2026-02-01']);
        DB::table('actividades')->where('id', 1)->update(['estado' => 2]);

        $document = Mockery::mock();
        $document->shouldReceive('save')->once()->andReturnSelf();
        $document->shouldReceive('download')->once()->andReturn(new Response('pdf-autoritzacio'));
        $pdf = Mockery::mock(PdfService::class);
        $pdf->shouldReceive('hazPdf')->once()->andReturn($document);

        (new AutorizacionPrintService($pdf))->imprimir(Actividad::class, 'Actividad', 'extraescolars');

        $this->assertSame('2026-03-20', DB::table('actividades')->where('id', 2)->value('fecha_impresion'));
        $this->assertSame(3, (int) DB::table('actividades')->where('id', 2)->value('estado'));
        $this->assertNotNull(DB::table('actividades')->where('id', 2)->value('idDocumento'));
        $this->assertSame('2026-03-20', DB::table('actividades')->where('id', 1)->value('fecha_impresion'));
        $this->assertNull(DB::table('actividades')->where('id', 3)->value('fecha_impresion'));
    }

    /**
     * Si no es pot generar l'autorització no es registra una impressió fictícia.
     */
    public function test_error_impressio_autoritzades_no_guarda_data_ni_canvia_estat(): void
    {
        $document = Mockery::mock();
        $document->shouldReceive('save')->once()->andThrow(new RuntimeException('Error PDF de prova'));
        $pdf = Mockery::mock(PdfService::class);
        $pdf->shouldReceive('hazPdf')->once()->andReturn($document);

        try {
            (new AutorizacionPrintService($pdf))->imprimir(Actividad::class, 'Actividad', 'extraescolars');
            $this->fail('La generació havia de fallar.');
        } catch (RuntimeException $exception) {
            $this->assertSame('Error PDF de prova', $exception->getMessage());
        }

        $this->assertSame(2, (int) DB::table('actividades')->where('id', 2)->value('estado'));
        $this->assertNull(DB::table('actividades')->where('id', 2)->value('fecha_impresion'));
    }

    /**
     * Prepara el gestor documental només per a les proves d'autorització impresa.
     */
    private function createDocumentSchema(): void
    {
        Schema::create('documentos', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('tipoDocumento')->nullable();
            $table->string('fichero');
            $table->text('tags')->nullable();
            $table->string('curso');
            $table->text('descripcion');
            $table->string('propietario');
            $table->string('supervisor');
            $table->integer('rol');
            $table->timestamps();
        });
    }

    private function direccionUser(): Profesor
    {
        return Profesor::on('sqlite')->findOrFail('DIR001');
    }

    private function createSchema(): void
    {
        Schema::connection('sqlite')->create('profesores', function (Blueprint $table): void {
            $table->string('dni', 10)->primary();
            $table->string('nombre')->nullable();
            $table->string('apellido1')->nullable();
            $table->string('apellido2')->nullable();
            $table->string('email')->nullable();
            $table->unsignedInteger('rol')->default(config('roles.rol.profesor'));
            $table->boolean('activo')->default(true);
            $table->date('fecha_baja')->nullable();
            $table->timestamps();
        });

        Schema::connection('sqlite')->create('actividades', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->unsignedInteger('tipo_actividad_id')->nullable();
            $table->boolean('extraescolar')->default(true);
            $table->dateTime('desde')->nullable();
            $table->dateTime('hasta')->nullable();
            $table->date('fecha_impresion')->nullable();
            $table->boolean('complementaria')->default(true);
            $table->boolean('fueraCentro')->default(true);
            $table->boolean('transport')->default(false);
            $table->text('objetivos')->nullable();
            $table->text('descripcion')->nullable();
            $table->text('comentarios')->nullable();
            $table->boolean('poll')->default(false);
            $table->text('desenvolupament')->nullable();
            $table->text('valoracio')->nullable();
            $table->text('aspectes')->nullable();
            $table->text('dades')->nullable();
            $table->unsignedBigInteger('idDocumento')->nullable();
            $table->tinyInteger('estado')->default(0);
            $table->timestamps();
        });

        Schema::connection('sqlite')->create('grupos', function (Blueprint $table): void {
            $table->string('codigo', 10)->primary();
            $table->string('nombre')->nullable();
            $table->timestamps();
        });

        Schema::connection('sqlite')->create('actividad_grupo', function (Blueprint $table): void {
            $table->unsignedInteger('idActividad');
            $table->string('idGrupo', 10);
        });

        Schema::connection('sqlite')->create('departamentos', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('cliteral')->nullable();
            $table->string('vliteral')->nullable();
            $table->unsignedInteger('idProfesor')->nullable();
            $table->string('depcurt')->nullable();
            $table->boolean('didactico')->default(true);
            $table->timestamps();
        });

        Schema::connection('sqlite')->create('tipo_actividad', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('cliteral')->nullable();
            $table->string('vliteral')->nullable();
            $table->unsignedInteger('departamento_id')->nullable();
            $table->text('justificacio')->nullable();
            $table->timestamps();
        });

        Schema::connection('sqlite')->create('actividad_profesor', function (Blueprint $table): void {
            $table->unsignedInteger('idActividad');
            $table->string('idProfesor', 10);
            $table->boolean('coordinador')->default(false);
        });

        Schema::connection('sqlite')->create('horas', function (Blueprint $table): void {
            $table->unsignedInteger('codigo')->primary();
            $table->string('hora_ini', 5);
            $table->string('hora_fin', 5);
        });

        Schema::connection('sqlite')->create('horarios', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('idProfesor', 10);
            $table->string('modulo')->nullable();
            $table->string('idGrupo', 10)->nullable();
            $table->unsignedInteger('ocupacion')->nullable();
            $table->string('aula')->nullable();
            $table->string('dia_semana', 1);
            $table->unsignedInteger('sesion_orden');
            $table->boolean('plantilla')->default(false);
            $table->timestamps();
        });

        Schema::connection('sqlite')->create('activities', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('action')->nullable();
            $table->text('comentari')->nullable();
            $table->string('document')->nullable();
            $table->string('model_class')->nullable();
            $table->unsignedBigInteger('model_id')->nullable();
            $table->string('author_id')->nullable();
            $table->timestamps();
        });
    }

    private function seedBaseData(): void
    {
        DB::connection('sqlite')->table('profesores')->insert([
            [
                'dni' => 'DIR001',
                'nombre' => 'Direccio',
                'apellido1' => 'Centre',
                'apellido2' => 'Test',
                'email' => 'dir@test.local',
                'rol' => config('roles.rol.direccion'),
                'activo' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'dni' => 'ACT100',
                'nombre' => 'Jordi',
                'apellido1' => 'Marti',
                'apellido2' => 'Perez',
                'email' => 'act100@test.local',
                'rol' => config('roles.rol.profesor'),
                'activo' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'dni' => 'ACT200',
                'nombre' => 'Anna',
                'apellido1' => 'Soler',
                'apellido2' => 'Lopez',
                'email' => 'act200@test.local',
                'rol' => config('roles.rol.profesor'),
                'activo' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        DB::connection('sqlite')->table('departamentos')->insert([
            [
                'id' => 10,
                'cliteral' => 'Dep. Arts',
                'vliteral' => 'Departament d\'Arts',
                'idProfesor' => null,
                'depcurt' => 'ART',
                'didactico' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 11,
                'cliteral' => 'Dep. Història',
                'vliteral' => 'Departament d\'Història',
                'idProfesor' => null,
                'depcurt' => 'HIS',
                'didactico' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        DB::connection('sqlite')->table('tipo_actividad')->insert([
            [
                'id' => 7,
                'cliteral' => 'Eixida',
                'vliteral' => 'Eixida cultural',
                'departamento_id' => 10,
                'justificacio' => 'Activitat per a consolidar aprenentatges.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 8,
                'cliteral' => 'Teatre',
                'vliteral' => 'Representació teatral',
                'departamento_id' => 11,
                'justificacio' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        DB::connection('sqlite')->table('actividades')->insert([
            [
                'id' => 1,
                'name' => 'Visita museu',
                'tipo_actividad_id' => 7,
                'descripcion' => 'Eixida cultural',
                'extraescolar' => 1,
                'desde' => '2026-03-20 09:00:00',
                'hasta' => '2026-03-20 13:00:00',
                'complementaria' => 1,
                'fueraCentro' => 1,
                'transport' => 0,
                'objetivos' => 'Visita guiada',
                'comentarios' => 'Cal confirmar el nombre final d\'assistents.',
                'idDocumento' => null,
                'estado' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'name' => 'Jornada convivencia',
                'tipo_actividad_id' => 7,
                'descripcion' => 'Activitat autoritzada',
                'extraescolar' => 1,
                'desde' => '2026-03-21 08:00:00',
                'hasta' => '2026-03-21 14:00:00',
                'complementaria' => 1,
                'fueraCentro' => 1,
                'transport' => 0,
                'objetivos' => 'Memòria activitat',
                'comentarios' => null,
                'idDocumento' => 99,
                'estado' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'name' => 'Teatre escolar',
                'tipo_actividad_id' => 8,
                'descripcion' => 'Registrada',
                'extraescolar' => 1,
                'desde' => '2026-03-10 10:00:00',
                'hasta' => '2026-03-10 12:00:00',
                'complementaria' => 1,
                'fueraCentro' => 1,
                'transport' => 0,
                'objetivos' => null,
                'comentarios' => null,
                'idDocumento' => null,
                'estado' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 4,
                'name' => 'Fira formativa',
                'tipo_actividad_id' => 7,
                'descripcion' => 'Pendent ITACA',
                'extraescolar' => 1,
                'desde' => '2026-03-05 09:30:00',
                'hasta' => '2026-03-05 12:30:00',
                'complementaria' => 0,
                'fueraCentro' => 1,
                'transport' => 1,
                'objetivos' => null,
                'comentarios' => null,
                'idDocumento' => null,
                'estado' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        DB::connection('sqlite')->table('actividad_profesor')->insert([
            ['idActividad' => 1, 'idProfesor' => 'ACT100', 'coordinador' => 1],
            ['idActividad' => 2, 'idProfesor' => 'ACT100', 'coordinador' => 1],
            ['idActividad' => 3, 'idProfesor' => 'ACT200', 'coordinador' => 1],
            ['idActividad' => 4, 'idProfesor' => 'ACT200', 'coordinador' => 1],
        ]);

        DB::connection('sqlite')->table('grupos')->insert([
            [
                'codigo' => 'GRP001',
                'nombre' => '1r CFGM Estètica',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        DB::connection('sqlite')->table('actividad_grupo')->insert([
            ['idActividad' => 2, 'idGrupo' => 'GRP001'],
        ]);

        DB::connection('sqlite')->table('horas')->insert([
            ['codigo' => 1, 'hora_ini' => '08:55', 'hora_fin' => '09:50'],
            ['codigo' => 2, 'hora_ini' => '09:50', 'hora_fin' => '10:45'],
        ]);

        DB::connection('sqlite')->table('horarios')->insert([
            [
                'idProfesor' => 'ACT100',
                'modulo' => 'MOD1',
                'idGrupo' => '2CFMLFPJ',
                'ocupacion' => null,
                'aula' => 'A1',
                'dia_semana' => nameDay('2026-03-21'),
                'sesion_orden' => 1,
                'plantilla' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'idProfesor' => 'ACT100',
                'modulo' => 'MOD2',
                'idGrupo' => '1CFMLFPG',
                'ocupacion' => null,
                'aula' => 'A2',
                'dia_semana' => nameDay('2026-03-21'),
                'sesion_orden' => 2,
                'plantilla' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
