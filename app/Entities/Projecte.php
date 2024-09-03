<?php

namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use Intranet\Events\ActivityReport;

class Projecte extends Model
{
    use BatoiModels;

    protected $fillable = [
        'idAlumne',
        'grup',
        'titol',
        'objectius',
        'resultats',
        'aplicacions',
        'recursos',
        'descripcio',
        'observacions'
    ];

    public function Alumno()
    {
        return $this->belongsTo(Alumno::class, 'idAlumne', 'nia');
    }

    public function Grupo()
    {
        return $this->belongsTo(Grupo::class, 'grup', 'codigo');
    }

    public function getStatusAttribute()
    {
         return config('auxiliares.estadoProjecte')[$this->estat];
    }

    public function getGrupOptions()
    {
         return hazArray(AuthUser()->Grupo, 'codigo', 'nombre');
    }

}
