<?php

declare(strict_types=1);

namespace Tests\Unit\Entities;

use Intranet\Application\Menu\MenuService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Intranet\Entities\Menu;
use Tests\TestCase;

class MenuTest extends TestCase
{
    private ?MenuService $menuService = null;

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('database.default', 'sqlite');
        config()->set('database.connections.sqlite.database', ':memory:');
        config()->set('cache.default', 'array');
        config()->set('roles.rol', [
            'todos' => 1,
            'profesor' => 2,
            'admin' => 11,
        ]);

        DB::purge('sqlite');
        DB::reconnect('sqlite');
        Cache::flush();

        $schema = Schema::connection('sqlite');
        $schema->dropIfExists('menus');
        $schema->create('menus', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('nombre');
            $table->string('url')->nullable();
            $table->string('class')->nullable();
            $table->unsignedInteger('rol')->default(1);
            $table->string('menu');
            $table->string('submenu')->default('');
            $table->boolean('activo')->default(true);
            $table->unsignedInteger('orden')->default(1);
            $table->string('img')->nullable();
            $table->text('ajuda')->nullable();
        });
    }

    private function menus(): MenuService
    {
        if ($this->menuService === null) {
            $this->menuService = app(MenuService::class);
        }

        return $this->menuService;
    }

    public function test_make_detecta_urls_externes_i_internes(): void
    {
        DB::table('menus')->insert([
            [
                'nombre' => 'Extern',
                'url' => 'https://example.org',
                'class' => 'fa-globe',
                'rol' => 2,
                'menu' => 'general',
                'submenu' => '',
                'activo' => 1,
                'orden' => 1,
            ],
            [
                'nombre' => 'Intern',
                'url' => '/ruta-interna',
                'class' => 'fa-link',
                'rol' => 2,
                'menu' => 'general',
                'submenu' => '',
                'activo' => 1,
                'orden' => 2,
            ],
        ]);

        $menu = $this->menus()->make('general', true, (object) ['dni' => 'U1', 'rol' => 2]);

        $this->assertSame('https://example.org', $menu['Extern']['full-url']);
        $this->assertSame('/ruta-interna', $menu['Intern']['url']);
    }

    public function test_clear_cache_forca_reconstruccio_del_menu(): void
    {
        DB::table('menus')->insert([
            'nombre' => 'Intern',
            'url' => '/old',
            'class' => 'fa-link',
            'rol' => 2,
            'menu' => 'general',
            'submenu' => '',
            'activo' => 1,
            'orden' => 1,
        ]);

        $user = (object) ['dni' => 'U2', 'rol' => 2];

        $first = $this->menus()->make('general', true, $user);
        $this->assertSame('/old', $first['Intern']['url']);

        DB::table('menus')->where('nombre', 'Intern')->update(['url' => '/new']);

        $cached = $this->menus()->make('general', true, $user);
        $this->assertSame('/old', $cached['Intern']['url']);

        $this->menus()->clearCache('general', 'U2');

        $refreshed = $this->menus()->make('general', true, $user);
        $this->assertSame('/new', $refreshed['Intern']['url']);
    }

    public function test_manté_comportament_legacy_els_fills_no_es_filtren_per_rol(): void
    {
        DB::table('menus')->insert([
            [
                'nombre' => 'Orientacio',
                'url' => '',
                'class' => 'fa-compass',
                'rol' => 2, // visible per a professor
                'menu' => 'general',
                'submenu' => '',
                'activo' => 1,
                'orden' => 1,
            ],
            [
                'nombre' => 'Actes',
                'url' => '/actes',
                'class' => null,
                'rol' => 99, // rol que no correspon a l'usuari de prova
                'menu' => 'general',
                'submenu' => 'Orientacio',
                'activo' => 1,
                'orden' => 1,
            ],
        ]);

        $menu = $this->menus()->make('general', true, (object) ['dni' => 'U3', 'rol' => 2]);

        $this->assertArrayHasKey('Orientacio', $menu);
        $this->assertArrayHasKey('submenu', $menu['Orientacio']);
        $this->assertSame('/actes', $menu['Orientacio']['submenu']['Actes']['url']);
    }

    public function test_xajuda_accessor_és_robust_i_neteja_html(): void
    {
        $menu = new Menu(['ajuda' => '<b>Text</b><script>x</script>']);
        $this->assertSame('Textx', $menu->Xajuda);

        $withoutAjuda = new Menu();
        $this->assertSame('', $withoutAjuda->Xajuda);
    }
}
