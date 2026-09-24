<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Intranet\Application\Empresa\EmpresaDataConfirmationService;
use Intranet\Entities\EmpresaDataConfirmation;
use Intranet\Entities\Profesor;
use Intranet\Mail\EmpresaDataConfirmationMail;
use Intranet\Mail\EmpresaDataConfirmedMail;
use Tests\TestCase;

/**
 * Proves del flux públic de confirmació de dades d'empresa.
 */
class EmpresaDataConfirmationTest extends TestCase
{
    use WithoutModelEvents;

    protected function setUp(): void
    {
        parent::setUp();
        config()->set('database.default', 'sqlite');
        config()->set('database.connections.sqlite.database', ':memory:');
        DB::purge('sqlite');
        DB::reconnect('sqlite');
        $this->createSchema();
        $this->seedData();
        Mail::fake();
    }

    public function test_envia_un_correu_per_empresa_sense_exigir_estat_de_colaboracio(): void
    {
        Mail::fake();
        $service = app(EmpresaDataConfirmationService::class);
        $tutor = Profesor::findOrFail('TUTOR1');

        $sent = $service->sendForTutor([1], $tutor);

        $this->assertCount(1, $sent);
        $this->assertSame([101, 102, 103], $sent->first()->colaboracion_ids);
        $this->assertSame([10, 11], $sent->first()->centro_ids);
        $this->assertNotNull($sent->first()->sent_at);
        $this->assertSame(64, strlen($sent->first()->token_hash));
        Mail::assertSent(EmpresaDataConfirmationMail::class, function (EmpresaDataConfirmationMail $mail): bool {
            $mail->build();

            return $mail->hasFrom('tutor@example.test')
                && $mail->hasReplyTo('tutor@example.test');
        });
        $html = html_entity_decode(Mail::sent(EmpresaDataConfirmationMail::class)->first()->render());
        $this->assertStringContainsString('A causa del canvi en l’aplicació de gestió de les pràctiques', $html);
        $this->assertStringContainsString('Debido al cambio en la aplicación de gestión de las prácticas', $html);
        $this->assertStringContainsString('dades disponibles al centre', $html);
        $this->assertStringContainsString('datos disponibles en el centro', $html);
        $this->assertStringNotContainsString('dades disponibles en la intranet', $html);
        $this->assertStringContainsString('Posteriorment enviarem al coordinador o coordinadora', $html);
    }

    public function test_el_selector_i_enviament_web_usen_la_sessio_del_professor(): void
    {
        Mail::fake();
        $tutor = Profesor::findOrFail('TUTOR1');

        $this->actingAs($tutor, 'profesor')
            ->get(route('empresa.confirmacio.options'))
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', 1)
            ->assertJsonPath('data.0.marked', true);

        $this->actingAs($tutor, 'profesor')
            ->post(route('empresa.confirmacio.send'), ['1' => 'on'])
            ->assertRedirect(route('colaboracion.mias'));

        $this->assertDatabaseHas('empresa_data_confirmations', [
            'empresa_id' => 1,
            'tutor_dni' => 'TUTOR1',
            'recipient_email' => 'empresa@example.test',
        ]);
        Mail::assertSent(EmpresaDataConfirmationMail::class, 1);

        $this->actingAs($tutor, 'profesor')
            ->get(route('empresa.confirmacio.options'))
            ->assertJsonPath('data.0.marked', false);
    }

    public function test_inclou_tots_els_cicles_de_lempresa_encara_que_siguen_dun_altre_tutor(): void
    {
        Mail::fake();
        $service = app(EmpresaDataConfirmationService::class);

        $sent = $service->sendForTutor([1, 2], Profesor::findOrFail('TUTOR1'));

        $this->assertSame([101, 102, 103], $sent->first()->colaboracion_ids);

        $confirmation = $sent->first();
        $this->assertSame(
            [2, 1, 3],
            $service->collaborations($confirmation)->pluck('idCiclo')->all()
        );
    }

