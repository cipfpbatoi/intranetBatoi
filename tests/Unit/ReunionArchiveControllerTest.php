<?php

declare(strict_types=1);

namespace Tests\Unit;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Intranet\Application\Reunion\ReunionContinuityService;
use Intranet\Entities\Profesor;
use Intranet\Http\Controllers\ReunionController;
use Mockery;
use RuntimeException;
use Tests\TestCase;

/**
 * Proves de blindatge del flux d'arxivament d'actes.
 */
class ReunionArchiveControllerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('database.default', 'sqlite');
        config()->set('database.connections.sqlite.database', ':memory:');
        config()->set('auth.defaults.guard', 'profesor');
        DB::purge('sqlite');
        DB::reconnect('sqlite');

        $schema = Schema::connection('sqlite');
        $schema->dropIfExists('ordenes_reuniones');
        $schema->dropIfExists('reuniones');
        $schema->dropIfExists('profesores');

        $schema->create('profesores', function (Blueprint $table): void {
            $table->string('dni', 10)->primary();
            $table->unsignedInteger('rol')->default(3);
        });

        $schema->create('reuniones', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('idProfesor', 10);
            $table->string('idGrupo', 10)->nullable();
            $table->unsignedTinyInteger('tipo')->default(2);
            $table->boolean('archivada')->default(false);
            $table->string('fichero')->nullable();
            $table->timestamps();
        });

        $schema->create('ordenes_reuniones', function (Blueprint $table): void {
            $table->increments('id');
            $table->unsignedInteger('idReunion');
            $table->unsignedTinyInteger('orden')->default(1);
            $table->string('descripcion')->nullable();
            $table->text('resumen')->nullable();
        });

        DB::table('profesores')->insert([
            ['dni' => 'P1', 'rol' => config('roles.rol.profesor')],
            ['dni' => 'P2', 'rol' => config('roles.rol.profesor')],
        ]);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_no_permet_arxivar_acta_d_un_altre_professor(): void
    {
        $this->insertAct(1, 'P1', false);
        $this->actingAs(Profesor::query()->findOrFail('P2'), 'profesor');

        $this->expectException(AuthorizationException::class);

        (new ReunionController())->saveFile(1);
    }

    public function test_no_modifica_una_acta_ja_arxivada(): void
    {
        $this->insertAct(2, 'P1', true);
        $this->insertOrder(2, null);
        $this->actingAs(Profesor::query()->findOrFail('P1'), 'profesor');

        (new ReunionController())->saveFile(2);

        $this->assertDatabaseHas('reuniones', ['id' => 2, 'archivada' => 1, 'fichero' => null]);
        $this->assertDatabaseHas('ordenes_reuniones', ['idReunion' => 2, 'resumen' => null]);
    }

    public function test_revertix_la_normalitzacio_si_falla_l_arxivament(): void
    {
        $this->insertAct(3, 'P1', false);
        $this->insertOrder(3, null);
        $this->actingAs(Profesor::query()->findOrFail('P1'), 'profesor');

        $continuity = Mockery::mock(ReunionContinuityService::class);
        $continuity->shouldReceive('normaliseEmptySummaries')
            ->once()
            ->andReturnUsing(function (): void {
                DB::table('ordenes_reuniones')->where('idReunion', 3)->update(['resumen' => 'No procedeix']);
                throw new RuntimeException('Error simulat de PDF');
            });

        (new ReunionController(null, null, null, $continuity))->saveFile(3);

        $this->assertDatabaseHas('reuniones', ['id' => 3, 'archivada' => 0, 'fichero' => null]);
        $this->assertDatabaseHas('ordenes_reuniones', ['idReunion' => 3, 'resumen' => null]);
    }

    private function insertAct(int $id, string $teacher, bool $archived): void
    {
        DB::table('reuniones')->insert([
            'id' => $id,
            'idProfesor' => $teacher,
            'idGrupo' => null,
            'tipo' => 2,
            'archivada' => $archived,
            'fichero' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function insertOrder(int $act, ?string $summary): void
    {
        DB::table('ordenes_reuniones')->insert([
            'idReunion' => $act,
            'orden' => 1,
            'descripcion' => 'Observacions',
            'resumen' => $summary,
        ]);
    }
}
