<?php

namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Intranet\Entities\AlumnoCurso;
use Intranet\Events\ActivityReport;

// antigua manipuladores

class Curso extends Model
{

    use BatoiModels;

    protected $nombre = 'Cursos';
    protected $fillable = [
        'titulo',
        'tipo',
        'comentarios',
        'profesorado',
        'activo',
        'horas',
        'fecha_inicio',
        'fecha_fin',
        'hora_ini',
        'hora_fin',
        'aforo'
    ];
    protected $inputTypes = [
        'tipo' => ['type' => 'hidden'],
        'comentario' => ['type' => 'textarea'],
        'profesorado' => ['type' => 'textarea'],
        'activo' => ['type' => 'radios', 'default' => [1 => 'Activo', 0 => 'Inactivo'], 'inline' => 'inline'],
        'fecha_inicio' => ['type' => 'date'],
        'fecha_fin' => ['type' => 'date'],
        'hora_ini' => ['type' => 'time'],
        'hora_fin' => ['type' => 'time'],
    ];
    protected $dispatchesEvents = [
        'saved' => ActivityReport::class,
        'deleted' => ActivityReport::class,
    ];
    protected $attributes = ['aforo'=>0];


    public function Alumnos()
    {
        return $this->belongstoMany(Alumno::class, 'alumnos_cursos', 'idCurso', 'idAlumno')->withPivot('id', 'registrado');
    }

    public function Asistentes()
    {
        return $this->belongstoMany(Alumno::class, 'alumnos_cursos', 'idCurso', 'idAlumno')->withPivot('id', 'finalizado')
            ->where('finalizado',1);

    }

    public function Registrado()
    {
        return AlumnoCurso::where('idCurso', $this->id)
                        ->where('idAlumno', authUser()->nia)
                        ->count();
    }

    public function getFechaInicioAttribute($entrada)
    {
        $fecha =  Carbon::parse($entrada);
        return $fecha->format('d-m-Y');
    }

    public function getFechaFinAttribute($salida)
    {
        $fecha =  Carbon::parse($salida);
        return $fecha->format('d-m-Y');
    }

    public function getHorainiAttribute($salida)
    {
        $fecha =  Carbon::parse($salida);
        return $fecha->format('H:i');
    }

    public function getHorafinAttribute($salida)
    {
        $fecha =  Carbon::parse($salida);
        return $fecha->format('H:i');
    }
    public function getNAlumnosAttribute()
    {
        return AlumnoCurso::where('idCurso', $this->id)->count();
    }
    public function getEstadoAttribute()
    {
        return $this->activo?trans('validation.attributes.Activo'):trans('validation.attributes.Inactivo');
    }

    public function scopeActivo($query)
    {
        return $query->where('activo', 1);
    }

}
