<?php

declare(strict_types=1);

namespace Tests\Unit;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Intranet\Entities\Concerns\BatoiModels;
use Tests\TestCase;

/**
 * Proves unitàries del comportament essencial del trait `BatoiModels`.
 */
class BatoiModelsTraitTest extends TestCase
{
    private string $sqlitePath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sqlitePath = storage_path('batoi_models_trait_testing.sqlite');
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
    }

    protected function tearDown(): void
    {
        Schema::connection('sqlite')->dropIfExists('dummy_batoi_models');

        if (file_exists($this->sqlitePath)) {
            @unlink($this->sqlitePath);
        }

        parent::tearDown();
    }

    public function test_isrequired_detecta_required_al_principi_de_la_regla(): void
    {
        $model = new DummyBatoiModel();

        $this->assertTrue($model->isRequired('name'));
        $this->assertFalse($model->isRequired('description'));
    }

    public function test_istypedate_i_existsdatepicker_funcionen_amb_posicio_zero(): void
    {
        $model = new DummyBatoiModel();

        $this->assertTrue($model->isTypeDate(['type' => 'date']));
        $this->assertTrue($model->existsDatepicker());
    }

    public function test_fillall_no_sobreescriu_camps_no_presents_i_processa_checkbox(): void
    {
        $item = DummyBatoiModel::create([
            'name' => 'Original',
            'description' => 'Mantindre',
            'flag' => 1,
        ]);

        $request = Request::create('/dummy', 'POST', [
            'name' => 'Nou nom',
            // description no arriba: ha de mantindre's
            // flag no arriba: checkbox ha de passar a 0
        ]);

        $item->fillAll($request);
        $reloaded = DummyBatoiModel::findOrFail($item->id);

        $this->assertSame('Nou nom', $reloaded->name);
        $this->assertSame('Mantindre', $reloaded->description);
        $this->assertSame(0, (int) $reloaded->flag);
    }

    public function test_fillall_data_buida_en_date_i_datetime_no_pet(): void
    {
        $item = DummyBatoiModel::create([
            'name' => 'Dates',
            'description' => 'x',
            'flag' => 0,
        ]);

        $request = Request::create('/dummy', 'POST', [
            'name' => 'Dates 2',
            'started_at' => '',
            'deadline_at' => '',
        ]);

        $item->fillAll($request);
        $reloaded = DummyBatoiModel::findOrFail($item->id);

        $this->assertNull($reloaded->started_at);
        $this->assertNull($reloaded->deadline_at);
    }

    private function createSchema(): void
    {
        if (!Schema::connection('sqlite')->hasTable('dummy_batoi_models')) {
            Schema::connection('sqlite')->create('dummy_batoi_models', function (Blueprint $table) {
                $table->id();
                $table->string('name')->nullable();
                $table->string('description')->nullable();
                $table->boolean('flag')->default(false);
                $table->string('started_at')->nullable();
                $table->string('deadline_at')->nullable();
            });
        }
    }
}

/**
 * Model de prova mínim per exercitar `BatoiModels` sense dependències de domini.
 */
class DummyBatoiModel extends Model
{
    use BatoiModels;

    protected $table = 'dummy_batoi_models';
    public $timestamps = false;
    protected $fillable = ['name', 'description', 'flag', 'started_at', 'deadline_at'];
    protected $rules = [
        'name' => 'required|string',
        'description' => 'nullable|string',
    ];
    protected $inputTypes = [
        'name' => ['type' => 'text'],
        'description' => ['type' => 'text'],
        'flag' => ['type' => 'checkbox'],
        'started_at' => ['type' => 'date'],
        'deadline_at' => ['type' => 'datetime'],
    ];
}
