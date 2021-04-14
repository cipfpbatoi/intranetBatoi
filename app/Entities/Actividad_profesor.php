<?php

namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Model;

class Actividad_profesor extends Model
{

    protected $table = 'actividad_profesor';
    protected $fillable = [
        'idActividad',
        'idProfesor',
        'coordinador'];
    
    public $timestamps = false;

    public function scopeTutor($query)
    {
        return $query->where('coordinador', '=', 1);
    }
    

}
