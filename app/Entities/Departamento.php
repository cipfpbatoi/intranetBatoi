<?php

namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Model;
use App;

class Departamento extends Model
{

    public $primaryKey = 'id';
    public $timestamps = false;

    public function Profesor()
    {
        return $this->hasMany(Profesor::class, 'id', 'departamento');
    }
    public function Modulo()
    {
        return $this->belongstoMany(Modulo::class,'modulo_ciclos','idDepartamento','idModulo');
    } 
    
    public function getLiteralAttribute()
    {
        return App::getLocale(session('lang')) == 'es' ? $this->cliteral : $this->vliteral;
    }

}
