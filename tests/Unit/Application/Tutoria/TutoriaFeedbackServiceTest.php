<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Tutoria;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Intranet\Application\Tutoria\TutoriaFeedbackService;
use Intranet\Entities\Tutoria;
use Intranet\Services\Notifications\NotificationService;
use Mockery;
use Tests\TestCase;

/**
 * Proves del càlcul de progrés de les tutories.
 */
class TutoriaFeedbackServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('database.default', 'sqlite');
        config()->set('database.connections.sqlite.database', ':memory:');
        DB::purge('sqlite');
        DB::reconnect('sqlite');

        $this->createSchema();
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_calcula_realitzades_total_i_percentatge_per_tipus_de_cicle(): void
    {
        DB::table('ciclos')->insert([
            ['id' => 1, 'tipo' => 1],
            ['id' => 2, 'tipo' => 2],
        ]);
        DB::table('grupos')->insert([
            ['codigo' => 'GM1', 'nombre' => 'Mitjà 1', 'idCiclo' => 1],
            ['codigo' => 'GM2', 'nombre' => 'Mitjà 2', 'idCiclo' => 1],
            ['codigo' => 'GM3', 'nombre' => 'Mitjà 3', 'idCiclo' => 1],
            ['codigo' => 'GM4', 'nombre' => 'Mitjà 4', 'idCiclo' => 1],
            ['codigo' => 'GS1', 'nombre' => 'Superior 1', 'idCiclo' => 2],
        ]);
        DB::table('tutorias')->insert([
            'id' => 10,
            'descripcion' => 'Tutoria de prova',
            'grupos' => 1,
            'hasta' => '2026-09-20',
        ]);
        DB::table('tutorias_grupos')->insert([
            ['idTutoria' => 10, 'idGrupo' => 'GM1', 'observaciones' => 'Feta'],
            ['idTutoria' => 10, 'idGrupo' => 'GM2', 'observaciones' => '<p>Feta</p>'],
            ['idTutoria' => 10, 'idGrupo' => 'GM3', 'observaciones' => 'També feta'],
            ['idTutoria' => 10, 'idGrupo' => 'GS1', 'observaciones' => 'No aplicable'],
        ]);

        $service = $this->service();

        $this->assertSame(
            ['completed' => 3, 'total' => 4, 'percentage' => 75],
            $service->progress(Tutoria::findOrFail(10))
        );
    }

    public function test_no_compta_observacions_buides_amb_espais_o_html(): void
    {
        DB::table('ciclos')->insert(['id' => 1, 'tipo' => 1]);
        DB::table('grupos')->insert([
            ['codigo' => 'G1', 'nombre' => 'Grup 1', 'idCiclo' => 1],
            ['codigo' => 'G2', 'nombre' => 'Grup 2', 'idCiclo' => 1],
            ['codigo' => 'G3', 'nombre' => 'Grup 3', 'idCiclo' => 1],
            ['codigo' => 'G4', 'nombre' => 'Grup 4', 'idCiclo' => 1],
        ]);
        DB::table('tutorias')->insert([
            'id' => 11,
            'descripcion' => 'Tutoria buida',
            'grupos' => 0,
            'hasta' => '2026-09-20',
        ]);
        DB::table('tutorias_grupos')->insert([
            ['idTutoria' => 11, 'idGrupo' => 'G1', 'observaciones' => ''],
            ['idTutoria' => 11, 'idGrupo' => 'G2', 'observaciones' => '   '],
            ['idTutoria' => 11, 'idGrupo' => 'G3', 'observaciones' => '<p><br>&nbsp;</p>'],
            ['idTutoria' => 11, 'idGrupo' => 'G4', 'observaciones' => "<div>\u{200B}</div>"],
        ]);

        $this->assertSame(
            ['completed' => 0, 'total' => 4, 'percentage' => 0],
            $this->service()->progress(Tutoria::findOrFail(11))
        );
    }

    private function service(): TutoriaFeedbackService
    {
        return new TutoriaFeedbackService(Mockery::mock(NotificationService::class));
    }

    private function createSchema(): void
    {
        Schema::create('ciclos', function (Blueprint $table): void {
            $table->increments('id');
            $table->unsignedTinyInteger('tipo');
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
    }
}
