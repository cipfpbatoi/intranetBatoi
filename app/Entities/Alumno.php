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
        return $this->belongsToMany(Curso::class, 'alumnos_cursos', 'idAlumno','idCurso','nia','id')
                ->withPivot(['registrado','finalizado']);
    }
    public function Colaboracion()
    {
        return $this->belongsToMany(Colaboracion::class, 'colaboraciones', 'idAlumno', 'idColaboracion');
    }

    public function Grupo()
    {
        return $this->belongsToMany(Grupo::class, 'alumnos_grupos', 'idAlumno', 'idGrupo');
    }

    public function Fcts()
    {
        return $this->belongsToMany(Fct::class,'alumno_fcts', 'idAlumno', 'idFct','nia','id')->withPivot(['calificacion','calProyecto','actas','insercion']);
    }
    public function AlumnoFct()
    {
        return $this->HasMany(AlumnoFct::class,'idAlumno' ,'nia');
    }

    public function AlumnoResultado()
    {
        return $this->HasMany(AlumnoResultado::class,'idAlumno' ,'nia');
    }
    
    public function Provincia()
    {
        return $this->belongsTo(Provincia::class, 'provincia','id');
    }


    public function Municipio()
    {
        return $this->belongsTo(Municipio::class,'municipio','cod_municipio')->where('provincias_id',$this->provincia);
    }

    public function scopeQGrupo($query, $grupo)
    {
        if (is_string($grupo))
            $alumnos = AlumnoGrupo::select('idAlumno')->where('idGrupo', '=', $grupo)->get()->toarray();
        else
            $alumnos = AlumnoGrupo::select('idAlumno')->whereIn('idGrupo', $grupo)->get()->toarray();
        return $query->whereIn('nia', $alumnos);
    }

    public function scopeMenor($query, $fecha = null)
    {
        $hoy = $fecha ? new Date($fecha) : new Date();
        $hace18 = $hoy->subYears(18)->toDateString();
        return $query->where('fecha_nac', '>', $hace18);
    }
    public function scopeMisAlumnos($query,$profesor=null,$dual=false)
    {
        $profesor = $profesor ?? AuthUser()->dni;
        $gruposC = Grupo::select('codigo')->QTutor($profesor,$dual)->get();
        $grupos = $gruposC->count()>0?$gruposC->toarray():[];
        $alumnos = hazArray(AlumnoGrupo::select('idAlumno')->whereIn('idGrupo',$grupos)->get(),'idAlumno','idAlumno');
        return $query->whereIn('nia',$alumnos);
        
    }

    public function getDepartamentoAttribute(){
        return $this->Grupo->count()?$this->Grupo->first()->Ciclo->departamento:'99';
    }
    
    public function getTutorAttribute(){
        if ($this->Grupo->count() == 0) return [];
        foreach ($this->Grupo as $grupo){
            $tutor[] = $grupo->Tutor;
        }
        return $tutor;
    }
    
    public function getIdiomaOptions()
    {
        return config('auxiliares.idiomas');
    }
    public function getFullNameAttribute()
    {
        return ucwords(mb_strtolower($this->nombre . ' ' . $this->apellido1 . ' ' . $this->apellido2,'UTF-8'));
    }
    public function getShortNameAttribute()
    {
        return ucwords(mb_strtolower($this->nombre . ' ' . $this->apellido1,'UTF-8'));
    }
    public function getNameFullAttribute()
    {
        return ucwords(mb_strtolower($this->apellido1 . ' ' . $this->apellido2.' , '.$this->nombre,'UTF-8'));
    }
    public function getIdGrupoAttribute()
    {
        return $this->Grupo->first()->codigo;
    }
    public function getHorasFctAttribute()
    {
        $horas = 0;
        foreach ($this->AlumnoFct as $fct){
            $horas += $fct->horas;
        }
        return $horas;
    }
    public function getFechaNacAttribute($entrada)
    {
        $fecha = new Date($entrada);
        return $fecha->format('d-m-Y');
    }
    public function getContactoAttribute(){
        return ucwords(mb_strtolower($this->nombre . ' ' . $this->apellido1 . ' ' . $this->apellido2,'UTF-8'));
    }
    public function getIdAttribute(){
        return $this->nia;
    }
    public function saveContact($contacto,$email)
    {
        $this->email = $email;
        $this->save();
    }
    public function getPoblacionAttribute(){
        return $this->Municipio->municipio;
    }

}
