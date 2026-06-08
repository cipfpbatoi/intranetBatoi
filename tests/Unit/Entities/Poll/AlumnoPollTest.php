<?php

declare(strict_types=1);

namespace Tests\Unit\Entities\Poll;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Intranet\Entities\Alumno as AlumnoEntity;
use Intranet\Entities\Poll\Alumno as AlumnoPoll;
use Tests\TestCase;

/**
 * Proves unitàries del tipus d'enquesta genèrica per a alumnat.
 */
class AlumnoPollTest extends TestCase
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

    /**
     * Verifica que un alumne amb grup obté un únic formulari pendent.
     */
    public function test_load_poll_retornara_context_quan_l_alumne_te_grup(): void
    {
        $alumno = $this->createAlumnoWithGroup('A1000001', '1DAM', 1, 10, 'DAM');
        $this->actingAs($alumno, 'alumno');

        $quests = AlumnoPoll::loadPoll([]);

        $this->assertNotNull($quests);
        $this->assertCount(1, $quests);
        $this->assertSame($this->studentVoteKey('A1000001'), $quests->first()['option1']->id);
        $this->assertSame('1DAM', $quests->first()['option1']->grupo);
    }

    /**
     * Verifica que el formulari no torna a aparéixer si l'alumne ja l'ha respost.
     */
    public function test_load_poll_retornara_null_si_ja_hi_ha_vots_previs(): void
    {
        $alumno = $this->createAlumnoWithGroup('A1000002', '1ASI', 2, 20, 'ASI');
        $this->actingAs($alumno, 'alumno');

        $this->assertNull(AlumnoPoll::loadPoll([123 => 123]));
    }

    /**
     * Verifica que només es recuperen els vots del mateix alumne autenticat.
     */
    public function test_load_votes_filtra_per_nia_hashat_de_l_alumne(): void
    {
        $alumno = $this->createAlumnoWithGroup('A1000003', '2DAW', 3, 30, 'DAW');
        $this->createAlumnoWithGroup('A1000004', '2SMX', 4, 40, 'SMX');
        $this->actingAs($alumno, 'alumno');

        DB::table('votes')->insert([
            [
                'idPoll' => 1,
                'user_id' => 'A1000003',
                'option_id' => 1,
                'idOption1' => $this->studentVoteKey('A1000003'),
                'idOption2' => null,
                'value' => 4,
                'text' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'idPoll' => 1,
                'user_id' => 'A1000003',
                'option_id' => 2,
                'idOption1' => $this->studentVoteKey('A1000003'),
                'idOption2' => null,
                'value' => null,
                'text' => 'Comentari propi',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'idPoll' => 1,
                'user_id' => 'A1000004',
                'option_id' => 1,
                'idOption1' => $this->studentVoteKey('A1000004'),
                'idOption2' => null,
                'value' => 1,
                'text' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $votes = AlumnoPoll::loadVotes(1);

        $this->assertSame([
            $this->studentVoteKey('A1000003') => [
                1 => 4,
                2 => 'Comentari propi',
            ],
        ], $votes);
    }

    /**
     * Verifica l'agregació per grup, cicle i departament.
     */
    public function test_aggregate_reparteix_vots_per_estructures_academiques(): void
    {
        $this->createAlumnoWithGroup('A1000005', '1G1', 5, 50, 'Cicle 1');
        $this->createAlumnoWithGroup('A1000006', '2G2', 6, 60, 'Cicle 2');

        $votes = [
            'grup' => [
                '1G1' => [1 => collect()],
                '2G2' => [1 => collect()],
            ],
            'cicle' => [
                5 => [1 => collect()],
                6 => [1 => collect()],
            ],
            'departament' => [
                50 => [1 => collect()],
                60 => [1 => collect()],
            ],
        ];

        $option1 = [
            $this->studentVoteKey('A1000005') => [
                1 => collect([(object) ['value' => 5]]),
            ],
            $this->studentVoteKey('A1000006') => [
                1 => collect([(object) ['value' => 3]]),
            ],
        ];

        AlumnoPoll::aggregate($votes, $option1, []);

        $this->assertCount(1, $votes['grup']['1G1'][1]);
        $this->assertCount(1, $votes['grup']['2G2'][1]);
        $this->assertSame(5, $votes['grup']['1G1'][1]->first()->value);
        $this->assertSame(3, $votes['departament'][60][1]->first()->value);
    }

    /**
     * Crea l'esquema mínim necessari per a provar el model.
     */
    private function createSchema(): void
    {
        $schema = Schema::connection('sqlite');

        $schema->create('alumnos', function (Blueprint $table): void {
            $table->string('nia')->primary();
            $table->string('dni')->nullable();
            $table->string('password')->nullable();
            $table->timestamps();
        });

        $schema->create('ciclos', function (Blueprint $table): void {
            $table->unsignedInteger('id')->primary();
            $table->unsignedInteger('departamento')->nullable();
            $table->string('ciclo')->nullable();
            $table->timestamps();
        });

        $schema->create('grupos', function (Blueprint $table): void {
            $table->string('codigo')->primary();
            $table->unsignedInteger('idCiclo')->nullable();
            $table->unsignedInteger('curso')->nullable();
            $table->timestamps();
        });

        $schema->create('alumnos_grupos', function (Blueprint $table): void {
            $table->string('idAlumno');
            $table->string('idGrupo');
        });

        $schema->create('votes', function (Blueprint $table): void {
            $table->increments('id');
            $table->unsignedInteger('idPoll');
            $table->string('user_id');
            $table->unsignedInteger('option_id');
            $table->unsignedInteger('idOption1')->nullable();
            $table->string('idOption2')->nullable();
            $table->integer('value')->nullable();
            $table->text('text')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Crea un alumne amb grup i cicle associats per als tests.
     */
    private function createAlumnoWithGroup(
        string $nia,
        string $grupoCode,
        int $cicloId,
        int $departamentoId,
        string $cicloName
    ): AlumnoEntity {
        DB::table('ciclos')->updateOrInsert(
            ['id' => $cicloId],
            ['departamento' => $departamentoId, 'ciclo' => $cicloName, 'created_at' => now(), 'updated_at' => now()]
        );

        DB::table('grupos')->updateOrInsert(
            ['codigo' => $grupoCode],
            ['idCiclo' => $cicloId, 'curso' => 1, 'created_at' => now(), 'updated_at' => now()]
        );

        DB::table('alumnos')->updateOrInsert(
            ['nia' => $nia],
            ['dni' => $nia . 'D', 'password' => 'secret', 'created_at' => now(), 'updated_at' => now()]
        );

        DB::table('alumnos_grupos')->insert([
            'idAlumno' => $nia,
            'idGrupo' => $grupoCode,
        ]);

        /** @var AlumnoEntity $alumno */
        $alumno = AlumnoEntity::query()->with(['Grupo.Ciclo'])->findOrFail($nia);

        return $alumno;
    }

    /**
     * Replica la clau interna que usa el model per a `idOption1`.
     */
    private function studentVoteKey(string $nia): int
    {
        return (int) sprintf('%u', crc32($nia));
    }
}
