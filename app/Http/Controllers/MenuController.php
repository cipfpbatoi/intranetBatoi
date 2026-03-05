<?php

namespace Intranet\Http\Controllers;

use Intranet\Application\Menu\MenuService;
use Intranet\Entities\Menu;
use Intranet\Http\Controllers\Core\IntranetController;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Intranet\Exceptions\NotFoundDomainException;

/**
 * Class MenuController
 * @package Intranet\Http\Controllers
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
     * @param int|string $id
     * @throws NotFoundDomainException
     * @return Menu
     */
    private function findMenuOrFail($id): Menu
    {
        try {
            return Menu::findOrFail((int) $id);
        } catch (ModelNotFoundException $e) {
            throw new NotFoundDomainException('Menú no trobat', ['menu_id' => $id], $e);
        }
    }

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
        $this->authorize('update', $this->findMenuOrFail($id));
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
        $this->authorize('update', $this->findMenuOrFail($id));
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
        $this->authorize('update', $this->findMenuOrFail($id));
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
        $this->authorize('update', $this->findMenuOrFail($id));
        return parent::update($request, $id);
    }

    /**
     * @param int|string $id
     * @throws NotFoundDomainException
     * @return \Illuminate\Http\RedirectResponse
     */
    public function active($id)
    {
        $this->authorize('update', $this->findMenuOrFail($id));
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
        $this->authorize('delete', $this->findMenuOrFail($id));
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
