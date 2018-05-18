<?php

namespace Intranet\Entities;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Intranet\Entities\Grupo;
use Intranet\Entities\Curso;
use Intranet\Entities\Municipio;
use Jenssegers\Date\Date;

class Alumno extends Authenticatable
{

    use Notifiable,
        BatoiModels;

    public $primaryKey = 'nia';
    public $keyType = 'string';
    protected static $tabla = 'alumnos';
    protected $visible = [
        'nia',
        'dni',
        'nombre',
        'apellido1',
        'apellido2',
        'email',
        'expediente',
        'domicilio',
        'municipio',
        'provincia',
        'telef1',
        'telef2',
        'sexo',
        'codigo_postal',
        'departamento',
        'fecha_ingreso',
        'fecha_matricula',
        'fecha_nac',
        'foto',
        'turno',
        'trabaja',
        'repite'
    ];
    protected $fillable = [
        'codigo',
        'nombre',
        'apellido1',
        'apellido2',
        'email',
        'foto',
        'idioma',
    ];
    protected $rules = [
        'email' => 'required|email',
    ];
    protected $inputTypes = [
        'codigo' => ['type' => 'hidden'],
        'nombre' => ['disabled' => 'disabled'],
        'apellido1' => ['disabled' => 'disabled'],
        'apellido2' => ['disabled' => 'disabled'],
        'foto' => ['type' => 'file'],
        'email' => ['type' => 'email'],
        'idioma' => ['type' => 'select'],
    ];

    public function Curso()
    {
        return $this->belongsToMany(Curso::class, 'curso_alumno', 'curso_id', 'alumno_id');
    }
    public function Colaboracion()
    {
        return $this->belongsToMany(Colaboracion::class, 'colaboraciones', 'idAlumno', 'idColaboracion');
    }

    public function Grupo()
    {
        return $this->belongsToMany(Grupo::class, 'alumnos_grupos', 'idAlumno', 'idGrupo');
    }
    
    public function Provincia()
    {
        return $this->belongsTo(Provincia::class, 'provincia','id');
    }
    public function Municipio()
    {
        return Municipio::where('provincias_id',$this->provincia)->where('cod_municipio',$this->municipio)->first()->municipio;
    }

    public function scopeQGrupo($query, $grupo)
    {
        if (is_string($grupo))
            $alumnos = Alumno_grupo::select('idAlumno')->where('idGrupo', '=', $grupo)->get()->toarray();
        else
            $alumnos = Alumno_grupo::select('idAlumno')->whereIn('idGrupo', $grupo)->get()->toarray();
        return $query->whereIn('nia', $alumnos);
    }

    public function scopeMenor($query, $fecha = null)
    {
        $hoy = $fecha ? new Date($fecha) : new Date();
        $hace18 = $hoy->subYears(18)->toDateString();
        return $query->where('fecha_nac', '>', $hace18);
    }
    public function scopeMisAlumnos($query,$profesor=null)
    {
        $profesor = $profesor?$profesor:AuthUser()->dni;
        $gruposC = Grupo::select('codigo')->QTutor($profesor)->get();
        $grupos = $gruposC->count()>0?$gruposC->toarray():[];   
        $alumnos = Alumno_grupo::select('idAlumno')->whereIn('idGrupo',$grupos)->get();
        return $query->whereIn('nia',$alumnos);
        
    }

//    public function nombre()
//    {
//        return $this->nombre . ' ' . $this->apellido1 . ' ' . $this->apellido2;
//    }
//
//    public function nombreCorto()
//    {
//        return $this->nombre . ' ' . $this->apellido1;
//    }
    public function getDepartamentoAttribute(){
        return $this->Grupo->first()->Ciclo->departamento;
    }
    
    public function getTutorAttribute(){
        return $this->Grupo->first()->Tutor;
    }
    public function getIdiomaOptions()
    {
        return config('constants.idiomas');
    }
    public function getFullNameAttribute()
    {
        return ucwords(mb_strtolower($this->nombre . ' ' . $this->apellido1 . ' ' . $this->apellido2,'UTF-8'));
    }
    public function getShortNameAttribute()
    {
        return ucwords(mb_strtolower($this->nombre . ' ' . $this->apellido1,'UTF-8'));
    }

}
