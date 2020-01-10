<?php

namespace Intranet\Entities;

use \Illuminate\Database\Eloquent\Model;

class AlumnoGrupo extends Model
{

    public $primaryKey = 'idAlumno';
    protected $keyType = 'string';
    protected $table = 'alumnos_grupos';
    public $timestamps = false;

    
    public static function find($params)
    {
        return static::where('idAlumno', $params[0])->where('idGrupo', $params[1])->first();
    }

    public function Alumno()
    {
        return $this->belongsTo(Alumno::class, 'idAlumno', 'nia');
    }
    public function Grupo()
    {
        return $this->belongsTo(Grupo::class, 'idGrupo', 'codigo');
    }
    public function getNombreAttribute()
    {
        return $this->Alumno->NameFull;
    }
    public function getPoblacionAttribute()
    {
        return $this->Alumno->Poblacion;
    }

    public function getFolAttribute()
    {
        return $this->Alumno->fol;
    }

}
