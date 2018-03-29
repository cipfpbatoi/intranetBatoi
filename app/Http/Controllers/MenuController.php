<?php

namespace Intranet\Http\Controllers;

use Illuminate\Http\Request;
use Intranet\Entities\Menu;
use Illuminate\Support\Facades\Session;

class MenuController extends IntranetController
{

    protected $perfil = 'profesor';
    protected $model = 'Menu';
    protected $gridFields = ['orden','submenu', 'nombre', 'url', 'Xrol', 'Xactivo'];
    //protected $modal = true;
    
    protected function search(){
        return $this->class::where('menu', '=', 'general')
                ->get();
    }
    

    public function copy($id)
    {
        $elemento = Menu::find($id);
        $copia = New Menu;
        $copia->fill($elemento->toArray());
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
            $find = Menu::where('orden',--$orden)->first(); 
        
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
        while (!$find && $orden < 1000)
            $find = Menu::where('orden',++$orden)->first(); 
        
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
