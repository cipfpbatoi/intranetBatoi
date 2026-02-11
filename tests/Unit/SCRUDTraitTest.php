<?php

declare(strict_types=1);

namespace Tests\Unit;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\Factory as ViewFactory;
use Intranet\Http\Traits\SCRUD;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class SCRUDTraitTest extends TestCase
{
    private string $sqlitePath;
    private string $viewsPath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sqlitePath = storage_path('scrud_trait_testing.sqlite');
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

        $this->viewsPath = storage_path('framework/testing/scrud_views');
        $this->prepareViews();
    }

    protected function tearDown(): void
    {
        Schema::connection('sqlite')->dropIfExists('scrud_trait_items');

        if (file_exists($this->sqlitePath)) {
            @unlink($this->sqlitePath);
        }

        $this->deleteDir($this->viewsPath);

        parent::tearDown();
    }

    public function test_modelclass_resol_nom_curt_i_assigna_class(): void
    {
        $controller = new DummySCRUDController();

        $resolved = $this->callProtectedMethod($controller, 'modelClass');

        $this->assertSame(SCRUDTraitItem::class, $resolved);
        $this->assertSame(SCRUDTraitItem::class, $controller->class);
    }

    public function test_modelclass_fa_abort_si_no_hi_ha_model(): void
    {
        $this->expectException(HttpException::class);
        $this->expectExceptionMessage('SCRUD misconfigured: $model not set');

        $controller = new DummySCRUDWithoutModelController();
        $this->callProtectedMethod($controller, 'modelClass');
    }

    public function test_chooseview_prioritza_vista_amb_punt_si_existix(): void
    {
        $controller = new DummySCRUDController();
        $controller->vista = ['show' => 'custom.full.show'];

        $view = $this->callProtectedMethod($controller, 'chooseView', ['show']);

        $this->assertSame('custom.full.show', $view);
    }

    public function test_chooseview_usa_prefix_mes_sufix_quan_existix(): void
    {
        $controller = new DummySCRUDController();
        $controller->vista = ['edit' => 'customprefix'];

        $view = $this->callProtectedMethod($controller, 'chooseView', ['edit']);

        $this->assertSame('customprefix.edit', $view);
    }

    public function test_show_torna_warning_quan_no_troba_registre(): void
    {
        $this->startSession();
        $controller = new DummySCRUDController();

        $response = $controller->show(9999);

        $this->assertTrue($response->isRedirect());
        $this->assertStringContainsString("No s'ha trobat", (string) $response->getSession()->get('warning'));
    }

    public function test_create_i_edit_tornen_les_vistes_esperades(): void
    {
        $item = SCRUDTraitItem::create(['name' => 'Prova']);
        $controller = new DummySCRUDController();

        $createView = $controller->create();
        $editView = $controller->edit($item->id);

        $this->assertSame('intranet.create', $createView->name());
        $this->assertSame('intranet.edit', $editView->name());
        $this->assertSame('SCRUDTraitItem', $createView->getData()['modelo']);
        $this->assertSame('SCRUDTraitItem', $editView->getData()['modelo']);
    }

    private function createSchema(): void
    {
        if (Schema::connection('sqlite')->hasTable('scrud_trait_items')) {
            return;
        }

        Schema::connection('sqlite')->create('scrud_trait_items', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
        });
    }

    private function prepareViews(): void
    {
        @mkdir($this->viewsPath . '/intranet', 0775, true);
        @mkdir($this->viewsPath . '/custom/full', 0775, true);
        @mkdir($this->viewsPath . '/customprefix', 0775, true);

        file_put_contents($this->viewsPath . '/intranet/show.blade.php', 'show');
        file_put_contents($this->viewsPath . '/intranet/create.blade.php', 'create');
        file_put_contents($this->viewsPath . '/intranet/edit.blade.php', 'edit');
        file_put_contents($this->viewsPath . '/custom/full/show.blade.php', 'custom-show');
        file_put_contents($this->viewsPath . '/customprefix/edit.blade.php', 'prefix-edit');

        config(['view.paths' => [$this->viewsPath]]);

        /** @var ViewFactory $view */
        $view = $this->app->make('view');
        $view->getFinder()->setPaths([$this->viewsPath]);
    }

    private function deleteDir(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }

        $items = scandir($dir);
        if ($items === false) {
            return;
        }

        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }

            $path = $dir . DIRECTORY_SEPARATOR . $item;
            if (is_dir($path)) {
                $this->deleteDir($path);
            } else {
                @unlink($path);
            }
        }

        @rmdir($dir);
    }
}

class SCRUDTraitItem extends Model
{
    protected $table = 'scrud_trait_items';
    public $timestamps = false;
    protected $guarded = [];

    public function isRequired($key): bool
    {
        return false;
    }
}

class DummySCRUDController
{
    use SCRUD;

    public string $namespace = 'Tests\\Unit\\';
    public string $model = 'SCRUDTraitItem';
    public ?string $class = null;
    public array $formFields = [];
    public array $vista = [];
}

class DummySCRUDWithoutModelController
{
    use SCRUD;

    public string $namespace = 'Tests\\Unit\\';
    public ?string $class = null;
    public array $formFields = [];
    public array $vista = [];
}
