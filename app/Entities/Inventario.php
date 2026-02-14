<?php


namespace Intranet\Entities;


class Inventario extends Material
{
    protected $visible = [
        'id',
        'descripcio',
        'espai',
        'estat',
        'nserieprov',
        'marca',
        'modelo',
        'orige',
        'proveedor',
        'articulo_lote_id'
    ];

    public function getEspaiAttribute()
    {
        return $this->espacio.' ('.$this->Espacios->descripcion.')';
    }

    public function getDescripcioAttribute()
    {
        return $this->descripcion;
    }

    public function getEstatAttribute()
    {
        return $this->State;
    }

    public function getOrigeAttribute()
    {
        return config('auxiliares.procedenciaMaterial')[$this->procedencia];
    }


}