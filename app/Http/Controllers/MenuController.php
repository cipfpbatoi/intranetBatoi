<?php

namespace Intranet\Http\Controllers;

use Intranet\Application\Menu\MenuService;
use Intranet\Http\Controllers\Core\IntranetController;

use Illuminate\Http\Request;
use Intranet\Entities\Menu;

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
        self::sort();
        return Menu::all();
    }

    /**
     *
     */
    private static function sort()
    {
        $anterior = '';
        foreach (Menu::where('submenu', '')->orderBy('menu')->orderBy('orden')->get() as $menu) {
            if ($anterior != $menu->menu) {$orden = 1;$anterior=$menu->menu; }
            if ((int) $menu->orden !== $orden) {
                $menu->orden = $orden;
                $menu->save();
            }
            $orden++;
        }

        foreach (Menu::where('submenu', '')->orderBy('menu')->orderBy('orden')->get() as $menu) {
            $orden = 1;
            foreach (Menu::where('submenu', $menu->nombre)->orderBy('orden')->get() as $submenu) {
                if ((int) $submenu->orden !== $orden) {
                    $submenu->orden = $orden;
                    $submenu->save();
                }
                $orden++;
            }
        }
    }

    public function realStore(Request $request, $id = null)
    {
        $elemento = $id ? Menu::find($id) : new Menu;
        $elemento->fillAll($request);
        $elemento->save();
        $this->menus()->clearCache();
        return redirect("/menu/$elemento->id/edit");
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function copy($id)
    {
        $elemento = Menu::find($id);
        $copia = new Menu;
        $copia->fill($elemento->toArray());
        $copia->orden = Menu::where('menu', $elemento->menu)->where('submenu', $elemento->submenu)->max('orden') + 1;
        $copia->activo = false;
        $copia->save();
        $this->menus()->clearCache();
        return redirect("/menu/$copia->id/edit");
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function up($id)
    {
        $elemento = Menu::find($id);
        $inicial = $elemento->orden;
        $orden = $elemento->orden;
        $find = false;
        while (!$find && $orden>1) {
            $find = Menu::where('orden', --$orden)
                ->where('menu', $elemento->menu)
                ->where('submenu', $elemento->submenu)
                ->first();
        }
        if ($find) {
            $find->orden = $inicial;
            $elemento->orden = $orden;
            $find->save();
            $elemento->save();
            $this->menus()->clearCache();
        }
        return redirect('/menu');
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function down($id)
    {
        $elemento = Menu::find($id);
        $inicial = $elemento->orden;
        $orden = $elemento->orden;
        $find = false;
        while (!$find && $orden < 100) {
            $find = Menu::where('orden', ++$orden)
                ->where('menu', $elemento->menu)
                ->where('submenu', $elemento->submenu)
                ->first();
        }
        if ($find) {
            $find->orden = $inicial;
            $elemento->orden = $orden;
            $find->save();
            $elemento->save();
            $this->menus()->clearCache();
        }
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