    public function test_un_nou_enviament_invalida_el_token_anterior_pendent(): void
    {
        Mail::fake();
        $service = app(EmpresaDataConfirmationService::class);
        $tutor = Profesor::findOrFail('TUTOR1');

        $previous = $service->sendForTutor([1], $tutor)->first();
        $current = $service->sendForTutor([1], $tutor)->first();

        $this->assertFalse($previous->fresh()->isActionable());
        $this->assertTrue($current->fresh()->isActionable());
        Mail::assertSent(EmpresaDataConfirmationMail::class, 2);
    }

    public function test_el_token_public_no_inicia_sessio_i_no_exposa_altres_empreses(): void
    {
        $plainToken = 'token-public-de-prova';
        EmpresaDataConfirmation::create([
            'empresa_id' => 1,
            'tutor_dni' => 'TUTOR1',
            'recipient_email' => 'empresa@example.test',
            'token_hash' => hash('sha256', $plainToken),
            'colaboracion_ids' => [101, 102, 103],
            'centro_ids' => [10, 11, 12],
            'sent_at' => now(),
            'expires_at' => now()->addDay(),
        ]);

        $response = $this->get(route('empresa.confirmacio.show', ['token' => $plainToken]));

        $response->assertOk();
        $response->assertSee('Empresa Un');
        $response->assertSee('Confirme la formació:');
        $response->assertSee('Ciclo C');
        $response->assertDontSee('Centre Sense Col·laboració');
        $response->assertDontSee('Instructor Sense Cicle');
        $response->assertSeeInOrder(['Centre Dos', 'Centre U']);
        $response->assertSee('Este instructor ja no està vinculat a este centre');
        $response->assertDontSee('Empresa Dos');
        $this->assertGuest();
    }

    public function test_confirma_dades_prefarcides_i_bloqueja_reutilitzar_el_token(): void
    {
        $service = app(EmpresaDataConfirmationService::class);
        $confirmation = EmpresaDataConfirmation::create([
            'empresa_id' => 1,
            'tutor_dni' => 'TUTOR1',
            'recipient_email' => 'empresa@example.test',
            'token_hash' => hash('sha256', 'token'),
            'colaboracion_ids' => [101, 102],
            'centro_ids' => [10, 11, 12],
            'sent_at' => now(),
            'expires_at' => now()->addDay(),
        ]);

        $data = $this->validConfirmationData();
        $service->confirm($confirmation, $data);

        $this->assertDatabaseHas('empresas', [
            'id' => 1,
            'gerente' => 'Maria Gerent',
            'nif_gerente' => '12345678Z',
        ]);
        $this->assertDatabaseHas('instructores', [
            'dni' => '87654321X',
            'email' => 'instructora@example.test',
            'coordinador' => 1,
        ]);
        $this->assertDatabaseHas('instructores', [
            'dni' => '22222222J',
            'coordinador' => 0,
        ]);
        $this->assertDatabaseHas('centros_instructores', [
            'idCentro' => 10,
            'idInstructor' => '87654321X',
        ]);
        $this->assertNotNull($confirmation->fresh()->confirmed_at);
        Mail::assertSent(EmpresaDataConfirmedMail::class, function (EmpresaDataConfirmedMail $mail): bool {
            return $mail->hasTo('tutor@example.test');
        });
        $this->assertStringContainsString(
            'Empresa Un',
            Mail::sent(EmpresaDataConfirmedMail::class)->first()->render()
        );

        $this->expectException(ValidationException::class);
        $service->confirm($confirmation->fresh(), $data);
    }

    public function test_rebutja_una_confirmacio_sense_totes_les_formacions(): void
    {
        $service = app(EmpresaDataConfirmationService::class);
        $confirmation = EmpresaDataConfirmation::create([
            'empresa_id' => 1,
            'tutor_dni' => 'TUTOR1',
            'recipient_email' => 'empresa@example.test',
            'token_hash' => hash('sha256', 'token'),
            'colaboracion_ids' => [101, 102],
            'centro_ids' => [10, 11, 12],
            'expires_at' => now()->addDay(),
        ]);
        $data = $this->validConfirmationData();
        $data['confirmed_colaborations'] = [101];

        $this->expectException(ValidationException::class);
        $service->confirm($confirmation, $data);
    }

