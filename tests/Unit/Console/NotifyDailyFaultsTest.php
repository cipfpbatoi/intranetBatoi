<?php

declare(strict_types=1);

namespace Tests\Unit\Console;

use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Schema;
use Intranet\Application\Comision\ComisionService;
use Intranet\Application\Horario\HorarioService;
use Intranet\Application\Profesor\ProfesorService;
use Intranet\Console\Commands\NotifyDailyFaults;
use Intranet\Entities\Profesor;
use Intranet\Notifications\mensajePanel;
use Illuminate\Support\Carbon;
use Mockery;
use Tests\TestCase;

/**
 * Proves de regressió per als avisos diaris de fitxatge.
 */
class NotifyDailyFaultsTest extends TestCase
{
    /**
     * Prepara una base de dades sqlite mínima per a provar la comanda.
     */
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('database.default', 'sqlite');
        config()->set('database.connections.sqlite.database', ':memory:');
        config()->set('variables.controlDiario', true);
        DB::purge('sqlite');
        DB::reconnect('sqlite');

        session(['lang' => 'ca']);

        $this->createSchema();
    }

    /**
     * Neteja els mocks i la data simulada.
     */
    protected function tearDown(): void
    {
        Carbon::setTestNow();
        Mockery::close();

        parent::tearDown();
    }

    /**
     * Verifica que no s'envien avisos quan el dia és no lectiu.
     */
    public function test_handle_no_envia_avisos_en_dia_no_lectiu(): void
    {
        Carbon::setTestNow('2026-04-01 21:30:00');
        Notification::fake();

        DB::table('calendari_escolar')->insert([
            'data' => '2026-04-01',
            'tipus' => 'no lectiu',
            'esdeveniment' => 'Vacances',
        ]);

        $profesorService = Mockery::mock(ProfesorService::class);
        $profesorService->shouldReceive('activosOrdered')->never();
        $profesorService->shouldReceive('find')->never();
        $this->app->instance(ProfesorService::class, $profesorService);

        $horarioService = Mockery::mock(HorarioService::class);
        $horarioService->shouldReceive('countByProfesorAndDay')->never();
        $this->app->instance(HorarioService::class, $horarioService);

        $comisionService = Mockery::mock(ComisionService::class);
        $comisionService->shouldReceive('byDay')->never();
        $this->app->instance(ComisionService::class, $comisionService);

        $resultat = (new NotifyDailyFaults())->handle();

        $this->assertSame(0, $resultat);
        Notification::assertNothingSent();
    }

    /**
     * Verifica que no s'envien avisos quan el dia és festiu.
     */
    public function test_handle_no_envia_avisos_en_dia_festiu(): void
    {
        Carbon::setTestNow('2026-04-01 21:30:00');
        Notification::fake();

        DB::table('calendari_escolar')->insert([
            'data' => '2026-04-01',
            'tipus' => 'festiu',
            'esdeveniment' => 'Festivitat local',
        ]);

        $profesorService = Mockery::mock(ProfesorService::class);
        $profesorService->shouldReceive('activosOrdered')->never();
        $profesorService->shouldReceive('find')->never();
        $this->app->instance(ProfesorService::class, $profesorService);

        $horarioService = Mockery::mock(HorarioService::class);
        $horarioService->shouldReceive('countByProfesorAndDay')->never();
        $this->app->instance(HorarioService::class, $horarioService);

        $comisionService = Mockery::mock(ComisionService::class);
        $comisionService->shouldReceive('byDay')->never();
        $this->app->instance(ComisionService::class, $comisionService);

        $resultat = (new NotifyDailyFaults())->handle();

        $this->assertSame(0, $resultat);
        Notification::assertNothingSent();
    }

    /**
     * Verifica que els avisos es mantenen en dies lectius.
     */
    public function test_handle_envia_avis_professor_en_dia_lectiu_si_no_ha_fitxat(): void
    {
        Carbon::setTestNow('2026-04-01 21:30:00');
        Notification::fake();

        DB::table('calendari_escolar')->insert([
            'data' => '2026-04-01',
            'tipus' => 'lectiu',
            'esdeveniment' => null,
        ]);

        $profesor = new Profesor([
            'dni' => 'P200',
            'nombre' => 'Anna',
            'apellido1' => 'Prova',
            'apellido2' => 'Centre',
            'email' => 'anna@example.com',
        ]);
        $profesor->dni = 'P200';

        $profesorService = Mockery::mock(ProfesorService::class);
        $profesorService->shouldReceive('activosOrdered')
            ->once()
            ->andReturn(new EloquentCollection([$profesor]));
        $profesorService->shouldReceive('find')
            ->once()
            ->with('P200')
            ->andReturn($profesor);
        $this->app->instance(ProfesorService::class, $profesorService);

        $horarioService = Mockery::mock(HorarioService::class);
        $horarioService->shouldReceive('countByProfesorAndDay')
            ->once()
            ->with('P200', Mockery::type('string'))
            ->andReturn(2);
        $this->app->instance(HorarioService::class, $horarioService);

        $comisionService = Mockery::mock(ComisionService::class);
        $comisionService->shouldReceive('byDay')
            ->once()
            ->with('2026-04-01')
            ->andReturn(new EloquentCollection());
        $this->app->instance(ComisionService::class, $comisionService);

        $resultat = (new NotifyDailyFaults())->handle();

        $this->assertSame(0, $resultat);
        Notification::assertSentTo(
            $profesor,
            mensajePanel::class,
            function (mensajePanel $notification, array $channels, Profesor $notifiable): bool {
                $payload = $notification->toArray($notifiable);

                return ($payload['motiu'] ?? null) === 'No has fitxat hui dia 01-04-2026'
                    && ($payload['emissor'] ?? null) === 'Sistema';
            }
        );
    }

    /**
     * Verifica que les faltes de dia complet o coincidents no generen avís de guàrdia.
     */
    public function test_handle_no_avisar_guardies_justificades_i_mante_les_no_justificades(): void
    {
        Carbon::setTestNow('2026-04-01 21:30:00');
        Notification::fake();

        DB::table('calendari_escolar')->insert([
            'data' => '2026-04-01',
            'tipus' => 'lectiu',
            'esdeveniment' => null,
        ]);
        DB::table('horas')->insert([
            ['codigo' => 1, 'hora_ini' => '08:00:00', 'hora_fin' => '09:00:00'],
            ['codigo' => 2, 'hora_ini' => '10:00:00', 'hora_fin' => '11:00:00'],
        ]);
        DB::table('guardias')->insert([
            ['idProfesor' => 'P001', 'dia' => '2026-04-01', 'hora' => 1, 'realizada' => -1],
            ['idProfesor' => 'P002', 'dia' => '2026-04-01', 'hora' => 1, 'realizada' => -1],
            ['idProfesor' => 'P003', 'dia' => '2026-04-01', 'hora' => 2, 'realizada' => -1],
            ['idProfesor' => 'P004', 'dia' => '2026-04-01', 'hora' => 1, 'realizada' => -1],
        ]);
        DB::table('faltas')->insert([
            [
                'idProfesor' => 'P001',
                'desde' => '2026-04-01',
                'hasta' => '2026-04-01',
                'dia_completo' => 1,
                'hora_ini' => null,
                'hora_fin' => null,
                'estado' => 3,
            ],
            [
                'idProfesor' => 'P002',
                'desde' => '2026-04-01',
                'hasta' => '2026-04-01',
                'dia_completo' => 0,
                'hora_ini' => '08:00:00',
                'hora_fin' => '09:00:00',
                'estado' => 3,
            ],
            [
                'idProfesor' => 'P003',
                'desde' => '2026-04-01',
                'hasta' => '2026-04-01',
                'dia_completo' => 0,
                'hora_ini' => '08:00:00',
                'hora_fin' => '09:00:00',
                'estado' => 3,
            ],
        ]);

        $profesores = collect(['P001', 'P002', 'P003', 'P004'])
            ->mapWithKeys(function (string $dni): array {
                $profesor = new Profesor(['dni' => $dni, 'email' => strtolower($dni) . '@example.com']);
                $profesor->dni = $dni;

                return [$dni => $profesor];
            });
        $profesorService = Mockery::mock(ProfesorService::class);
        $profesorService->shouldReceive('activosOrdered')->once()->andReturn(new EloquentCollection());
        $profesorService->shouldReceive('find')->with(Mockery::type('string'))->andReturnUsing(
            fn (string $dni): ?Profesor => $profesores->get($dni)
        );
        $this->app->instance(ProfesorService::class, $profesorService);

        $horarioService = Mockery::mock(HorarioService::class);
        $horarioService->shouldReceive('countByProfesorAndDay')->never();
        $this->app->instance(HorarioService::class, $horarioService);

        $comisionService = Mockery::mock(ComisionService::class);
        $comisionService->shouldReceive('byDay')->once()->with('2026-04-01')->andReturn(new EloquentCollection());
        $this->app->instance(ComisionService::class, $comisionService);

        $resultat = (new NotifyDailyFaults())->handle();

        $this->assertSame(0, $resultat);
        Notification::assertNotSentTo($profesores['P001'], mensajePanel::class);
        Notification::assertNotSentTo($profesores['P002'], mensajePanel::class);
        Notification::assertSentTo($profesores['P003'], mensajePanel::class);
        Notification::assertSentTo($profesores['P004'], mensajePanel::class);
    }

    /**
     * Crea les taules mínimes per a executar la comanda en proves.
     */
    private function createSchema(): void
    {
        $schema = Schema::connection('sqlite');

        $schema->create('calendari_escolar', function (Blueprint $table): void {
            $table->increments('id');
            $table->date('data')->unique();
            $table->string('tipus');
            $table->string('esdeveniment')->nullable();
            $table->timestamps();
        });

        $schema->create('faltas_profesores', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('idProfesor', 10);
            $table->date('dia');
            $table->time('entrada')->nullable();
            $table->time('salida')->nullable();
            $table->timestamps();
        });

        $schema->create('guardias', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('idProfesor', 10)->nullable();
            $table->date('dia');
            $table->unsignedInteger('hora')->nullable();
            $table->integer('realizada')->default(-1);
            $table->timestamps();
        });

        $schema->create('faltas', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('idProfesor', 10)->nullable();
            $table->date('desde');
            $table->date('hasta');
            $table->boolean('dia_completo')->default(true);
            $table->time('hora_ini')->nullable();
            $table->time('hora_fin')->nullable();
            $table->integer('estado')->default(0);
            $table->timestamps();
        });

        $schema->create('horas', function (Blueprint $table): void {
            $table->unsignedInteger('codigo')->primary();
            $table->time('hora_ini');
            $table->time('hora_fin');
        });

        $schema->create('actividades', function (Blueprint $table): void {
            $table->increments('id');
            $table->dateTime('desde');
            $table->dateTime('hasta');
            $table->boolean('fueraCentro')->default(true);
            $table->timestamps();
        });
    }
}
