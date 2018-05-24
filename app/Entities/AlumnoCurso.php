<?php

namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Model;

class AlumnoCurso extends Model
{

    protected $table = 'alumnos_cursos';

    public function scopeCurso($query, $curso)
    {
        return $query->where('idCurso', $curso);
    }
    public function scopeFinalizado($query)
    {
        return $query->where('finalizado',1);
    }
    
    public function Alumno()
    {
        return $this->belongsTo(Alumno::class, 'idAlumno', 'nia');
    }
//    public function Curso()
//    {
//        return $this->belongsTo(Grupo::class, 'idCurso', 'id');
//    }
    public function getNombreAttribute()
    {
        return $this->Alumno->NameFull;
    }
}
