<?php

namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{

    use \Intranet\Entities\Concerns\BatoiModels;

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

    public function getXrolAttribute()
    {
        return implode(',', nameRolesUser($this->rol));
    }
    public function getXactivoAttribute()
    {
        return trans('messages.states.' . $this->activo);
    }
    public function getCategoriaAttribute()
    {
        if ($this->submenu == '') {
            return $this->menu.' ('.str_pad($this->orden, 2, '0', STR_PAD_LEFT).')';
        } else {
            return $this->menu.'-'.$this->submenu.' ('.str_pad($this->orden, 2, '0', STR_PAD_LEFT).')';
        }
    }
    public function getDescripcionAttribute()
    {
        return trans("messages.menu.".ucwords($this->nombre));
    }

    /**
     * Versió segura de l'ajuda per al grid (sense HTML).
     */
    public function getXajudaAttribute()
    {
        return trim(strip_tags((string) ($this->attributes['ajuda'] ?? '')));
    }

}
