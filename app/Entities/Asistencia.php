<?php

namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Model;

class Asistencia extends Model
{

    protected $table = 'asistencias';
    public $timestamps = false;
    protected $fillable = [
        'idReunion',
        'idProfesor',
    ];

    public function Profesor()
    {
        return $this->belongsTo(Profesor::class, 'idProfesor', 'dni');
    }
}
