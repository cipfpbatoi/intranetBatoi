<?php

namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Model;
use Intranet\Entities\Alumno;
use Intranet\Events\ActivityReport;
use Intranet\Entities\Horario;
use Illuminate\Support\Facades\Auth;
use Intranet\Entities\Ciclo;

class Grupo extends Model
{

    protected $primaryKey = 'codigo';
    protected $keyType = 'string';
    public $timestamps = true;

    use BatoiModels;

    protected $fillable = [
        'nombre',
        'turno',
        'tutor',
        'idCiclo',
        'codigo',
        'curso'
    ];
    protected $rules = [
    ];
    protected $inputTypes = [
        'turno' => ['disabled' => 'disabled'],
        'tutor' => ['type' => 'select'],
        'idCiclo' => ['type' => 'select']
    ];
    protected $dispatchesEvents = [
        'deleted' => ActivityReport::class,
        'created' => ActivityReport::class,
    ];

    public function Alumnos()
    {
        return $this->hasManyThrough(Alumno::class, Alumno_grupo::class, 'idGrupo', 'idAlumno', 'codigo');
    }

    public function Actividades()
    {
        return $this->belongsToMany(Actividad::class, 'actividad_grupo');
    }

    public function Tutor()
    {
        return $this->hasOne(Profesor::class, 'dni', 'tutor');
    }

    public function Departamento()
    {
        return $this->hasOne(Departamento::class, 'departamento', 'id');
    }

    public function Ciclo()
    {
        return $this->belongsto(Ciclo::class, 'idCiclo', 'id');
    }

    public function Horario()
    {
        return $this->hasMany(Horario::class, 'codigo', 'idGrupo');
    }

    public function getTodosOptions()
    {
        return hazArray(Grupo::all(), 'codigo', 'nombre');
    }

    public function getIdCicloOptions()
    {
        return hazArray(Ciclo::all(), 'id', 'ciclo');
    }

    public function getTutorOptions()
    {
        return hazArray(Profesor::orderBy('apellido1')
                        ->orderBy('apellido2')->get(), 'dni', ['apellido1', 'apellido2', 'nombre']);
    }

    public function scopeQTutor($query,$profesor=null)
    {
        $profesor = isset($profesor)?$profesor:AuthUser()->dni;
        if  (($sustituido = Profesor::findOrFail($profesor)->sustituye_a)!=' ')
            return $query->where('tutor', $sustituido)->orWhere('tutor',$profesor);
        else 
            return $query->where('tutor', $profesor);
    }

    public function scopeMisGrupos($query, $profesor = null)
    {
        $profesor = $profesor ? $profesor : AuthUser();
        $grupos = Horario::select('idGrupo')
                ->Profesor($profesor->dni)
                ->whereNotNull('idGrupo')
                ->distinct()
                ->get()
                ->toarray();
        return $query->whereIn('codigo', $grupos);
    }
    
    public function scopeMatriculado($query, $alumno )
    {
        $grupos = Alumno_grupo::select('idGrupo')->where('idAlumno',$alumno)->get()->toarray();
        return $query->whereIn('codigo', $grupos);
    }

    public function scopeQueDepartamento($query, $dep)
    {
        $ciclos = Ciclo::select('id')->QueDepartamento($dep)->get()->toarray();
        return $query->whereIn('idCiclo', $ciclos);
    }
    public function scopeCurso($query,$curso)
    {
        return $query->where('curso',$curso);
    }

    public function getProfesores()
    {
        $profesores = Horario::select('idProfesor')
                ->distinct()
                ->Grup($this->codigo)
                ->get();
        return $profesores;
    }

    public function getProyectoAttribute()
    {
        return ($this->curso == 2 && $this->Ciclo->normativa == 'LOE' && $this->Ciclo->tipo == 2);
    }
    public function getXcicloAttribute(){
        return isset($this->Ciclo->ciclo)?$this->Ciclo->ciclo:$this->idCiclo;
    }
    public function getXtutorAttribute(){
        return isset($this->Tutor->Sustituye->dni)?$this->Tutor->Sustituye->FullName:(isset($this->Tutor->nombre)?$this->Tutor->FullName:'');
    }
    public function getActaAttribute(){
        return $this->acta_pendiente?'Pendiente':'';
    }
    

}
