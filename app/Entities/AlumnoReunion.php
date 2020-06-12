<?php

namespace Intranet\Entities;

use \Illuminate\Database\Eloquent\Model;

class AlumnoReunion extends Model
{

    protected $table = 'alumno_reuniones';
    public $timestamps = false;


    public function Alumno()
    {
        return $this->belongsTo(Alumno::class, 'idAlumno', 'nia');
    }
    public function Reunion()
    {
        return $this->belongsTo(Reunion::class, 'idReunion', 'id');
    }

}
