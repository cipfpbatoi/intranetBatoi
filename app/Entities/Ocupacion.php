<?php

namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Model;

class Ocupacion extends Model
{

    public $primaryKey = 'codigo';
    protected $table = 'ocupaciones';
    public $timestamps = false;
    protected $fillable = [
        'codigo',
        'nombre',
        'nom'
    ];

    public function Ocupacion()
    {
        return $this->hasMany(Horario::class, 'codigo', 'ocupacion');
    }

}