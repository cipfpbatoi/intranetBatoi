<?php

declare(strict_types=1);

namespace Intranet\Http\Controllers {
    function AuthUser(): object
    {
        return (object) ['dni' => '111A'];
    }
}

namespace Tests\Unit {

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Intranet\Http\Controllers\BaseController;
use Tests\TestCase;

class BaseControllerTest extends TestCase
{
    private string $sqlitePath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sqlitePath = storage_path('base_controller_testing.sqlite');
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
        Schema::connection('sqlite')->dropIfExists('base_items');
        Schema::connection('sqlite')->dropIfExists('base_items_prof');

        if (file_exists($this->sqlitePath)) {
            @unlink($this->sqlitePath);
        }

        parent::tearDown();
    }

    public function test_chooseview_fallback_intranet_quan_no_hi_ha_vista(): void
    {
        $controller = new DummyBaseController();

        $view = $this->callProtectedMethod($controller, 'chooseView', ['show']);

        $this->assertSame('intranet.show', $view);
    }

    public function test_chooseview_array_amb_punt_torna_ruta_completa(): void
    {
        $controller = new DummyBaseController();
        $controller->setVista(['show' => 'custom.full.show']);

        $view = $this->callProtectedMethod($controller, 'chooseView', ['show']);

        $this->assertSame('custom.full.show', $view);
    }

    public function test_chooseview_array_sense_punt_concatena_accio(): void
    {
        $controller = new DummyBaseController();
        $controller->setVista(['edit' => 'perfil']);

        $view = $this->callProtectedMethod($controller, 'chooseView', ['edit']);

        $this->assertSame('perfil.edit', $view);
    }

    public function test_chooseview_string_amb_punt_torna_la_ruta(): void
    {
        $controller = new DummyBaseController();
        $controller->setVista('panel.custom');

        $view = $this->callProtectedMethod($controller, 'chooseView', ['index']);

        $this->assertSame('panel.custom', $view);
    }

    public function test_chooseview_string_sense_punt_concatena_accio(): void
    {
        $controller = new DummyBaseController();
        $controller->setVista('material');

        $view = $this->callProtectedMethod($controller, 'chooseView', ['index']);

        $this->assertSame('material.index', $view);
    }

    public function test_search_retornal_tots_quan_no_existix_idprofesor(): void
    {
        BaseItem::insert([
            ['name' => 'A'],
            ['name' => 'B'],
        ]);

        $controller = new DummyBaseController();
        $controller->setModelAndClass(BaseItem::class, BaseItem::class);

        $result = $this->callProtectedMethod($controller, 'search');

        $this->assertCount(2, $result);
    }

    public function test_search_filtra_per_professor_quan_existix_idprofesor(): void
    {
        BaseItemProfesor::insert([
            ['name' => 'A', 'idProfesor' => '111A'],
            ['name' => 'B', 'idProfesor' => '222B'],
            ['name' => 'C', 'idProfesor' => '111A'],
        ]);

        $controller = new DummyBaseController();
        $controller->setModelAndClass(BaseItemProfesor::class, BaseItemProfesor::class);

        $result = $this->callProtectedMethod($controller, 'search');

        $this->assertCount(2, $result);
        $this->assertSame(['A', 'C'], $result->pluck('name')->all());
    }

    private function createSchema(): void
    {
        if (!Schema::connection('sqlite')->hasTable('base_items')) {
            Schema::connection('sqlite')->create('base_items', function (Blueprint $table) {
                $table->id();
                $table->string('name')->nullable();
            });
        }

        if (!Schema::connection('sqlite')->hasTable('base_items_prof')) {
            Schema::connection('sqlite')->create('base_items_prof', function (Blueprint $table) {
                $table->id();
                $table->string('name')->nullable();
                $table->string('idProfesor')->nullable();
            });
        }
    }
}

class BaseItem extends Model
{
    protected $table = 'base_items';
    public $timestamps = false;
    protected $guarded = [];
}

class BaseItemProfesor extends Model
{
    protected $table = 'base_items_prof';
    public $timestamps = false;
    protected $guarded = [];
}

class DummyBaseController extends BaseController
{
    public function __construct()
    {
        // Evitem dependències de panell per a proves unitàries de mètodes interns.
    }

    protected function iniBotones()
    {
    }

    protected function iniPestanas($parametres = null)
    {
    }

    public function setVista(mixed $vista): void
    {
        $this->vista = $vista;
    }

    public function setModelAndClass(string $model, string $class): void
    {
        $this->model = $model;
        $this->class = $class;
    }
}

}
