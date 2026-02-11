<?php

declare(strict_types=1);

namespace Tests\Unit;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Session;
use Intranet\Http\Traits\Core\Panel;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class PanelTraitTest extends TestCase
{
    private string $sqlitePath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sqlitePath = storage_path('panel_trait_testing.sqlite');
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
        Schema::connection('sqlite')->dropIfExists('panel_trait_items');

        if (file_exists($this->sqlitePath)) {
            @unlink($this->sqlitePath);
        }

        parent::tearDown();
    }

    public function test_index_configura_tabs_i_redirect_quan_hi_ha_estats(): void
    {
        config(['modelos.TestPanel.estados' => [1 => 'Pendent', 2 => 'Aprovat']]);
        PanelTraitItem::insert([
            ['estado' => 1, 'desde' => '2026-01-01 00:00:00'],
            ['estado' => 2, 'desde' => '2026-01-02 00:00:00'],
        ]);

        $this->startSession();
        Session::put('pestana', 2);

        $panel = new FakePanel();
        $controller = new DummyPanelController($panel);

        $result = $controller->index();

        $this->assertSame('grid-rendered', $result);
        $this->assertTrue($controller->iniBotonesCalled);
        $this->assertSame('PanelTestPanelController@index', Session::get('redirect'));
        $this->assertCount(2, $panel->tabs);
    }

    public function test_index_no_falla_quan_no_hi_ha_estats_en_config(): void
    {
        config(['modelos.TestPanel.estados' => null]);
        PanelTraitItem::insert([
            ['estado' => 1, 'desde' => '2026-01-01 00:00:00'],
        ]);

        $panel = new FakePanel();
        $controller = new DummyPanelController($panel);

        $result = $controller->index();

        $this->assertSame('grid-rendered', $result);
        $this->assertCount(0, $panel->tabs);
    }

    public function test_search_usa_camp_orden_quan_esta_definit(): void
    {
        PanelTraitItem::insert([
            ['estado' => 1, 'desde' => '2026-01-01 00:00:00'],
            ['estado' => 3, 'desde' => '2026-01-02 00:00:00'],
            ['estado' => 2, 'desde' => '2026-01-03 00:00:00'],
        ]);

        $controller = new DummyPanelController(new FakePanel());
        $controller->orden = 'estado';

        $result = $this->callProtectedMethod($controller, 'search');

        $this->assertSame([3, 2, 1], $result->pluck('estado')->all());
    }

    public function test_setauthbotonera_afegeix_només_collectius_d_estats_disponibles(): void
    {
        PanelTraitItem::insert([
            ['estado' => 2, 'desde' => '2026-01-01 00:00:00'],
        ]);

        $panel = new FakePanel();
        $controller = new DummyPanelController($panel);

        $this->callProtectedMethod($controller, 'setAuthBotonera');

        $this->assertCount(1, $panel->buttons['index'] ?? []);
        $this->assertCount(3, $panel->buttons['profile'] ?? []);
    }

    public function test_index_fa_abort_si_falta_class_o_model(): void
    {
        $this->expectException(HttpException::class);
        $this->expectExceptionMessage("El model i la classe han d'estar definits");

        $controller = new DummyPanelWithoutClassController(new FakePanel());
        $controller->index();
    }

    private function createSchema(): void
    {
        if (Schema::connection('sqlite')->hasTable('panel_trait_items')) {
            return;
        }

        Schema::connection('sqlite')->create('panel_trait_items', function (Blueprint $table) {
            $table->id();
            $table->integer('estado');
            $table->dateTime('desde')->nullable();
        });
    }
}

class PanelTraitItem extends Model
{
    protected $table = 'panel_trait_items';
    public $timestamps = false;
    protected $guarded = [];
}

class FakePanel
{
    public array $tabs = [];
    public array $buttons = [];

    public function setPestana($label, $active, $view, $where, $unused = null, $replace = null, $params = []): void
    {
        $this->tabs[] = compact('label', 'active', 'view', 'where', 'replace', 'params');
    }

    public function setBoton(string $scope, $button): void
    {
        $this->buttons[$scope][] = $button;
    }
}

class DummyPanelController
{
    use Panel;

    public string $model = 'TestPanel';
    public string $class = PanelTraitItem::class;
    public ?string $orden = null;
    public FakePanel $panel;
    public array $parametresVista = [];
    public bool $iniBotonesCalled = false;

    public function __construct(FakePanel $panel)
    {
        $this->panel = $panel;
    }

    protected function iniBotones(): void
    {
        $this->iniBotonesCalled = true;
    }

    protected function grid($todos)
    {
        return 'grid-rendered';
    }
}

class DummyPanelWithoutClassController
{
    use Panel;

    public string $model = 'TestPanel';
    public FakePanel $panel;
    public array $parametresVista = [];

    public function __construct(FakePanel $panel)
    {
        $this->panel = $panel;
    }

    protected function iniBotones(): void
    {
    }

    protected function grid($todos)
    {
        return 'grid-rendered';
    }
}
