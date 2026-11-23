<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Intranet\Entities\AssumpteParticular;
use Intranet\Application\Falta\FaltaService;
use Intranet\Application\Falta\FaltaAnulacioService;
use Intranet\Entities\Falta;
use Intranet\Mail\ReminderFaltaJustificant;
use Intranet\Entities\Profesor;
use Intranet\Livewire\FaltaDireccionPanel;
use Livewire\Livewire;
use Tests\TestCase;

/** Verifica les baixes, l'esborrat abans d'imprimir i l'actualització del panell de Direcció. */
class FaltaDireccionPanelTest extends TestCase
{
    private string $sqlitePath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sqlitePath = storage_path('falta_direccion_panel_testing.sqlite');
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
        Schema::connection('sqlite')->dropIfExists('activities');
        Schema::connection('sqlite')->dropIfExists('notifications');
        Schema::connection('sqlite')->dropIfExists('faltas');
        Schema::connection('sqlite')->dropIfExists('assumptes_particulars');
        Schema::connection('sqlite')->dropIfExists('profesores');

        if (file_exists($this->sqlitePath)) {
            @unlink($this->sqlitePath);
        }

        parent::tearDown();
    }

    public function test_renderitza_llistat_inicial_i_textos_clau(): void
    {
        $component = $this->actingAsDireccion()
            ? Livewire::test(FaltaDireccionPanel::class)
            : null;

        $component
            ->assertSee('Nova')
            ->assertSee('Maria Garcia Lopez')
            ->assertSee('Joan Soler Perez');

        $faltes = $component->get('faltes');

        $this->assertCount(3, $faltes);
    }

    public function test_mostra_la_resolucio_pel_circuit_ordinari_de_la_falta(): void
    {
        DB::connection('sqlite')->table('faltas')->where('id', 3)->update([
            'fichero' => 'assumptes-particulars/resolucions/resolucio.pdf',
        ]);
        DB::connection('sqlite')->table('assumptes_particulars')->insert([
            'id' => 10,
            'falta_id' => 3,
            'idProfesor' => 'PF200',
            'estat' => 'autoritzada',
            'resolucio_document' => 'assumptes-particulars/resolucions/resolucio.pdf',
        ]);

        $component = Livewire::actingAs($this->direccionUser(), 'profesor')
            ->test(FaltaDireccionPanel::class);

        $this->assertTrue(collect($component->get('faltes'))->firstWhere('id', 3)['hasDocument']);
        $component
            ->assertSeeHtml(route('falta.direccion.document', ['falta' => 3]))
            ->assertSeeHtml(route('falta.direccion.document', ['falta' => 2]));
    }

    public function test_no_mostra_resolucio_si_la_peticio_no_te_pdf(): void
    {
        DB::connection('sqlite')->table('assumptes_particulars')->insert([
            'id' => 10,
            'falta_id' => 3,
            'idProfesor' => 'PF200',
            'estat' => 'autoritzada',
            'resolucio_document' => null,
        ]);

        Livewire::actingAs($this->direccionUser(), 'profesor')
            ->test(FaltaDireccionPanel::class)
            ->assertDontSeeHtml(route('assumptes-particulars.document', ['assumpteParticular' => 10]))
            ->assertSeeHtml(route('falta.direccion.document', ['falta' => 2]));
    }

    public function test_no_mostra_resolucio_d_una_peticio_no_autoritzada(): void
    {
        DB::connection('sqlite')->table('assumptes_particulars')->insert([
            'id' => 10,
            'falta_id' => 3,
            'idProfesor' => 'PF200',
            'estat' => 'denegada',
            'resolucio_document' => 'assumptes-particulars/resolucions/resolucio.pdf',
        ]);

        Livewire::actingAs($this->direccionUser(), 'profesor')
            ->test(FaltaDireccionPanel::class)
            ->assertDontSeeHtml(route('assumptes-particulars.document', ['assumpteParticular' => 10]));
    }

    public function test_filtra_per_professor_mentre_escriu_nom_o_dni(): void
    {
        $component = Livewire::actingAs($this->direccionUser(), 'profesor')
            ->test(FaltaDireccionPanel::class);

        $component->set('filterProfessor', 'Maria');

        $faltes = $component->get('faltes');

        $this->assertCount(2, $faltes);
        $this->assertSame(['PF100', 'PF100'], array_column($faltes, 'idProfesor'));

        $component->set('filterProfessor', 'PF200');

        $faltes = $component->get('faltes');

        $this->assertCount(1, $faltes);
        $this->assertSame('PF200', $faltes[0]['idProfesor']);
    }

    public function test_filtra_per_estat_textual(): void
    {
        $component = Livewire::actingAs($this->direccionUser(), 'profesor')
            ->test(FaltaDireccionPanel::class);

        $component
            ->set('filterEstat', '2')
            ->assertSee('Justificada')
            ->assertDontSee('Sense justificant')
            ->assertDontSee('Autoritzada');

        $faltes = $component->get('faltes');

        $this->assertCount(1, $faltes);
        $this->assertSame(2, $faltes[0]['estado']);
    }

    public function test_mostra_tots_els_estats_amb_recompte_encara_que_no_tinguen_faltes(): void
    {
        $component = Livewire::actingAs($this->direccionUser(), 'profesor')
            ->test(FaltaDireccionPanel::class);

        $this->assertSame([0, 1, 2, 3, 4, 5], array_keys($component->get('estatOptions')));
        $this->assertEstatOptionCounts($component->get('estatOptions'), [
            '0' => 0,
            '1' => 1,
            '2' => 1,
            '3' => 1,
            '4' => 0,
            '5' => 0,
        ]);

        $component->set('filterProfessor', 'Maria');

        $this->assertEstatOptionCounts($component->get('estatOptions'), [
            '0' => 0,
            '1' => 1,
            '2' => 1,
            '3' => 0,
            '4' => 0,
            '5' => 0,
        ]);
    }

    /** Direcció pot esborrar qualsevol estat no imprés, però sempre amb motiu i confirmació. */
    public function test_direccio_esborra_qualsevol_estat_abans_d_imprimir(): void
    {
        Log::spy();
        foreach ([0, 1, 2, 3, 5, 6] as $estat) {
            $id = DB::table('faltas')->insertGetId([
                'idProfesor' => 'PF100', 'estado' => $estat, 'desde' => '2026-03-12', 'motivos' => 1,
            ]);
            $component = Livewire::actingAs($this->direccionUser(), 'profesor')
                ->test(FaltaDireccionPanel::class)
                ->assertSeeHtml('wire:click="esborrar(' . $id . ')"')
                ->call('esborrar', $id)
                ->assertSet('anullarId', $id)
                ->assertSet('esborrant', true);
            $this->assertDatabaseHas('faltas', ['id' => $id]);
            $component->set('motiuAnulacio', 'Error en el registre')
                ->call('confirmarAnulacio')
                ->assertHasNoErrors()
                ->assertSet('message', 'Falta esborrada correctament.')
                ->assertSet('anullarId', null);
            $this->assertDatabaseMissing('faltas', ['id' => $id]);
        }
        Log::shouldHaveReceived('info')->with('Falta esborrada per Direcció', \Mockery::on(
            fn (array $context): bool => $context['direccio_dni'] === 'DIR001' && $context['motiu'] === 'Error en el registre'
        ))->times(6);
    }

    /** Sense motiu no es confirma; cancel·lar deixa intacta la falta i el document. */
    public function test_esborrar_requerix_motiu_i_es_pot_cancelar(): void
    {
        Storage::fake('local');
        Storage::disk('local')->put('faltas/doc.pdf', 'PDF');
        Livewire::actingAs($this->direccionUser(), 'profesor')
            ->test(FaltaDireccionPanel::class)
            ->call('esborrar', 2)
            ->set('motiuAnulacio', '   ')
            ->call('confirmarAnulacio')
            ->assertHasErrors(['motiuAnulacio' => 'required'])
            ->call('cancelarAnulacio')
            ->assertHasNoErrors()
            ->assertSet('anullarId', null);
        $this->assertDatabaseHas('faltas', ['id' => 2]);
        Storage::disk('local')->assertExists('faltas/doc.pdf');
    }

    /** El document ordinari també es retira quan es confirma l'esborrat. */
    public function test_esborrar_retira_el_justificant_ordinari(): void
    {
        Storage::fake('local');
        Storage::disk('local')->put('faltas/doc.pdf', 'PDF');
        Livewire::actingAs($this->direccionUser(), 'profesor')
            ->test(FaltaDireccionPanel::class)
            ->call('esborrar', 2)
            ->set('motiuAnulacio', 'Duplicada')
            ->call('confirmarAnulacio')
            ->assertHasNoErrors();
        Storage::disk('local')->assertMissing('faltas/doc.pdf');
        $this->assertDatabaseMissing('faltas', ['id' => 2]);
    }

    /** Estat imprés o vincle documental impedixen tant el botó com l'acció directa. */
    public function test_no_esborra_faltes_incloses_en_un_informe_impres(): void
    {
        foreach ([['estado' => 4, 'idDocumento' => null], ['estado' => 5, 'idDocumento' => 12]] as $canvis) {
            DB::table('faltas')->where('id', 3)->update($canvis);
            Livewire::actingAs($this->direccionUser(), 'profesor')
                ->test(FaltaDireccionPanel::class)
                ->assertDontSeeHtml('wire:click="esborrar(3)"')
                ->call('esborrar', 3)
                ->assertForbidden();
            $this->assertDatabaseHas('faltas', ['id' => 3]);
        }
    }

    /** Si s'imprimix durant la confirmació, el servidor rebutja l'esborrat. */
    public function test_revalida_la_impressio_en_confirmar_esborrat(): void
    {
        $component = Livewire::actingAs($this->direccionUser(), 'profesor')
            ->test(FaltaDireccionPanel::class)
            ->call('esborrar', 3)
            ->set('motiuAnulacio', 'Duplicada');
        DB::table('faltas')->where('id', 3)->update(['idDocumento' => 12]);
        $component->call('confirmarAnulacio')->assertForbidden();
        $this->assertDatabaseHas('faltas', ['id' => 3]);

        try {
            app(FaltaAnulacioService::class)->esborrar(3, 'Duplicada', 'DIR001');
            $this->fail('El servei també ha de rebutjar una falta impresa.');
        } catch (\Illuminate\Validation\ValidationException $exception) {
            $this->assertArrayHasKey('motiuAnulacio', $exception->errors());
        }
        $this->assertDatabaseHas('faltas', ['id' => 3]);
    }

    /** Esborrar el registre de baixa no modifica la situació laboral del professor. */
    public function test_esborrar_baixa_llarga_no_tramita_l_alta(): void
    {
        $this->prepararBaixaLlarga();
        Livewire::actingAs($this->direccionUser(), 'profesor')
            ->test(FaltaDireccionPanel::class)
            ->call('esborrar', 2)
            ->set('motiuAnulacio', 'Registre duplicat')
            ->call('confirmarAnulacio')
            ->assertHasNoErrors();
        $this->assertDatabaseMissing('faltas', ['id' => 2]);
        $this->assertDatabaseHas('profesores', ['dni' => 'PF100', 'fecha_baja' => '2026-03-01']);
    }

    /** Una identitat sense Direcció no pot confirmar l'esborrat d'una autoritzada. */
    public function test_esborrar_revalida_el_rol_en_confirmar(): void
    {
        $component = Livewire::actingAs($this->direccionUser(), 'profesor')
            ->test(FaltaDireccionPanel::class)
            ->call('esborrar', 3)
            ->set('motiuAnulacio', 'Error');
        $this->actingAs(Profesor::findOrFail('PF100'), 'profesor');
        $component->call('confirmarAnulacio')->assertForbidden();
        $this->assertDatabaseHas('faltas', ['id' => 3]);
    }

    /** Una actualització recupera dades externes i recomptes, mantenint els filtres. */
    public function test_actualitza_dades_sense_perdre_filtres(): void
    {
        $component = Livewire::actingAs($this->direccionUser(), 'profesor')
            ->test(FaltaDireccionPanel::class)
            ->assertSee('Actualitzar dades')
            ->set('filterProfessor', 'PF100')
            ->set('filterEstat', '1');
        DB::table('faltas')->where('id', 1)->update(['estado' => 4, 'idDocumento' => 42]);
        DB::table('faltas')->where('id', 2)->update(['estado' => 1]);
        DB::table('profesores')->where('dni', 'PF100')->update(['nombre' => 'Maria Actualitzada']);

        $component->call('actualitzarDades')
            ->assertSet('filterProfessor', 'PF100')
            ->assertSet('filterEstat', '1')
            ->assertSet('faltes', fn (array $faltes): bool => count($faltes) === 1 && $faltes[0]['id'] === 2)
            ->assertSee('Maria Actualitzada');
        $this->assertStringContainsString('Maria Actualitzada', $component->get('professorOptions')['PF100']);
        $this->assertEstatOptionCounts($component->get('estatOptions'), ['1' => 1, '2' => 0, '4' => 1]);

        $component->set('filterEstat', '')
            ->assertDontSeeHtml('wire:click="esborrar(1)"')
            ->assertSeeHtml('wire:click="esborrar(2)"');
    }

    public function test_direccio_pot_anullar_una_falta_autoritzada_amb_motiu(): void
    {
        Livewire::actingAs($this->direccionUser(), 'profesor')
            ->test(FaltaDireccionPanel::class)
            ->call('obrirAnulacio', 3)
            ->set('motiuAnulacio', 'Duplicada')
            ->call('confirmarAnulacio')
            ->assertSet('message', 'Falta anul·lada i dia alliberat correctament.');

        $this->assertNull(DB::connection('sqlite')->table('faltas')->where('id', 3)->first());
    }

    public function test_no_pot_anullar_una_falta_tancada_mensualment(): void
    {
        DB::connection('sqlite')->table('faltas')->where('id', 3)->update(['idDocumento' => 12]);

        Livewire::actingAs($this->direccionUser(), 'profesor')
            ->test(FaltaDireccionPanel::class)
            ->assertDontSeeHtml('wire:click="obrirAnulacio(3)"');

        $this->assertNotNull(DB::connection('sqlite')->table('faltas')->where('id', 3)->first());
    }

    public function test_crea_una_falta_des_del_formulari_livewire(): void
    {
        Livewire::actingAs($this->direccionUser(), 'profesor')
            ->test(FaltaDireccionPanel::class)
            ->call('crear')
            ->set('formProfessorSearch', 'Maria Garcia Lopez (PF100)')
            ->set('formDesde', '2026-03-15')
            ->set('formHasta', '2026-03-15')
            ->set('formDiaCompleto', true)
            ->set('formMotivos', '2')
            ->set('formObservaciones', 'Nova falta des de Livewire')
            ->call('guardar')
            ->assertSet('error', '')
            ->assertSet('message', 'Falta creada correctament.');

        $this->assertSame(4, DB::connection('sqlite')->table('faltas')->count());

        $falta = DB::connection('sqlite')->table('faltas')->where('observaciones', 'Nova falta des de Livewire')->first();
        $this->assertNotNull($falta);
        $this->assertSame('PF100', $falta->idProfesor);
    }

    public function test_edita_una_falta_sense_endpoint_edit_data(): void
    {
        Livewire::actingAs($this->direccionUser(), 'profesor')
            ->test(FaltaDireccionPanel::class)
            ->call('editar', 2)
            ->assertSet('formFaltaId', 2)
            ->set('formObservaciones', 'Editada des de Livewire')
            ->call('guardar')
            ->assertSet('error', '')
            ->assertSet('message', 'Falta actualitzada correctament.');

        $this->assertSame(
            'Editada des de Livewire',
            DB::connection('sqlite')->table('faltas')->where('id', 2)->value('observaciones')
        );
    }

    public function test_edita_una_falta_autoritzada(): void
    {
        Livewire::actingAs($this->direccionUser(), 'profesor')
            ->test(FaltaDireccionPanel::class)
            ->call('editar', 3)
            ->assertSet('formFaltaId', 3)
            ->set('formObservaciones', 'Autoritzada editada des de direccio')
            ->call('guardar')
            ->assertSet('error', '')
            ->assertSet('message', 'Falta actualitzada correctament.');

        $this->assertSame(
            'Autoritzada editada des de direccio',
            DB::connection('sqlite')->table('faltas')->where('id', 3)->value('observaciones')
        );
    }

    public function test_editar_una_falta_autoritzada_com_a_baixa_llarga_la_mostra_al_filtre_corresponent(): void
    {
        $component = Livewire::actingAs($this->direccionUser(), 'profesor')
            ->test(FaltaDireccionPanel::class)
            ->call('editar', 3)
            ->set('formBaja', true)
            ->call('guardar')
            ->assertSet('error', '')
            ->assertSet('message', 'Falta actualitzada correctament.');

        $this->assertSame(1, (int) DB::connection('sqlite')->table('faltas')->where('id', 3)->value('baja'));
        $this->assertSame(5, (int) DB::connection('sqlite')->table('faltas')->where('id', 3)->value('estado'));
        $this->assertSame('2026-03-12', DB::connection('sqlite')->table('profesores')->where('dni', 'PF200')->value('fecha_baja'));

        $component->set('filterEstat', '5');

        $faltes = $component->get('faltes');
        $this->assertCount(1, $faltes);
        $this->assertSame(3, $faltes[0]['id']);
    }

    public function test_mostra_boto_recordatori_només_en_estat_sense_justificant(): void
    {
        Livewire::actingAs($this->direccionUser(), 'profesor')
            ->test(FaltaDireccionPanel::class)
            ->assertSeeHtml('wire:click="enviarRecordatoriJustificant(1)"')
            ->assertDontSeeHtml('wire:click="enviarRecordatoriJustificant(2)"')
            ->assertDontSeeHtml('wire:click="enviarRecordatoriJustificant(3)"');
    }

    public function test_envia_recordatori_quan_la_falta_està_en_estat_1(): void
    {
        Mail::fake();

        Livewire::actingAs($this->direccionUser(), 'profesor')
            ->test(FaltaDireccionPanel::class)
            ->call('enviarRecordatoriJustificant', 1)
            ->assertSet('error', '')
            ->assertSet('message', 'Recordatori enviat a pf100@test.local.');

        Mail::assertSent(ReminderFaltaJustificant::class, function (ReminderFaltaJustificant $mail) {
            return $mail->hasTo('pf100@test.local') && (int) $mail->falta->id === 1;
        });
    }

    public function test_no_envia_recordatori_si_l_estat_no_es_1(): void
    {
        Mail::fake();

        Livewire::actingAs($this->direccionUser(), 'profesor')
            ->test(FaltaDireccionPanel::class)
            ->call('enviarRecordatoriJustificant', 2)
            ->assertSet('error', 'Només es pot enviar el recordatori en faltes sense justificant.');

        Mail::assertNothingSent();
    }

    /** Cobrix els límits del marge natural i els estats no convertibles. */
    public function test_llarga_durada_nomes_es_mostra_per_a_absencies_iniciades_fins_ahir(): void
    {
        $this->travelTo(\Illuminate\Support\Carbon::parse('2026-03-12 15:00:00'));
        $casos = [
            ['2026-03-10', '2026-03-13', 1, 0, true],
            ['2026-03-12', '2026-03-12', 2, 0, true],
            ['2026-03-10', '2026-03-11', 3, 0, true],
            ['2026-03-10', '2026-03-10', 1, 0, false],
            ['2026-03-13', '2026-03-14', 1, 0, false],
            ['2026-03-10', null, 5, 1, false],
            ['2026-03-10', '2026-03-12', 4, 0, false],
        ];

        foreach ($casos as [$desde, $hasta, $estado, $baja, $visible]) {
            DB::table('faltas')->where('id', 1)->update(compact('desde', 'hasta', 'estado', 'baja'));
            $component = Livewire::actingAs($this->direccionUser(), 'profesor')
                ->test(FaltaDireccionPanel::class);
            $this->assertSame($visible, collect($component->get('faltes'))->firstWhere('id', 1)['canLongLeave']);
            if ($visible) {
                $component->assertSeeHtml('wire:click="obrirLlargaDurada(1)"')
                    ->assertSee('L.Dura')
                    ->assertSeeHtml('title="Llarga durada"');
            } else {
                $component->assertDontSeeHtml('wire:click="obrirLlargaDurada(1)"');
            }
        }
    }

    /** Preselecciona el motiu i usa hui quan la data de baixa està buida. */
    public function test_llarga_durada_preselecciona_motiu_i_hui_sense_guardar_en_obrir(): void
    {
        $this->travelTo(\Illuminate\Support\Carbon::parse('2026-03-12 15:00:00'));
        Livewire::actingAs($this->direccionUser(), 'profesor')
            ->test(FaltaDireccionPanel::class)
            ->call('obrirLlargaDurada', 3)
            ->assertSet('llargaDuradaMotiu', '3')
            ->assertSet('llargaDuradaDataBaixa', '2026-03-12')
            ->assertSee('Data de baixa del professor')
            ->assertDispatched('show-llarga-durada-modal')
            ->set('llargaDuradaMotiu', '2')
            ->call('cancelarLlargaDurada')
            ->assertSet('llargaDuradaId', null)
            ->assertDispatched('hide-llarga-durada-modal');

        $this->assertNull(DB::table('profesores')->where('dni', 'PF200')->value('fecha_baja'));
        $this->assertSame(3, (int) DB::table('faltas')->where('id', 3)->value('motivos'));
        $this->assertSame(0, (int) DB::table('faltas')->where('id', 3)->value('baja'));
    }

    /** Guarda atòmicament el motiu, la conversió i la data editada del professor. */
    public function test_llarga_durada_permet_canviar_data_actual_i_motiu(): void
    {
        $this->travelTo(\Illuminate\Support\Carbon::parse('2026-03-12 15:00:00'));
        DB::table('profesores')->where('dni', 'PF100')->update(['fecha_baja' => '2026-03-01']);
        DB::table('faltas')->where('id', 2)->update([
            'dia_completo' => 0, 'hora_ini' => '08:00:00', 'hora_fin' => '10:00:00',
        ]);

        Livewire::actingAs($this->direccionUser(), 'profesor')
            ->test(FaltaDireccionPanel::class)
            ->call('obrirLlargaDurada', 2)
            ->assertSet('llargaDuradaMotiu', '2')
            ->assertSet('llargaDuradaDataBaixa', '2026-03-01')
            ->set('llargaDuradaDataBaixa', '2026-03-09')
            ->set('llargaDuradaMotiu', '3')
            ->call('guardarLlargaDurada')
            ->assertHasNoErrors()
            ->assertSet('llargaDuradaId', null)
            ->assertSet('message', __('falta.llarga_durada.guardada'))
            ->assertSeeHtml('wire:click="alta(2)"')
            ->assertDontSeeHtml('wire:click="obrirLlargaDurada(2)"');

        $this->assertDatabaseHas('faltas', [
            'id' => 2, 'desde' => '2026-03-11', 'hasta' => null,
            'motivos' => 3, 'baja' => 1, 'estado' => 5, 'dia_completo' => 1,
            'hora_ini' => null, 'hora_fin' => null, 'fichero' => 'faltas/doc.pdf',
            'observaciones' => 'Amb document',
        ]);
        $this->assertDatabaseHas('profesores', ['dni' => 'PF100', 'fecha_baja' => '2026-03-09', 'activo' => 1]);
    }

    /** Les dades invàlides no alteren cap registre. */
    public function test_llarga_durada_valida_motiu_i_data(): void
    {
        $this->travelTo(\Illuminate\Support\Carbon::parse('2026-03-12 15:00:00'));
        Livewire::actingAs($this->direccionUser(), 'profesor')
            ->test(FaltaDireccionPanel::class)
            ->call('obrirLlargaDurada', 3)
            ->set('llargaDuradaMotiu', '999999')
            ->set('llargaDuradaDataBaixa', '')
            ->call('guardarLlargaDurada')
            ->assertHasErrors(['llargaDuradaMotiu', 'llargaDuradaDataBaixa']);

        $this->assertDatabaseHas('faltas', ['id' => 3, 'baja' => 0, 'estado' => 3]);
        $this->assertNull(DB::table('profesores')->where('dni', 'PF200')->value('fecha_baja'));
    }

    /** Revalida el termini en guardar encara que el diàleg ja estiga obert. */
    public function test_llarga_durada_revalida_el_termini_en_guardar(): void
    {
        $this->travelTo(\Illuminate\Support\Carbon::parse('2026-03-12 15:00:00'));
        $component = Livewire::actingAs($this->direccionUser(), 'profesor')
            ->test(FaltaDireccionPanel::class)
            ->call('obrirLlargaDurada', 2);
        $this->travelTo(\Illuminate\Support\Carbon::parse('2026-03-13 15:00:00'));
        $component->call('guardarLlargaDurada')->assertHasErrors('llargaDuradaMotiu');
        $this->assertDatabaseHas('faltas', ['id' => 2, 'baja' => 0, 'estado' => 2]);
        $this->assertNull(DB::table('profesores')->where('dni', 'PF100')->value('fecha_baja'));
    }

    /** Manté la protecció de resolucions firmades d'assumptes particulars. */
    public function test_llarga_durada_no_modifica_assumptes_particulars_autoritzats(): void
    {
        $this->travelTo(\Illuminate\Support\Carbon::parse('2026-03-12 15:00:00'));
        DB::table('assumptes_particulars')->insert([
            'id' => 10, 'falta_id' => 3, 'idProfesor' => 'PF200', 'estat' => 'autoritzada',
        ]);
        Livewire::actingAs($this->direccionUser(), 'profesor')
            ->test(FaltaDireccionPanel::class)
            ->assertDontSeeHtml('wire:click="obrirLlargaDurada(3)"')
            ->call('obrirLlargaDurada', 3)
            ->assertSet('error', __('falta.llarga_durada.no_disponible'))
            ->set('llargaDuradaId', 3)
            ->set('llargaDuradaMotiu', '3')
            ->set('llargaDuradaDataBaixa', '2026-03-12')
            ->call('guardarLlargaDurada')
            ->assertHasErrors('llargaDuradaMotiu');
        $this->assertDatabaseHas('faltas', ['id' => 3, 'baja' => 0]);
    }

    /** Comprova permisos reals encara que es manipule l'estat públic del component. */
    public function test_llarga_durada_rebutja_professor_sense_rol_direccio(): void
    {
        $this->travelTo(\Illuminate\Support\Carbon::parse('2026-03-12 15:00:00'));
        $user = Profesor::findOrFail('PF100');
        Livewire::actingAs($user, 'profesor')->test(FaltaDireccionPanel::class)
            ->set('isDireccion', true)
            ->assertDontSeeHtml('wire:click="obrirLlargaDurada(2)"')
            ->call('obrirLlargaDurada', 2)
            ->assertForbidden();
        Livewire::actingAs($user, 'profesor')->test(FaltaDireccionPanel::class)
            ->set('llargaDuradaId', 2)
            ->set('llargaDuradaMotiu', '3')
            ->set('llargaDuradaDataBaixa', '2026-03-12')
            ->call('guardarLlargaDurada')
            ->assertForbidden();
        $this->assertDatabaseHas('faltas', ['id' => 2, 'baja' => 0]);
    }

    /** El botó només apareix en baixes llargues obertes. */
    public function test_canvi_motiu_nomes_apareix_en_baixes_llargues_obertes(): void
    {
        $this->prepararBaixaLlarga();
        $component = Livewire::actingAs($this->direccionUser(), 'profesor')
            ->test(FaltaDireccionPanel::class)
            ->assertSee('Canv. Motiu')
            ->assertSeeHtml('title="Seguir amb altre motiu"')
            ->assertSeeHtml('wire:click="obrirCanviMotiu(2)"')
            ->assertDontSeeHtml('wire:click="obrirCanviMotiu(1)"')
            ->assertDontSeeHtml('wire:click="obrirCanviMotiu(3)"');

        foreach ([['estado' => 3], ['baja' => 0], ['hasta' => '2026-03-12']] as $canvis) {
            $this->prepararBaixaLlarga();
            DB::table('faltas')->where('id', 2)->update($canvis);
            $component->set('filterProfessor', 'PF100')
                ->assertDontSeeHtml('wire:click="obrirCanviMotiu(2)"')
                ->call('obrirCanviMotiu', 2)
                ->assertSet('error', __('falta.canvi_motiu.no_disponible'));
            $component->set('filterProfessor', '');
        }
    }

    /** Proposa hui i l'endemà, permet editar i cancel·lar sense persistir. */
    public function test_canvi_motiu_proposa_dates_i_permet_cancelar(): void
    {
        $this->prepararBaixaLlarga();
        Livewire::actingAs($this->direccionUser(), 'profesor')
            ->test(FaltaDireccionPanel::class)
            ->call('obrirCanviMotiu', 2)
            ->assertSet('canviMotiuId', 2)
            ->assertSet('canviMotiuDataFi', '2026-03-12')
            ->assertSet('canviMotiuDataInici', '2026-03-13')
            ->assertSet('canviMotiuNouMotiu', '')
            ->assertDispatched('show-canvi-motiu-modal')
            ->set('canviMotiuDataFi', '2026-03-14')
            ->assertSet('canviMotiuDataInici', '2026-03-15')
            ->set('canviMotiuDataInici', '2026-03-16')
            ->set('canviMotiuNouMotiu', '3')
            ->call('cancelarCanviMotiu')
            ->assertSet('canviMotiuId', null)
            ->assertSet('canviMotiuNouMotiu', '')
            ->assertDispatched('hide-canvi-motiu-modal');

        $this->assertDatabaseCount('faltas', 3);
        $this->assertDatabaseHas('faltas', ['id' => 2, 'estado' => 5, 'hasta' => null, 'motivos' => 2]);
        $this->assertDatabaseHas('profesores', ['dni' => 'PF100', 'fecha_baja' => '2026-03-01']);
    }

    /** Tanca l'original i crea la continuació amb les dates editades sense alterar substituts. */
    public function test_canvi_motiu_guarda_les_dues_absencies_i_conserva_la_baixa_del_professor(): void
    {
        $this->prepararBaixaLlarga();
        DB::table('profesores')->where('dni', 'PF200')->update(['sustituye_a' => 'PF100']);
        $professors = DB::table('profesores')->orderBy('dni')->get()->toArray();

        $component = Livewire::actingAs($this->direccionUser(), 'profesor')
            ->test(FaltaDireccionPanel::class)
            ->call('obrirCanviMotiu', 2)
            ->set('canviMotiuDataFi', '2026-03-14')
            ->set('canviMotiuDataInici', '2026-03-16')
            ->set('canviMotiuNouMotiu', '3')
            ->call('guardarCanviMotiu')
            ->assertHasNoErrors()
            ->assertSet('canviMotiuId', null)
            ->assertSet('message', __('falta.canvi_motiu.guardada'))
            ->assertDispatched('hide-canvi-motiu-modal')
            ->assertDontSeeHtml('wire:click="obrirCanviMotiu(2)"');

        $this->assertDatabaseCount('faltas', 4);
        $this->assertDatabaseHas('faltas', [
            'id' => 2, 'idProfesor' => 'PF100', 'desde' => '2026-03-11', 'hasta' => '2026-03-14',
            'motivos' => 2, 'estado' => 3, 'baja' => 0, 'fichero' => 'faltas/doc.pdf',
            'observaciones' => 'Amb document',
        ]);
        $nova = DB::table('faltas')->where('id', '>', 3)->first();
        $this->assertDatabaseHas('faltas', [
            'id' => $nova->id, 'idProfesor' => 'PF100', 'desde' => '2026-03-16', 'hasta' => null,
            'motivos' => 3, 'estado' => 5, 'baja' => 1, 'dia_completo' => 1,
            'hora_ini' => null, 'hora_fin' => null, 'fichero' => null, 'idDocumento' => null,
        ]);
        $this->assertEquals($professors, DB::table('profesores')->orderBy('dni')->get()->toArray());
        $component->assertSeeHtml('wire:click="obrirCanviMotiu(' . $nova->id . ')"');
    }

    /** Valida motiu, dates obligatòries i límits sense deixar canvis parcials. */
    public function test_canvi_motiu_rebutja_dates_o_motiu_invalids(): void
    {
        $this->prepararBaixaLlarga();
        $casos = [
            ['', '3', '2026-03-13', 'canviMotiuDataFi'],
            ['2026-02-30', '3', '2026-03-13', 'canviMotiuDataFi'],
            ['2026-03-10', '3', '2026-03-13', 'canviMotiuDataFi'],
            ['2026-03-12', '', '2026-03-13', 'canviMotiuNouMotiu'],
            ['2026-03-12', '999999', '2026-03-13', 'canviMotiuNouMotiu'],
            ['2026-03-12', '3', '', 'canviMotiuDataInici'],
            ['2026-03-12', '3', '2026-03-12', 'canviMotiuDataInici'],
            ['2026-03-12', '3', '2026-03-11', 'canviMotiuDataInici'],
        ];

        foreach ($casos as [$fi, $motiu, $inici, $error]) {
            Livewire::actingAs($this->direccionUser(), 'profesor')
                ->test(FaltaDireccionPanel::class)
                ->call('obrirCanviMotiu', 2)
                ->set('canviMotiuDataFi', $fi)
                ->set('canviMotiuNouMotiu', $motiu)
                ->set('canviMotiuDataInici', $inici)
                ->call('guardarCanviMotiu')
                ->assertHasErrors($error);

            $this->assertDatabaseCount('faltas', 3);
            $this->assertDatabaseHas('faltas', ['id' => 2, 'estado' => 5, 'baja' => 1, 'hasta' => null]);
        }
    }

    /** Accepta finalitzar el mateix dia d'inici i impedix repetir el guardat de l'original. */
    public function test_canvi_motiu_accepta_el_limit_inicial_i_no_duplica_la_continuacio(): void
    {
        $this->prepararBaixaLlarga();
        $component = Livewire::actingAs($this->direccionUser(), 'profesor')
            ->test(FaltaDireccionPanel::class)
            ->call('obrirCanviMotiu', 2)
            ->set('canviMotiuDataFi', '2026-03-11')
            ->set('canviMotiuNouMotiu', '3')
            ->call('guardarCanviMotiu')
            ->assertHasNoErrors();

        $component->set('canviMotiuId', 2)
            ->set('canviMotiuDataFi', '2026-03-11')
            ->set('canviMotiuDataInici', '2026-03-12')
            ->set('canviMotiuNouMotiu', '3')
            ->call('guardarCanviMotiu')
            ->assertHasErrors('canviMotiuNouMotiu');

        $this->assertDatabaseCount('faltas', 4);
        $this->assertDatabaseHas('faltas', ['id' => 2, 'hasta' => '2026-03-11', 'estado' => 3]);
    }

    /** Revalida una baixa que s'ha tancat mentre el diàleg estava obert. */
    public function test_canvi_motiu_revalida_l_estat_en_guardar(): void
    {
        $this->prepararBaixaLlarga();
        $component = Livewire::actingAs($this->direccionUser(), 'profesor')
            ->test(FaltaDireccionPanel::class)
            ->call('obrirCanviMotiu', 2)
            ->set('canviMotiuNouMotiu', '3');

        DB::table('faltas')->where('id', 2)->update(['estado' => 3, 'baja' => 0, 'hasta' => '2026-03-12']);
        $component->call('guardarCanviMotiu')->assertHasErrors('canviMotiuNouMotiu');
        $this->assertDatabaseCount('faltas', 3);
        $this->assertDatabaseHas('faltas', ['id' => 2, 'estado' => 3, 'hasta' => '2026-03-12']);
    }

    /** Denega l'acció a un professor inclús amb les propietats públiques manipulades. */
    public function test_canvi_motiu_exigix_rol_de_direccio(): void
    {
        $this->prepararBaixaLlarga();
        $professor = Profesor::findOrFail('PF100');
        Livewire::actingAs($professor, 'profesor')->test(FaltaDireccionPanel::class)
            ->set('isDireccion', true)
            ->assertDontSeeHtml('wire:click="obrirCanviMotiu(2)"')
            ->call('obrirCanviMotiu', 2)
            ->assertForbidden();

        Livewire::actingAs($professor, 'profesor')->test(FaltaDireccionPanel::class)
            ->set('canviMotiuId', 2)
            ->set('canviMotiuDataFi', '2026-03-12')
            ->set('canviMotiuNouMotiu', '3')
            ->set('canviMotiuDataInici', '2026-03-13')
            ->call('guardarCanviMotiu')
            ->assertForbidden();

        $this->assertDatabaseCount('faltas', 3);
        $this->assertDatabaseHas('faltas', ['id' => 2, 'estado' => 5, 'hasta' => null]);
    }

    /** Una fallada en crear la continuació restaura el tancament de l'absència original. */
    public function test_canvi_motiu_revertix_el_tancament_si_falla_la_nova_absencia(): void
    {
        $this->prepararBaixaLlarga();
        $this->actingAsDireccion();
        Event::listen('eloquent.creating: ' . Falta::class, function (): void {
            throw new \RuntimeException('Fallada simulada en crear la continuació.');
        });

        try {
            app(FaltaService::class)->continuarAmbAltreMotiu(2, '2026-03-12', '3', '2026-03-13');
            $this->fail('La creació havia de fallar.');
        } catch (\RuntimeException $exception) {
            $this->assertSame('Fallada simulada en crear la continuació.', $exception->getMessage());
        }

        $this->assertDatabaseCount('faltas', 3);
        $this->assertDatabaseHas('faltas', ['id' => 2, 'estado' => 5, 'baja' => 1, 'hasta' => null]);
        $this->assertDatabaseHas('profesores', ['dni' => 'PF100', 'fecha_baja' => '2026-03-01']);
    }

    /** Prepara una baixa de llarga durada existent amb data de professor independent. */
    private function prepararBaixaLlarga(): void
    {
        $this->travelTo(\Illuminate\Support\Carbon::parse('2026-03-12 15:00:00'));
        DB::table('faltas')->where('id', 2)->update(['estado' => 5, 'baja' => 1, 'hasta' => null]);
        DB::table('profesores')->where('dni', 'PF100')->update(['fecha_baja' => '2026-03-01']);
    }

    private function actingAsDireccion(): bool
    {
        $this->actingAs($this->direccionUser(), 'profesor');

        return true;
    }

    private function direccionUser(): Profesor
    {
        return Profesor::on('sqlite')->findOrFail('DIR001');
    }

    /**
     * Comprova els recomptes mostrats al final de cada opció d'estat.
     *
     * @param array<string, string> $estatOptions
     * @param array<string, int> $expectedCounts
     */
    private function assertEstatOptionCounts(array $estatOptions, array $expectedCounts): void
    {
        foreach ($expectedCounts as $estat => $expectedCount) {
            $this->assertArrayHasKey($estat, $estatOptions);
            $this->assertStringEndsWith('(' . $expectedCount . ')', $estatOptions[$estat]);
        }
    }

    private function createSchema(): void
    {
        Schema::connection('sqlite')->create('profesores', function (Blueprint $table): void {
            $table->string('dni', 10)->primary();
            $table->string('nombre')->nullable();
            $table->string('apellido1')->nullable();
            $table->string('apellido2')->nullable();
            $table->string('email')->nullable();
            $table->string('api_token', 80)->nullable();
            $table->unsignedInteger('rol')->default(config('roles.rol.profesor'));
            $table->unsignedInteger('departamento')->nullable();
            $table->boolean('activo')->default(true);
            $table->date('fecha_baja')->nullable();
            $table->string('sustituye_a', 10)->nullable();
            $table->timestamps();
        });

        Schema::connection('sqlite')->create('faltas', function (Blueprint $table): void {
            $table->id();
            $table->string('idProfesor', 10);
            $table->boolean('baja')->default(false);
            $table->boolean('dia_completo')->default(true);
            $table->date('desde')->nullable();
            $table->date('hasta')->nullable();
            $table->time('hora_ini')->nullable();
            $table->time('hora_fin')->nullable();
            $table->unsignedInteger('motivos')->nullable();
            $table->string('observaciones', 200)->nullable();
            $table->string('fichero')->nullable();
            $table->tinyInteger('estado')->default(0);
            $table->unsignedInteger('idDocumento')->nullable();
            $table->timestamps();
        });

        Schema::connection('sqlite')->create('assumptes_particulars', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('falta_id')->nullable();
            $table->string('idProfesor', 10);
            $table->string('estat');
            $table->string('origen')->default(AssumpteParticular::ORIGEN_SOLLICITUD);
            $table->string('resolucio_document')->nullable();
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

        Schema::connection('sqlite')->create('notifications', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('type');
            $table->morphs('notifiable');
            $table->text('data');
            $table->timestamp('read_at')->nullable();
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
                'dni' => 'PF100',
                'nombre' => 'Maria',
                'apellido1' => 'Garcia',
                'apellido2' => 'Lopez',
                'email' => 'pf100@test.local',
                'rol' => config('roles.rol.profesor'),
                'activo' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'dni' => 'PF200',
                'nombre' => 'Joan',
                'apellido1' => 'Soler',
                'apellido2' => 'Perez',
                'email' => 'pf200@test.local',
                'rol' => config('roles.rol.profesor'),
                'activo' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        DB::connection('sqlite')->table('faltas')->insert([
            [
                'id' => 1,
                'idProfesor' => 'PF100',
                'baja' => 0,
                'dia_completo' => 1,
                'desde' => '2026-03-10',
                'hasta' => '2026-03-10',
                'motivos' => 1,
                'observaciones' => 'Sense document',
                'fichero' => null,
                'estado' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'idProfesor' => 'PF100',
                'baja' => 0,
                'dia_completo' => 1,
                'desde' => '2026-03-11',
                'hasta' => '2026-03-11',
                'motivos' => 2,
                'observaciones' => 'Amb document',
                'fichero' => 'faltas/doc.pdf',
                'estado' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'idProfesor' => 'PF200',
                'baja' => 0,
                'dia_completo' => 1,
                'desde' => '2026-03-12',
                'hasta' => '2026-03-12',
                'motivos' => 3,
                'observaciones' => 'Autoritzada',
                'fichero' => null,
                'estado' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
