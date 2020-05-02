<?php

namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Model;
use Intranet\Providers\AuthServiceProvider;
use Styde\Html\Facades\Menu as StydeMenu;
use Illuminate\Support\Facades\Cache;

class Menu extends Model
{

    use BatoiModels;

    public $timestamps = false;
    protected $fillable = [
        'nombre',
        'url',
        'class',
        'rol',
        'menu',
        'submenu',
        'activo',
        'ajuda'
    ];
    protected $rules = [
        'nombre' => 'required',
        'rol' => 'required|integer',
        'activo' => 'required',
    ];
    protected $inputTypes = [
        
    ];



    private static function tipoUrl($url)
    {
        if (strpos($url, 'ttp::'))
            return 'full-url';
        else
            return 'url';
    }

    public static function make($nom, $array = false){
        /**$menu = Cache::remember('menu'.$nom.AuthUser()->dni,now()->addDay(),function () use ($nom){
           return self::build($nom);
        });**/
        $menu = self::build($nom);
        if ($array) return $menu;
        return StydeMenu::make($menu);
    }

    private static function build($nom)
    {
        $submenus = Menu::where([['menu', '=', $nom], ['submenu', '=', ''],['activo', '=', 1]])
                ->whereIn('rol', RolesUser(AuthUser()->rol))
                ->orderby('orden')
                ->get();
        foreach ($submenus as $sitem) {
            if ($sitem->url == '') {
                $items = Menu::where([['menu', '=', $nom], ['submenu', '=', $sitem->nombre], ['activo', '=', 1]])
                        ->orderBy('orden')
                        ->get();
                $menu[$sitem->nombre]['class'] = $sitem->class;
                foreach ($items as $item) {
                    $menu[$sitem->nombre]['submenu'][$item->nombre] = array(self::tipoUrl($item->url) => $item->url, 
                        'img' => $item->img, 'roles' => $item->rol,'secure'=>true);
                    
                }
            } else {
                $menu[$sitem->nombre] = array(self::tipoUrl($sitem->url) => $sitem->url, 'class' => $sitem->class,'secure'=>true);
            }
        }
        return $menu;
    }
    
    public function getXrolAttribute()
    {
        return implode(',', NameRolesUser($this->rol));
    }
    public function getXactivoAttribute()
    {
        return trans('messages.states.' . $this->activo);
    }
    public function getCategoriaAttribute(){
        if ($this->submenu == '') return $this->menu.' ('.str_pad($this->orden,2,'0',STR_PAD_LEFT).')'; 
        else return $this->menu.'-'.$this->submenu.' ('.str_pad($this->orden,2,'0',STR_PAD_LEFT).')';
    }
    public function getDescripcionAttribute(){
        return trans("messages.menu.".ucwords($this->nombre));
    }

}
