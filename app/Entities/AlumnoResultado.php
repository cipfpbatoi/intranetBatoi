<?php

namespace Intranet\Entities;

use \Illuminate\Database\Eloquent\Model;

class AlumnoResultado extends Model
{

    protected $table = 'alumno_resultados';
    public $timestamps = false;

    use BatoiModels;

    protected $fillable = [
        'idAlumno',
        'idModuloGrupo',
        'valoraciones',
        'observaciones'
    ];
    protected $rules = [
        'idAlumno' => 'required',
        'idModuloGrupo' => 'required',
        'observaciones' => 'max:200',
      ];
    protected $inputTypes = [
        'idModuloGrupo' => ['type' => 'hidden'],
        'idAlumno' => ['type' => 'select'],
        'valoraciones' => ['type' => 'select'],
    ];
    protected $attributes = [
        'nota' => 0,
    ];
    

    public function Alumno()
    {
        return $this->belongsTo(Alumno::class, 'idAlumno', 'nia');
    }
    public function ModuloGrupo()
    {
        return $this->belongsTo(Modulo_grupo::class, 'idModuloGrupo', 'id');
    }
    public function getNombreAttribute()
    {
        return $this->Alumno->NameFull;
    }

    public function getidAlumnoOptions()
    {
        $alumnos_rellenos = hazArray(AlumnoResultado::where('idModuloGrupo',$this->idModuloGrupo)->get(),'idAlumno');
        return hazArray($this->ModuloGrupo->Grupo->Alumnos->whereNotIn('nia',$alumnos_rellenos),'nia','fullName');
    }

    /**
    public function getNotaStringAttribute()
    {
        return config('auxiliares.notas')[$this->nota];
    }*/

    public function getValoracionAttribute()
    {
        return config('auxiliares.valoraciones')[$this->valoraciones];
    }

    public function getModuloAttribute()
    {
        return $this->ModuloGrupo->literal;
    }


}
