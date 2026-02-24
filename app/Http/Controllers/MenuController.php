<?php

namespace Intranet\Http\Controllers;

use Intranet\Application\Menu\MenuService;
use Intranet\Http\Controllers\Core\IntranetController;

use Illuminate\Http\Request;

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
     * @return \Illuminate\Database\Eloquent\Collection|Menu[]|mixed
     */
    protected function search()
    {
        return $this->menus()->listForGrid();
    }

    public function realStore(Request $request, $id = null)
    {
        $elemento = $this->menus()->saveFromRequest($request, $id);
        return redirect("/menu/$elemento->id/edit");
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function copy($id)
    {
        $copia = $this->menus()->copy($id);
        return redirect("/menu/$copia->id/edit");
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function up($id)
    {
        $this->menus()->moveUp($id);
        return redirect('/menu');
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function down($id)
    {
        $this->menus()->moveDown($id);
        return redirect('/menu');
    }

    public function active($id)
    {
        $response = parent::active($id);
        $this->menus()->clearCache();

        return $response;
    }

    public function destroy($id)
    {
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
