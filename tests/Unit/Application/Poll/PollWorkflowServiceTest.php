<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Poll;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Intranet\Application\Grupo\GrupoService;
use Intranet\Application\Poll\PollWorkflowService;
use Intranet\Entities\Grupo;
use Tests\TestCase;

/**
 * Proves unitàries del workflow d'enquestes.
 */
class PollWorkflowServiceTest extends TestCase
{
    use WithoutModelEvents;

    private const TEST_MODEL = 'Intranet\\Entities\\Poll\\WorkflowTestModel';

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('database.default', 'sqlite');
        config()->set('database.connections.sqlite.database', ':memory:');
        DB::purge('sqlite');
        DB::reconnect('sqlite');

        $this->createSchema();
        $this->ensureWorkflowTestModel();
        $this->resetWorkflowTestModel();
    }

    public function test_prepare_survey_retorn_null_si_poll_no_existix(): void
    {
        $service = new PollWorkflowService();

        $result = $service->prepareSurvey(999, (object) ['id' => 'USR1', 'dni' => 'DNI1']);

        $this->assertNull($result);
    }

    public function test_prepare_survey_passa_vots_previs_anonims_al_model(): void
    {
        $ids = $this->seedPollBase(anonymous: 1);

        DB::table('votes')->insert([
            'idPoll' => $ids['poll'],
            'user_id' => md5('DNI01'),
            'option_id' => 1,
            'idOption1' => 9,
            'idOption2' => null,
            'value' => 4,
            'text' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $model = self::TEST_MODEL;
        $model::$loadPollResponse = [['question' => 'ok']];

        $service = new PollWorkflowService();
        $result = $service->prepareSurvey($ids['poll'], (object) ['id' => 'USR1', 'dni' => 'DNI01']);

        $this->assertNotNull($result);
        $this->assertSame($ids['poll'], $result['poll']->id);
        $this->assertSame([['question' => 'ok']], $result['quests']);
        $this->assertSame([9 => 9], $model::$lastVotesInput);
    }

    public function test_prepare_survey_filtra_opcions_per_cicle_i_mante_les_comunes(): void
    {
        $ids = $this->seedPollBase(anonymous: 0);

        DB::table('options')->insert([
            ['id' => 4, 'question' => 'Q4', 'scala' => 0, 'choices' => "DAM1\nDAM2", 'idCiclo' => 8, 'ppoll_id' => $ids['ppoll']],
            ['id' => 5, 'question' => 'Q5', 'scala' => 0, 'choices' => "ASIX1\nASIX2", 'idCiclo' => 9, 'ppoll_id' => $ids['ppoll']],
        ]);

        $service = new PollWorkflowService();
        $result = $service->prepareSurvey(
            $ids['poll'],
            (object) [
                'id' => 'AL1',
                'dni' => 'DNI01',
                'nia' => 'NIA01',
                'Grupo' => collect([(object) ['idCiclo' => 8]]),
            ]
        );

        $this->assertNotNull($result);
        $this->assertSame([1, 2, 3, 4], $result['options']->pluck('id')->all());
    }

    public function test_save_survey_guarda_vot_numeric_i_text(): void
    {
        $ids = $this->seedPollBase(anonymous: 0);

        $request = Request::create('/poll', 'POST', [
            'option1_7' => '5',
            'option2_7' => 'Molt bé',
            'option3_7' => 'Optativa2',
        ]);

        $service = new PollWorkflowService();
        $saved = $service->saveSurvey($request, $ids['poll'], (object) ['id' => 'PROF1', 'dni' => 'DNI01']);

        $this->assertTrue($saved);

        $numericVote = DB::table('votes')
            ->where('idPoll', $ids['poll'])
            ->where('option_id', 1)
            ->first();

        $textVote = DB::table('votes')
            ->where('idPoll', $ids['poll'])
            ->where('option_id', 2)
            ->first();
        $selectVote = DB::table('votes')
            ->where('idPoll', $ids['poll'])
            ->where('option_id', 3)
            ->first();

        $this->assertNotNull($numericVote);
        $this->assertNotNull($textVote);
        $this->assertNotNull($selectVote);
        $this->assertSame('PROF1', $numericVote->user_id);
        $this->assertSame(5, (int) $numericVote->value);
        $this->assertSame('Molt bé', $textVote->text);
        $this->assertSame('Optativa2', $selectVote->text);
    }

    public function test_save_survey_ignora_valor_de_seleccio_no_permes(): void
    {
        $ids = $this->seedPollBase(anonymous: 0);

        $request = Request::create('/poll', 'POST', [
            'option3_7' => 'Opcio inventada',
        ]);

        $service = new PollWorkflowService();
        $saved = $service->saveSurvey($request, $ids['poll'], (object) ['id' => 'PROF1', 'dni' => 'DNI01']);

        $this->assertTrue($saved);
        $this->assertNull(
            DB::table('votes')
                ->where('idPoll', $ids['poll'])
                ->where('option_id', 3)
                ->first()
        );
    }

    public function test_save_survey_reindexa_les_preguntes_filtrades_per_cicle(): void
    {
        $ids = $this->seedPollBase(anonymous: 0);

        DB::table('options')->where('id', 2)->update(['idCiclo' => 9]);
        DB::table('options')->where('id', 3)->update(['idCiclo' => 9]);
        DB::table('options')->insert([
            'id' => 4,
            'question' => 'Q4',
            'scala' => 0,
            'choices' => "Optativa DAM 1\nOptativa DAM 2",
            'idCiclo' => 8,
            'ppoll_id' => $ids['ppoll'],
        ]);

        $request = Request::create('/poll', 'POST', [
            'option1_7' => '5',
            'option2_7' => 'Optativa DAM 2',
        ]);

        $service = new PollWorkflowService();
        $saved = $service->saveSurvey(
            $request,
            $ids['poll'],
            (object) [
                'id' => 'AL1',
                'dni' => 'DNI01',
                'nia' => 'NIA01',
                'Grupo' => collect([(object) ['idCiclo' => 8]]),
            ]
        );

        $this->assertTrue($saved);
        $this->assertNotNull(
            DB::table('votes')
                ->where('idPoll', $ids['poll'])
                ->where('option_id', 1)
                ->first()
        );
        $this->assertNotNull(
            DB::table('votes')
                ->where('idPoll', $ids['poll'])
                ->where('option_id', 4)
                ->where('text', 'Optativa DAM 2')
                ->first()
        );
        $this->assertNull(
            DB::table('votes')
                ->where('idPoll', $ids['poll'])
                ->whereIn('option_id', [2, 3])
                ->first()
        );
    }

    public function test_my_votes_retorna_blocs_i_filtra_opcions_numeric_text(): void
    {
        $ids = $this->seedPollBase(anonymous: 0);

        $model = self::TEST_MODEL;
        $model::$loadVotesResponse = ['k' => 'v'];
        $model::$loadGroupVotesResponse = ['G1' => ['x']];

        $service = new PollWorkflowService();
        $result = $service->myVotes($ids['poll']);

        $this->assertNotNull($result);
        $this->assertSame(['k' => 'v'], $result['myVotes']);
        $this->assertSame(['G1' => ['x']], $result['myGroupsVotes']);
        $this->assertCount(1, $result['options_numeric']);
        $this->assertCount(1, $result['options_text']);
        $this->assertCount(1, $result['options_select']);
        $this->assertCount(3, $result['options']);
    }

    public function test_all_votes_inclou_detall_de_seleccio_per_grup_i_alumne(): void
    {
        DB::table('departamentos')->insert([
            'id' => 1,
            'cliteral' => 'Informàtica',
            'vliteral' => 'Informàtica',
        ]);

        DB::table('ciclos')->insert([
            'id' => 8,
            'ciclo' => 'DAM',
            'departamento' => 1,
        ]);

        DB::table('grupos')->insert([
            'codigo' => '1DAM',
            'nombre' => '1DAM',
            'idCiclo' => 8,
            'curso' => 1,
        ]);

        DB::table('alumnos')->insert([
            [
                'nia' => 'NIA01',
                'dni' => 'DNI01',
                'nombre' => 'Ada',
                'apellido1' => 'Lovelace',
                'apellido2' => 'Test',
                'email' => 'ada@test.local',
            ],
            [
                'nia' => 'NIA02',
                'dni' => 'DNI02',
                'nombre' => 'Grace',
                'apellido1' => 'Hopper',
                'apellido2' => 'Test',
                'email' => 'grace@test.local',
            ],
        ]);

        DB::table('alumnos_grupos')->insert([
            ['idAlumno' => 'NIA01', 'idGrupo' => '1DAM'],
            ['idAlumno' => 'NIA02', 'idGrupo' => '1DAM'],
        ]);

        $idPPoll = (int) DB::table('ppolls')->insertGetId([
            'title' => 'Plantilla optatives',
            'what' => 'Alumno',
            'anonymous' => 0,
            'remains' => 0,
        ]);

        $idPoll = (int) DB::table('polls')->insertGetId([
            'title' => 'Poll optatives',
            'desde' => '2026-03-01',
            'hasta' => '2026-03-31',
            'idPPoll' => $idPPoll,
        ]);

        DB::table('options')->insert([
            'id' => 30,
            'question' => 'Quina optativa vols?',
            'scala' => 0,
            'choices' => "Optativa A\nOptativa B",
            'idCiclo' => 8,
            'ppoll_id' => $idPPoll,
        ]);

        DB::table('votes')->insert([
            'idPoll' => $idPoll,
            'user_id' => 'NIA01',
            'option_id' => 30,
            'idOption1' => $this->studentVoteKey('NIA01'),
            'idOption2' => null,
            'value' => null,
            'text' => 'Optativa B',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $grupo = new Grupo();
        $grupo->codigo = '1DAM';
        $grupoService = $this->createMock(GrupoService::class);
        $grupoService
            ->method('all')
            ->willReturn(new EloquentCollection([$grupo]));

        $service = new PollWorkflowService();
        $result = $service->allVotes($idPoll, $grupoService);

        $this->assertNotNull($result);
        $this->assertArrayHasKey('1DAM', $result['student_select_groups']);

        $sheet = $result['student_select_groups']['1DAM'];
        $this->assertSame('1DAM', $sheet['group']->codigo);
        $this->assertCount(1, $sheet['options']);
        $this->assertCount(2, $sheet['rows']);
        $this->assertSame('Ada Lovelace Test', $sheet['rows'][0]['student_name']);
        $this->assertSame('Optativa B', $sheet['rows'][0]['choices'][30]);
        $this->assertSame('Grace Hopper Test', $sheet['rows'][1]['student_name']);
        $this->assertSame([], $sheet['rows'][1]['choices']);
    }

    public function test_all_votes_no_inclou_detall_per_alumne_si_la_poll_es_anonima(): void
    {
        DB::table('departamentos')->insert([
            'id' => 1,
            'cliteral' => 'Informàtica',
            'vliteral' => 'Informàtica',
        ]);

        DB::table('ciclos')->insert([
            'id' => 8,
            'ciclo' => 'DAM',
            'departamento' => 1,
        ]);

        DB::table('grupos')->insert([
            'codigo' => '1DAM',
            'nombre' => '1DAM',
            'idCiclo' => 8,
            'curso' => 1,
        ]);

        DB::table('alumnos')->insert([
            'nia' => 'NIA01',
            'dni' => 'DNI01',
            'nombre' => 'Ada',
            'apellido1' => 'Lovelace',
            'apellido2' => 'Test',
            'email' => 'ada@test.local',
        ]);

        DB::table('alumnos_grupos')->insert([
            'idAlumno' => 'NIA01',
            'idGrupo' => '1DAM',
        ]);

        $idPPoll = (int) DB::table('ppolls')->insertGetId([
            'title' => 'Plantilla optatives anonima',
            'what' => 'Alumno',
            'anonymous' => 1,
            'remains' => 0,
        ]);

        $idPoll = (int) DB::table('polls')->insertGetId([
            'title' => 'Poll optatives anonima',
            'desde' => '2026-03-01',
            'hasta' => '2026-03-31',
            'idPPoll' => $idPPoll,
        ]);

        DB::table('options')->insert([
            'id' => 31,
            'question' => 'Quina optativa vols?',
            'scala' => 0,
            'choices' => "Optativa A\nOptativa B",
            'idCiclo' => 8,
            'ppoll_id' => $idPPoll,
        ]);

        DB::table('votes')->insert([
            'idPoll' => $idPoll,
            'user_id' => md5('NIA01'),
            'option_id' => 31,
            'idOption1' => $this->studentVoteKey('NIA01'),
            'idOption2' => null,
            'value' => null,
            'text' => 'Optativa B',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $grupo = new Grupo();
        $grupo->codigo = '1DAM';
        $grupoService = $this->createMock(GrupoService::class);
        $grupoService
            ->method('all')
            ->willReturn(new EloquentCollection([$grupo]));

        $service = new PollWorkflowService();
        $result = $service->allVotes($idPoll, $grupoService);

        $this->assertNotNull($result);
        $this->assertSame([], $result['student_select_groups']);
    }

    public function test_prepare_survey_permet_reobrir_optatives_activa_encara_que_hi_haja_vots_previs(): void
    {
        $idPPoll = (int) DB::table('ppolls')->insertGetId([
            'title' => 'Plantilla test',
            'what' => 'WorkflowTestModel',
            'anonymous' => $anonymous,
            'remains' => 0,
        ]);

        $idPoll = (int) DB::table('polls')->insertGetId([
            'title' => 'Poll test',
            'desde' => '2026-02-01',
            'hasta' => '2026-02-28',
            'idPPoll' => $idPPoll,
        ]);

        DB::table('options')->insert([
            ['id' => 1, 'question' => 'Q1', 'scala' => 10, 'choices' => null, 'idCiclo' => null, 'ppoll_id' => $idPPoll],
            ['id' => 2, 'question' => 'Q2', 'scala' => 0, 'choices' => null, 'idCiclo' => null, 'ppoll_id' => $idPPoll],
            ['id' => 3, 'question' => 'Q3', 'scala' => 0, 'choices' => "Optativa1\nOptativa2\nOptativa3", 'idCiclo' => null, 'ppoll_id' => $idPPoll],
        ]);

        return ['ppoll' => $idPPoll, 'poll' => $idPoll];
    }

    private function createSchema(): void
    {
        $schema = Schema::connection('sqlite');

        $schema->create('ppolls', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('title')->nullable();
            $table->string('what');
            $table->unsignedTinyInteger('anonymous')->default(0);
            $table->unsignedTinyInteger('remains')->default(0);
        });

        $schema->create('polls', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('title')->nullable();
            $table->date('desde')->nullable();
            $table->date('hasta')->nullable();
            $table->unsignedInteger('idPPoll');
        });

        $schema->create('options', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('question')->nullable();
            $table->integer('scala')->default(0);
            $table->text('choices')->nullable();
            $table->unsignedInteger('idCiclo')->nullable();
            $table->unsignedInteger('ppoll_id');
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

        $schema->create('departamentos', function (Blueprint $table): void {
            $table->unsignedInteger('id')->primary();
            $table->string('cliteral')->nullable();
            $table->string('vliteral')->nullable();
        });

        $schema->create('ciclos', function (Blueprint $table): void {
            $table->unsignedInteger('id')->primary();
            $table->string('ciclo')->nullable();
            $table->unsignedInteger('departamento')->nullable();
        });

        $schema->create('grupos', function (Blueprint $table): void {
            $table->string('codigo')->primary();
            $table->string('nombre')->nullable();
            $table->unsignedInteger('idCiclo')->nullable();
            $table->unsignedTinyInteger('curso')->default(1);
        });

        $schema->create('alumnos', function (Blueprint $table): void {
            $table->string('nia')->primary();
            $table->string('dni')->nullable();
            $table->string('nombre')->nullable();
            $table->string('apellido1')->nullable();
            $table->string('apellido2')->nullable();
            $table->string('email')->nullable();
        });

        $schema->create('alumnos_grupos', function (Blueprint $table): void {
            $table->string('idAlumno');
            $table->string('idGrupo');
        });
    }

    private function ensureWorkflowTestModel(): void
    {
        if (class_exists(self::TEST_MODEL)) {
            return;
        }

        eval(<<<'PHP_EVAL'
namespace Intranet\Entities\Poll;

class WorkflowTestModel extends ModelPoll
{
    public static array $lastVotesInput = [];
    public static ?array $loadPollResponse = null;
    public static mixed $loadVotesResponse = null;
    public static mixed $loadGroupVotesResponse = null;

    public static function keyInterviewed()
    {
        return 'dni';
    }

    public static function loadPoll($votes)
    {
        self::$lastVotesInput = is_array($votes) ? $votes : [];

        if (self::$loadPollResponse !== null) {
            return self::$loadPollResponse;
        }

        return [
            ['option1' => (object) ['id' => 7]],
        ];
    }

    public static function loadVotes($id)
    {
        return self::$loadVotesResponse;
    }

    public static function loadGroupVotes($id)
    {
        return self::$loadGroupVotesResponse;
    }
}
PHP_EVAL);
    }

    private function resetWorkflowTestModel(): void
    {
        $model = self::TEST_MODEL;
        $model::$lastVotesInput = [];
        $model::$loadPollResponse = null;
        $model::$loadVotesResponse = null;
        $model::$loadGroupVotesResponse = null;
    }

    private function studentVoteKey(string $nia): int
    {
        return (int) sprintf('%u', crc32($nia));
    }
}
