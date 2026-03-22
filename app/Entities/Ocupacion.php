<?php

namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;

class Ocupacion extends Model
{

    public $primaryKey = 'codigo';
    protected $table = 'ocupaciones';
    public $timestamps = false;
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'codigo',
        'nombre',
        'nom'
    ];

    public function Horarios()
    {
        return $this->hasMany(Horario::class, 'ocupacion', 'codigo');
    }

    // Legacy alias per compatibilitat amb crides antigues.
    public function Ocupacion()
    {
        return $this->Horarios();
    }
    public function getliteralAttribute()
    {
        $lang = session('lang', App::getLocale());
        if ($lang === 'es') {
            return $this->nombre ?? $this->nom ?? '';
        }

        return $this->nom ?? $this->nombre ?? '';
    }
}
