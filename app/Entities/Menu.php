<?php

namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Model;
use Styde\Html\Facades\Menu as StydeMenu;
use Illuminate\Support\Facades\Auth;

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
    ];
    protected $rules = [
        'nombre' => 'required',
        'rol' => 'required|integer',
        'activo' => 'required',
    ];
    protected $inputTypes = [
        'menu' => ['type' => 'hidden'],
    ];

    public function __construct()
    {
        $this->menu = 'general';
        $this->activo = 1;
        //$this->orden = Menu::select('orden')->orderby('orden','DESC')->first()->orden + 1;
    }

    private static function tipoUrl($url)
    {
        if (strpos($url, 'ttp::'))
            return 'full-url';
        else
            return 'url';
    }

    public static function make($nom, $array = false)
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
                        'img' => $item->img, 'roles' => $item->rol);
                }
            } else {
                $menu[$sitem->nombre] = array(self::tipoUrl($sitem->url) => $sitem->url, 'class' => $sitem->class);
            }
        }
        if ($array)
            return $menu;
        else
            return StydeMenu::make($menu);
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

}
