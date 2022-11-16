<?php

namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;

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
    public function getliteralAttribute()
    {
        return App::getLocale(session('lang')) == 'es' ? $this->nombre : $this->nom;
    }
}
