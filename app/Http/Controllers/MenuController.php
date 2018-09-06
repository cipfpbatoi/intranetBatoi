<?php

namespace Intranet\Http\Controllers;

use Illuminate\Http\Request;
use Intranet\Entities\Menu;
use Illuminate\Support\Facades\Session;

class MenuController extends IntranetController
{

    protected $perfil = 'profesor';
    protected $model = 'Menu';
    protected $gridFields = ['categoria', 'nombre','descripcion', 'url', 'Xrol', 'Xactivo','ajuda'];
    //protected $modal = true;
    
//    protected function search(){
//        return Menu::where('menu', '=', 'general')
//                ->get();
//    }
    protected function search(){
        $anterior = '';
        foreach (Menu::where('submenu','')->orderBy('menu')->orderBy('orden')->get() as $menu){
            if ($anterior != $menu->menu) {$orden = 1;$anterior=$menu->menu; }
            $menu->orden = $orden ++;
            $menu->save();
        };
        
        foreach (Menu::where('submenu','')->orderBy('menu')->orderBy('orden')->get() as $menu){
            $orden = 1;
            foreach (Menu::where('submenu',$menu->nombre)->orderBy('orden')->get() as $submenu){
                $submenu->orden = $orden ++;
                $submenu->save(); 
            }
        }
        return Menu::all();
    }

    public function copy($id)
    {
        $elemento = Menu::find($id);
        $copia = New Menu;
        $copia->fill($elemento->toArray());
        $copia->orden = Menu::where('menu',$elemento->menu)->where('submenu',$elemento->submenu)->max('orden') + 1;
        $copia->activo = false;
        $copia->save();
        return redirect("/menu/$copia->id/edit");
    }
    public function up($id){
        $elemento = Menu::find($id);
        $inicial = $elemento->orden;
        $orden = $elemento->orden;
        $find = false;
        while (!$find && $orden>1) 
            $find = Menu::where('orden',--$orden)->where('menu',$elemento->menu)->where('submenu',$elemento->submenu)->first(); 
        
        if ($find){
            $find->orden = $inicial;
            $elemento->orden = $orden;
            $find->save();
            $elemento->save();
        }   
        return redirect('/menu');
    }
    public function down($id){
        $elemento = Menu::find($id);
        $inicial = $elemento->orden;
        $orden = $elemento->orden;
        $find = false;
        while (!$find && $orden < 100)
            $find = Menu::where('orden',++$orden)->where('menu',$elemento->menu)->where('submenu',$elemento->submenu)->first(); 
        
        if ($find){
            $find->orden = $inicial;
            $elemento->orden = $orden;
            $find->save();
            $elemento->save();
        } 
        return redirect('/menu');
    }

    protected function iniBotones()
    {
        $this->panel->setBotonera(['create'], ['delete', 'edit', 'active', 'copy','up','down']);
    }

}
