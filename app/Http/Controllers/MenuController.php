<?php

namespace Intranet\Http\Controllers;

use Intranet\Http\Controllers\Core\IntranetController;

use Illuminate\Http\Request;
use Intranet\Entities\Menu;
use Illuminate\Support\Facades\Session;

/**
 * Class MenuController
 * @package Intranet\Http\Controllers
 */
class MenuController extends IntranetController
{

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
        'ajuda'
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
            $menu->orden = $orden ++;
            $menu->save();
        }

        foreach (Menu::where('submenu', '')->orderBy('menu')->orderBy('orden')->get() as $menu) {
            $orden = 1;
            foreach (Menu::where('submenu', $menu->nombre)->orderBy('orden')->get() as $submenu) {
                $submenu->orden = $orden ++;
                $submenu->save();
            }
        }
    }

    public function realStore(Request $request, $id = null)
    {
        $elemento = $id ? Menu::find($id) : new Menu;
        $elemento->fillAll($request);
        $elemento->save();
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
        }
        return redirect('/menu');
    }

    /**
     *
     */
    protected function iniBotones()
    {
        $this->panel->setBotonera(['create'], ['delete', 'edit', 'active', 'copy','up','down']);
    }

}
