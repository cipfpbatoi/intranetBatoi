<?php

declare(strict_types=1);

namespace Tests\Unit;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Intranet\Http\Controllers\Core\IntranetController;
use Tests\TestCase;

class IntranetControllerTest extends TestCase
{
    private string $sqlitePath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sqlitePath = storage_path('intranet_controller_testing.sqlite');
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
        Schema::connection('sqlite')->dropIfExists('dummy_intranet_items');

        if (file_exists($this->sqlitePath)) {
            @unlink($this->sqlitePath);
        }

        parent::tearDown();
    }

    public function test_active_alterna_camp_activo(): void
    {
        $item = DummyIntranetItem::create(['name' => 'Item 1', 'activo' => true, 'flag' => false]);

        $controller = new DummyIntranetController();
        $controller->setModelAndClass('DummyIntranetItem', DummyIntranetItem::class);

        $controller->active($item->id);

        $this->assertFalse((bool) DummyIntranetItem::findOrFail($item->id)->activo);
    }

    public function test_managecheckbox_marca_false_si_no_arriba_al_request(): void
    {
        $controller = new DummyIntranetController();
        $request = Request::create('/dummy', 'POST', ['name' => 'Prova']);

        $result = $controller->exposeManageCheckBox($request, new DummyIntranetItem());

        $this->assertSame('Prova', $result->input('name'));
        $this->assertFalse((bool) $result->input('flag'));
    }

    public function test_managecheckbox_marca_true_quan_arriba_al_request(): void
    {
        $controller = new DummyIntranetController();
        $request = Request::create('/dummy', 'POST', ['name' => 'Prova', 'flag' => 'on']);

        $result = $controller->exposeManageCheckBox($request, new DummyIntranetItem());

        $this->assertTrue((bool) $result->input('flag'));
    }

    public function test_validateall_retorna_dades_validades(): void
    {
        $controller = new DummyIntranetController();
        $request = Request::create('/dummy', 'POST', ['name' => 'Valid']);

        $validated = $controller->exposeValidateAll($request, new DummyIntranetItem());

        $this->assertSame(['name' => 'Valid'], $validated);
    }

    public function test_borrarfichero_esborra_fitxer_de_public_i_storage(): void
    {
        $controller = new DummyIntranetController();

        $publicRelative = 'intranet-controller-public.txt';
        $storageRelative = 'intranet-controller-storage.txt';
        $publicPath = public_path($publicRelative);
        $storagePath = storage_path('app/' . $storageRelative);

        file_put_contents($publicPath, 'public');
        file_put_contents($storagePath, 'storage');

        $controller->exposeBorrarFichero($publicRelative);
        $controller->exposeBorrarFichero($storageRelative);

        $this->assertFileDoesNotExist($publicPath);
        $this->assertFileDoesNotExist($storagePath);
    }

    private function createSchema(): void
    {
        if (!Schema::connection('sqlite')->hasTable('dummy_intranet_items')) {
            Schema::connection('sqlite')->create('dummy_intranet_items', function (Blueprint $table) {
                $table->id();
                $table->string('name')->nullable();
                $table->boolean('activo')->default(true);
                $table->boolean('flag')->default(false);
            });
        }
    }
}

class DummyIntranetItem extends Model
{
    protected $table = 'dummy_intranet_items';
    public $timestamps = false;
    protected $guarded = [];
    protected $fillable = ['name', 'activo', 'flag'];

    public function getInputType(string $property): array
    {
        if ($property === 'flag') {
            return ['type' => 'checkbox'];
        }

        return ['type' => 'text'];
    }

    public function getRules(): array
    {
        return ['name' => 'required|string'];
    }
}

class DummyIntranetController extends IntranetController
{
    public function __construct()
    {
        // Evitem inicialització de panells/UI en proves unitàries.
    }

    public function setModelAndClass(string $model, string $class): void
    {
        $this->model = $model;
        $this->class = $class;
    }

    public function exposeManageCheckBox(Request $request, object $element): Request
    {
        return $this->manageCheckBox($request, $element);
    }

    public function exposeValidateAll(Request $request, object $element): array
    {
        return $this->validateAll($request, $element);
    }

    public function exposeBorrarFichero(?string $fichero): void
    {
        $this->borrarFichero($fichero);
    }

    protected function redirect()
    {
        return redirect('/ok');
    }

    protected function iniBotones()
    {
    }

    protected function iniPestanas($parametres = null)
    {
    }
}
