<?php

declare(strict_types=1);

namespace Tests\Feature\Console;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Intranet\Services\Notifications\NotificationService;
use Mockery;
use Tests\TestCase;

/**
 * Proves de la notificació programada del feedback pendent.
 */
class NotifyPendingTutoriaFeedbackTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('database.default', 'sqlite');
        config()->set('database.connections.sqlite.database', ':memory:');
        DB::purge('sqlite');
        DB::reconnect('sqlite');
        Carbon::setTestNow('2026-09-30 07:15:00');

        $this->createSchema();
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        Mockery::close();
        parent::tearDown();
    }

    public function test_ordre_notifica_una_vegada_per_setmana_fins_que_arriba_el_feedback(): void
    {
        DB::table('ciclos')->insert(['id' => 1, 'tipo' => 1]);
        DB::table('profesores')->insert([
            'dni' => 'TUTOR1',
            'nombre' => 'Anna',
            'sustituye_a' => null,
        ]);
        DB::table('grupos')->insert([
            'codigo' => 'G1',
            'nombre' => 'Grup 1',
            'idCiclo' => 1,
            'tutor' => 'TUTOR1',
        ]);
        DB::table('tutorias')->insert([
            'id' => 20,
            'descripcion' => 'Convivència',
            'grupos' => 0,
            'hasta' => '2026-09-29',
        ]);

        $notifications = Mockery::mock(NotificationService::class);
        $notifications->shouldReceive('send')
            ->twice()
            ->with(
                'TUTOR1',
                Mockery::on(static fn (string $message): bool => str_contains($message, 'Convivència')),
                '/tutoria/20/anexo',
                'Sistema'
            );
        $this->app->instance(NotificationService::class, $notifications);

        $this->artisan('tutories:notifica-feedback-pendent')
            ->expectsOutput("S'han enviat 1 avisos de feedback pendent.")
            ->assertSuccessful();
        $this->artisan('tutories:notifica-feedback-pendent')
            ->expectsOutput("S'han enviat 0 avisos de feedback pendent.")
            ->assertSuccessful();

        Carbon::setTestNow('2026-10-09 07:15:00');
        $this->artisan('tutories:notifica-feedback-pendent')
            ->expectsOutput("S'han enviat 1 avisos de feedback pendent.")
            ->assertSuccessful();

        DB::table('tutorias_grupos')->insert([
            'idTutoria' => 20,
            'idGrupo' => 'G1',
            'observaciones' => 'Feedback completat',
        ]);
        Carbon::setTestNow('2026-10-16 07:15:00');
        $this->artisan('tutories:notifica-feedback-pendent')
            ->expectsOutput("S'han enviat 0 avisos de feedback pendent.")
            ->assertSuccessful();

        $this->assertDatabaseCount('tutoria_feedback_notifications', 2);
        $this->assertDatabaseHas('tutoria_feedback_notifications', [
            'idTutoria' => 20,
            'idGrupo' => 'G1',
            'week_start' => '2026-09-28',
        ]);
        $this->assertDatabaseHas('tutoria_feedback_notifications', [
            'idTutoria' => 20,
            'idGrupo' => 'G1',
            'week_start' => '2026-10-05',
        ]);
    }

    private function createSchema(): void
    {
        Schema::create('ciclos', function (Blueprint $table): void {
            $table->increments('id');
            $table->unsignedTinyInteger('tipo');
        });
        Schema::create('profesores', function (Blueprint $table): void {
            $table->string('dni', 10)->primary();
            $table->string('nombre');
            $table->string('sustituye_a', 10)->nullable();
        });
        Schema::create('grupos', function (Blueprint $table): void {
            $table->string('codigo', 10)->primary();
            $table->string('nombre');
            $table->unsignedInteger('idCiclo')->nullable();
            $table->string('tutor', 10)->nullable();
            $table->timestamps();
        });
        Schema::create('tutorias', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('descripcion');
            $table->unsignedTinyInteger('grupos')->default(0);
            $table->date('hasta');
        });
        Schema::create('tutorias_grupos', function (Blueprint $table): void {
            $table->increments('id');
            $table->unsignedInteger('idTutoria');
            $table->string('idGrupo', 10);
            $table->text('observaciones');
        });
        Schema::create('tutoria_feedback_notifications', function (Blueprint $table): void {
            $table->id();
            $table->unsignedInteger('idTutoria');
            $table->string('idGrupo', 10);
            $table->date('week_start');
            $table->timestamps();
            $table->unique(['idTutoria', 'idGrupo', 'week_start']);
        });
    }
}
