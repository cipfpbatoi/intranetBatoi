<?php

declare(strict_types=1);

namespace Intranet\Http\Controllers\Core {
    if (!function_exists(__NAMESPACE__ . '\\AuthUser')) {
        function AuthUser(): object
        {
            return (object) ['dni' => '111A'];
        }
    }
}

namespace Tests\Unit {

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Intranet\Http\Controllers\Core\ModalController;
use Tests\TestCase;

class ModalControllerTest extends TestCase
{
    private string $sqlitePath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sqlitePath = storage_path('modal_controller_testing.sqlite');
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
        Schema::connection('sqlite')->dropIfExists('modal_items');
        Schema::connection('sqlite')->dropIfExists('modal_items_prof');

        if (file_exists($this->sqlitePath)) {
            @unlink($this->sqlitePath);
        }

        parent::tearDown();
    }

    public function test_resolveindexview_fallback_i_configuracio(): void
    {
        $controller = new DummyModalController();

        $this->assertSame('intranet.indexModal', $controller->exposeResolveIndexView());

        $controller->setVista(['index' => 'custom.modal']);
        $this->assertSame('custom.modal', $controller->exposeResolveIndexView());

        $controller->setVista('alt.indexModal');
        $this->assertSame('alt.indexModal', $controller->exposeResolveIndexView());
    }

    public function test_search_retorna_tots_si_no_existix_idprofesor(): void
    {
        DummyModalItem::insert([
            ['name' => 'A'],
            ['name' => 'B'],
        ]);

        $controller = new DummyModalController();
        $controller->setModelAndClass('DummyModalItem', DummyModalItem::class);

        $result = $controller->exposeSearch();

        $this->assertCount(2, $result);
    }

    public function test_search_filtra_per_professor_si_existix_columna(): void
    {
        DummyModalItemProfesor::insert([
            ['name' => 'A', 'idProfesor' => '111A'],
            ['name' => 'B', 'idProfesor' => '222B'],
            ['name' => 'C', 'idProfesor' => '111A'],
        ]);

        $controller = new DummyModalController();
        $controller->setModelAndClass('DummyModalItemProfesor', DummyModalItemProfesor::class);

        $result = $controller->exposeSearch();

        $this->assertCount(2, $result);
        $this->assertSame(['A', 'C'], $result->pluck('name')->all());
    }

    public function test_createwithdefaultvalues_retorna_instancia_model(): void
    {
        $controller = new DummyModalController();
        $controller->setModelAndClass('DummyModalItem', DummyModalItem::class);

        $record = $controller->exposeCreateWithDefaultValues(['name' => 'Inicial']);

        $this->assertInstanceOf(DummyModalItem::class, $record);
        $this->assertSame('Inicial', $record->name);
    }

    public function test_destroy_esborra_i_executa_hooks_quan_correspon(): void
    {
        $item = DummyModalItemProfesor::create([
            'name' => 'Esborrar',
            'idProfesor' => '111A',
            'fichero' => 'tmp/file.pdf',
        ]);

        $controller = new DummyModalController();
        $controller->setModelAndClass('DummyModalItemProfesor', DummyModalItemProfesor::class);

        $controller->destroy($item->id);

        $this->assertNull(DummyModalItemProfesor::find($item->id));
        $this->assertSame('tmp/file.pdf', $controller->lastDeletedFile);
        $this->assertSame($item->id, $controller->lastDeletedAttachedId);
    }

    private function createSchema(): void
    {
        if (!Schema::connection('sqlite')->hasTable('modal_items')) {
            Schema::connection('sqlite')->create('modal_items', function (Blueprint $table) {
                $table->id();
                $table->string('name')->nullable();
            });
        }

        if (!Schema::connection('sqlite')->hasTable('modal_items_prof')) {
            Schema::connection('sqlite')->create('modal_items_prof', function (Blueprint $table) {
                $table->id();
                $table->string('name')->nullable();
                $table->string('idProfesor')->nullable();
                $table->string('fichero')->nullable();
            });
        }
    }
}

class DummyModalItem extends Model
{
    protected $table = 'modal_items';
    public $timestamps = false;
    protected $guarded = [];
}

class DummyModalItemProfesor extends Model
{
    protected $table = 'modal_items_prof';
    public $timestamps = false;
    protected $guarded = [];
}

class DummyModalController extends ModalController
{
    public ?string $lastDeletedFile = null;
    public ?int $lastDeletedAttachedId = null;

    public function __construct()
    {
        // Evitem dependències del panell per a proves de lògica interna.
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

    public function exposeResolveIndexView(): string
    {
        return $this->resolveIndexView();
    }

    public function exposeSearch()
    {
        return $this->search();
    }

    public function exposeCreateWithDefaultValues(array $default = [])
    {
        return $this->createWithDefaultValues($default);
    }

    protected function borrarFichero($fichero)
    {
        $this->lastDeletedFile = $fichero;
    }

    protected function deleteAttached($id)
    {
        $this->lastDeletedAttachedId = (int) $id;
    }

    protected function redirect()
    {
        return redirect('/ok');
    }
}

}

