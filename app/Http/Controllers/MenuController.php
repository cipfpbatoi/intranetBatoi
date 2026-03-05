<?php

namespace Intranet\Http\Controllers;

use Intranet\Application\Menu\MenuService;
use Intranet\Entities\Menu;
use Intranet\Http\Controllers\Core\IntranetController;

use Illuminate\Http\Request;
use Intranet\Exceptions\NotFoundDomainException;

/**
 * Controlador de manteniment de menús.
 */
class MenuController extends IntranetController
{
    private ?MenuService $menuService = null;

    /**
     * @var string
     */
    protected $perfil = 'profesor';
    /**
     * @var string
     */
    protected $model = 'Menu';
    /**
     * @var array
     */
    protected $gridFields = [
        'categoria',
        'nombre',
        'descripcion',
        'url',
        'Xrol',
        'Xactivo',
        'Xajuda'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Collection|Menu[]|mixed
     */
    protected function search()
    {
        return $this->menus()->listForGrid();
    }

    public function realStore(Request $request, $id = null)
    {
        $elemento = $this->menus()->saveFromRequest($request, $id);
        return redirect()->route('menu.edit', ['menu' => $elemento->id]);
    }

    /**
     * @param int|string $id
     * @throws NotFoundDomainException
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function copy($id)
    {
        $menu = $this->findModelOrFail(Menu::class, $id, 'Menú no trobat', ['menu_id' => $id]);
        $this->authorize('update', $menu);
        $copia = $this->menus()->copy($id);
        return redirect()->route('menu.edit', ['menu' => $copia->id]);
    }

    /**
     * @param int|string $id
     * @throws NotFoundDomainException
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function up($id)
    {
        $menu = $this->findModelOrFail(Menu::class, $id, 'Menú no trobat', ['menu_id' => $id]);
        $this->authorize('update', $menu);
        $this->menus()->moveUp($id);
        return redirect()->route('menu.index');
    }

    /**
     * @param int|string $id
     * @throws NotFoundDomainException
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function down($id)
    {
        $menu = $this->findModelOrFail(Menu::class, $id, 'Menú no trobat', ['menu_id' => $id]);
        $this->authorize('update', $menu);
        $this->menus()->moveDown($id);
        return redirect()->route('menu.index');
    }

    /**
     * Guarda un nou menú amb autorització explícita.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Menu::class);
        return parent::store($request);
    }

    /**
     * Actualitza un menú amb autorització explícita.
     *
     * @param int|string $id
     * @throws NotFoundDomainException
     */
    public function update(Request $request, $id)
    {
        $menu = $this->findModelOrFail(Menu::class, $id, 'Menú no trobat', ['menu_id' => $id]);
        $this->authorize('update', $menu);
        return parent::update($request, $id);
    }

    /**
     * @param int|string $id
     * @throws NotFoundDomainException
     * @return \Illuminate\Http\RedirectResponse
     */
    public function active($id)
    {
        $menu = $this->findModelOrFail(Menu::class, $id, 'Menú no trobat', ['menu_id' => $id]);
        $this->authorize('update', $menu);
        $response = parent::active($id);
        $this->menus()->clearCache();

        return $response;
    }

    /**
     * @param int|string $id
     * @throws NotFoundDomainException
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $menu = $this->findModelOrFail(Menu::class, $id, 'Menú no trobat', ['menu_id' => $id]);
        $this->authorize('delete', $menu);
        $response = parent::destroy($id);
        $this->menus()->clearCache();

        return $response;
    }

    private function menus(): MenuService
    {
        if ($this->menuService === null) {
            $this->menuService = app(MenuService::class);
        }

        return $this->menuService;
    }

    /**
     *
     */
    protected function iniBotones()
    {
        $this->panel->setBotonera(['create'], ['delete', 'edit', 'active', 'copy','up','down']);
    }

}