    public function test_desvincula_un_instructor_del_centre_sense_esborrar_altres_vinculacions(): void
    {
        $service = app(EmpresaDataConfirmationService::class);
        $confirmation = $this->confirmation();
        $data = $this->validConfirmationData();
        $data['instructor_removals'] = [10 => ['11111111H']];
        $data['coordinator_dni'] = '22222222J';

        $service->confirm($confirmation, $data);

        $this->assertDatabaseMissing('centros_instructores', [
            'idCentro' => 10,
            'idInstructor' => '11111111H',
        ]);
        $this->assertDatabaseHas('centros_instructores', [
            'idCentro' => 11,
            'idInstructor' => '11111111H',
        ]);
        $this->assertDatabaseHas('centros_instructores', [
            'idCentro' => 12,
            'idInstructor' => '11111111H',
        ]);
        $this->assertDatabaseHas('instructores', ['dni' => '11111111H']);
    }

    public function test_no_permet_eliminar_del_centre_el_coordinador_seleccionat(): void
    {
        $service = app(EmpresaDataConfirmationService::class);
        $confirmation = $this->confirmation();
        $data = $this->validConfirmationData();
        $data['instructor_removals'] = [11 => ['22222222J']];
        $data['coordinator_dni'] = '22222222J';

        $this->expectException(ValidationException::class);
        $service->confirm($confirmation, $data);
    }

    public function test_conserva_un_instructor_desvinculat_si_te_historic_fct(): void
    {
        DB::table('fcts')->insert(['idInstructor' => '22222222J']);
        $service = app(EmpresaDataConfirmationService::class);
        $confirmation = $this->confirmation();
        $data = $this->validConfirmationData();
        $data['instructors']['new'] = [];
        $data['instructor_removals'] = [11 => ['22222222J']];
        $data['coordinator_dni'] = '11111111H';

        $service->confirm($confirmation, $data);

        $this->assertDatabaseMissing('centros_instructores', [
            'idCentro' => 11,
            'idInstructor' => '22222222J',
        ]);
        $this->assertDatabaseHas('instructores', ['dni' => '22222222J']);
    }

    public function test_un_error_en_lavis_al_tutor_no_desfa_la_confirmacio(): void
    {
        Mail::shouldReceive('to')->once()->andThrow(new \RuntimeException('SMTP no disponible'));
        Log::spy();
        $service = app(EmpresaDataConfirmationService::class);
        $confirmation = $this->confirmation();

        $service->confirm($confirmation, $this->validConfirmationData());

        $this->assertNotNull($confirmation->fresh()->confirmed_at);
        Log::shouldHaveReceived('warning')->once();
    }

    /**
     * Crea una confirmació accionable per a l'empresa de prova.
     */
    private function confirmation(): EmpresaDataConfirmation
    {
        return EmpresaDataConfirmation::create([
            'empresa_id' => 1,
            'tutor_dni' => 'TUTOR1',
            'recipient_email' => 'empresa@example.test',
            'token_hash' => hash('sha256', uniqid('token-', true)),
            'colaboracion_ids' => [101, 102],
            'centro_ids' => [10, 11, 12],
            'sent_at' => now(),
            'expires_at' => now()->addDay(),
        ]);
    }

