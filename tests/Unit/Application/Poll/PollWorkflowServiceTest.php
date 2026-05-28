<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Poll;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Intranet\Application\Poll\PollWorkflowService;
use Tests\TestCase;

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

    public function test_save_survey_guarda_vot_numeric_i_text(): void
    {
        $ids = $this->seedPollBase(anonymous: 0);

        $request = Request::create('/poll', 'POST', [
            'option1_7' => '5',
            'option2_7' => 'Molt bé',
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

        $this->assertNotNull($numericVote);
        $this->assertNotNull($textVote);
        $this->assertSame('PROF1', $numericVote->user_id);
        $this->assertSame(5, (int) $numericVote->value);
        $this->assertSame('Molt bé', $textVote->text);
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
        $this->assertCount(2, $result['options']);
    }

    private function seedPollBase(int $anonymous): array
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
            ['id' => 1, 'question' => 'Q1', 'scala' => 10, 'ppoll_id' => $idPPoll],
            ['id' => 2, 'question' => 'Q2', 'scala' => 0, 'ppoll_id' => $idPPoll],
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
}