    /**
     * Retorna una confirmació completa per als dos centres de prova.
     *
     * @return array<string, mixed>
     */
    private function validConfirmationData(): array
    {
        return [
            'empresa' => [
                'nombre' => 'Empresa Un', 'cif' => 'B12345678', 'email' => 'empresa@example.test',
                'telefono' => '965000000', 'direccion' => 'Carrer Major, 1', 'localidad' => 'Alcoi',
                'gerente' => 'Maria Gerent', 'nif_gerente' => '12345678z',
            ],
            'confirmed_colaborations' => [101, 102, 103],
            'centers' => [
                10 => ['nombre' => 'Centre U', 'email' => 'centre@example.test', 'telefono' => '965000001', 'direccion' => 'C/ U', 'localidad' => 'Alcoi', 'horarios' => 'Matí'],
                11 => ['nombre' => 'Centre Dos', 'email' => null, 'telefono' => null, 'direccion' => 'C/ Dos', 'localidad' => 'Alcoi', 'horarios' => null],
            ],
            'instructors' => [
                'existing' => [[
                        'dni' => '11111111H', 'name' => 'Anna', 'surnames' => 'Instructora',
                        'email' => 'anna@example.test', 'telefono' => '611111111',
                ], [
                        'dni' => '22222222J', 'name' => 'Pere', 'surnames' => 'Coordinador anterior',
                        'email' => 'pere@example.test', 'telefono' => null,
                ]],
                'new' => ['dni' => '87654321X', 'name' => 'Nova', 'surnames' => 'Instructora', 'email' => 'instructora@example.test', 'telefono' => '622222222', 'center_ids' => [10]],
            ],
            'coordinator_dni' => '__new__',
        ];
    }

    /**
     * Crea l'esquema mínim del flux de confirmació.
     */
    private function createSchema(): void
    {
        $schema = Schema::connection('sqlite');
        $schema->create('profesores', function (Blueprint $table): void {
            $table->string('dni')->primary(); $table->string('nombre'); $table->string('apellido1')->nullable();
            $table->string('apellido2')->nullable(); $table->string('email'); $table->unsignedInteger('rol')->default(3);
            $table->boolean('activo')->default(true); $table->date('fecha_baja')->nullable(); $table->timestamps();
        });
        $schema->create('empresas', function (Blueprint $table): void {
            $table->increments('id'); $table->string('nombre'); $table->string('cif')->nullable(); $table->string('email')->nullable();
            $table->string('telefono')->nullable(); $table->string('direccion'); $table->string('localidad');
            $table->string('gerente')->nullable(); $table->string('nif_gerente')->nullable(); $table->timestamps();
        });
        $schema->create('centros', function (Blueprint $table): void {
            $table->increments('id'); $table->unsignedInteger('idEmpresa'); $table->string('nombre'); $table->string('email')->nullable();
            $table->string('telefono')->nullable(); $table->string('direccion'); $table->string('localidad'); $table->string('horarios')->nullable(); $table->timestamps();
        });
        $schema->create('ciclos', function (Blueprint $table): void {
            $table->increments('id'); $table->string('ciclo')->nullable(); $table->string('vliteral')->nullable(); $table->string('cliteral')->nullable(); $table->unsignedInteger('departamento')->nullable(); $table->unsignedTinyInteger('tipo')->default(1);
        });
        $schema->create('colaboraciones', function (Blueprint $table): void {
            $table->increments('id'); $table->unsignedInteger('idCentro'); $table->unsignedInteger('idCiclo'); $table->string('tutor')->nullable();
            $table->unsignedTinyInteger('estado')->default(1); $table->string('email')->nullable(); $table->timestamps();
        });
        $schema->create('instructores', function (Blueprint $table): void {
            $table->string('dni')->primary(); $table->string('name'); $table->string('surnames')->default('');
            $table->string('email')->nullable(); $table->string('telefono')->nullable(); $table->string('departamento')->nullable();
            $table->boolean('coordinador')->default(false);
        });
        $schema->create('centros_instructores', function (Blueprint $table): void {
            $table->unsignedInteger('idCentro'); $table->string('idInstructor'); $table->unique(['idCentro', 'idInstructor']);
        });
        $schema->create('fcts', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('idInstructor')->nullable();
        });
        $schema->create('empresa_data_confirmations', function (Blueprint $table): void {
            $table->id(); $table->unsignedInteger('empresa_id'); $table->string('tutor_dni'); $table->string('recipient_email');
            $table->string('token_hash')->unique(); $table->json('colaboracion_ids'); $table->json('centro_ids'); $table->timestamp('sent_at')->nullable();
            $table->timestamp('expires_at'); $table->timestamp('confirmed_at')->nullable(); $table->timestamps();
        });
    }

    /**
     * Inserix tutors, empreses, centres, cicles, col·laboracions i un instructor.
     */
    private function seedData(): void
    {
        DB::table('profesores')->insert([
            ['dni' => 'TUTOR1', 'nombre' => 'Tutor', 'apellido1' => 'Primer', 'email' => 'tutor@example.test', 'created_at' => now(), 'updated_at' => now()],
            ['dni' => 'TUTOR2', 'nombre' => 'Tutora', 'apellido1' => 'Segona', 'email' => 'tutora@example.test', 'created_at' => now(), 'updated_at' => now()],
        ]);
        DB::table('empresas')->insert([
            ['id' => 1, 'nombre' => 'Empresa Un', 'cif' => 'B12345678', 'email' => 'empresa@example.test', 'telefono' => '965000000', 'direccion' => 'C/ Major', 'localidad' => 'Alcoi', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'nombre' => 'Empresa Dos', 'cif' => 'B87654321', 'email' => 'dos@example.test', 'telefono' => '965000002', 'direccion' => 'C/ Dos', 'localidad' => 'Alcoi', 'created_at' => now(), 'updated_at' => now()],
        ]);
        DB::table('centros')->insert([
            ['id' => 10, 'idEmpresa' => 1, 'nombre' => 'Centre U', 'direccion' => 'C/ U', 'localidad' => 'Alcoi', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 11, 'idEmpresa' => 1, 'nombre' => 'Centre Dos', 'direccion' => 'C/ Dos', 'localidad' => 'Alcoi', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 12, 'idEmpresa' => 1, 'nombre' => 'Centre Sense Col·laboració', 'direccion' => 'C/ Tres', 'localidad' => 'Alcoi', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 20, 'idEmpresa' => 2, 'nombre' => 'Centre Altre', 'direccion' => 'C/ Altre', 'localidad' => 'Alcoi', 'created_at' => now(), 'updated_at' => now()],
        ]);
        DB::table('ciclos')->insert([
            ['id' => 1, 'ciclo' => 'A', 'vliteral' => 'Cicle A', 'cliteral' => 'Ciclo A'],
            ['id' => 2, 'ciclo' => 'B', 'vliteral' => 'Cicle B', 'cliteral' => 'Ciclo B'],
            ['id' => 3, 'ciclo' => 'C', 'vliteral' => 'Cicle C', 'cliteral' => 'Ciclo C'],
        ]);
        DB::table('colaboraciones')->insert([
            ['id' => 101, 'idCentro' => 10, 'idCiclo' => 1, 'tutor' => 'TUTOR1', 'estado' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 102, 'idCentro' => 11, 'idCiclo' => 2, 'tutor' => 'TUTOR1', 'estado' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 103, 'idCentro' => 10, 'idCiclo' => 3, 'tutor' => 'TUTOR2', 'estado' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 201, 'idCentro' => 20, 'idCiclo' => 1, 'tutor' => 'TUTOR2', 'estado' => 2, 'created_at' => now(), 'updated_at' => now()],
        ]);
        DB::table('instructores')->insert([
            ['dni' => '11111111H', 'name' => 'Anna', 'surnames' => 'Instructora', 'email' => 'anna@example.test', 'coordinador' => false],
            ['dni' => '22222222J', 'name' => 'Pere', 'surnames' => 'Coordinador anterior', 'email' => 'pere@example.test', 'coordinador' => true],
            ['dni' => '33333333P', 'name' => 'Instructor', 'surnames' => 'Sense Cicle', 'email' => 'sense@example.test', 'coordinador' => false],
        ]);
        DB::table('centros_instructores')->insert([
            ['idCentro' => 10, 'idInstructor' => '11111111H'],
            ['idCentro' => 11, 'idInstructor' => '11111111H'],
            ['idCentro' => 11, 'idInstructor' => '22222222J'],
            ['idCentro' => 12, 'idInstructor' => '11111111H'],
            ['idCentro' => 12, 'idInstructor' => '33333333P'],
        ]);
    }
}
