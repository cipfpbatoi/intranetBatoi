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
        'tutorDual',
        'idCiclo',
        'codigo',
        'curso'
    ];
    protected $rules = [
    ];
    protected $inputTypes = [
        'turno' => ['disabled' => 'disabled'],
        'tutor' => ['type' => 'select'],
        'tutorDual' => ['type' => 'select'],
        'idCiclo' => ['type' => 'select'],
    ];
    protected $dispatchesEvents = [
        'deleted' => ActivityReport::class,
        'created' => ActivityReport::class,
    ];

    public function Alumnos()
    {
        return $this->belongsToMany(Alumno::class, 'alumnos_grupos', 'idGrupo', 'idAlumno', 'codigo', 'nia');
    }

    public function Actividades()
    {
        return $this->belongsToMany(Actividad::class, 'actividad_grupo','idGrupo','idActividad','codigo','id');
    }

    public function Tutor()
    {
        return $this->hasOne(Profesor::class, 'dni', 'tutor');
    }

    public function TutorDual()
    {
        return $this->hasOne(Profesor::class, 'dni', 'tutorDual');
    }

    public function Ciclo()
    {
        return $this->belongsto(Ciclo::class, 'idCiclo', 'id');
    }

    public function Horario()
    {
        return $this->hasMany(Horario::class, 'codigo', 'idGrupo');
    }


    public function Modulos()
    {
        return $this->hasMany(Modulo_grupo::class,'idGrupo','codigo');
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
        return isset($this->Ciclo->departamento)?hazArray(Profesor::orderBy('apellido1')
                        ->orderBy('apellido2')
                        ->where('departamento', $this->Ciclo->departamento)
                        ->get(), 'dni', ['apellido1', 'apellido2', 'nombre']):[];
    }

    public function getTutorDualOptions()
    {
        return isset($this->Ciclo->departamento)?hazArray(Profesor::orderBy('apellido1')
                        ->orderBy('apellido2')
                        ->where('departamento', $this->Ciclo->departamento)
                        ->get(), 'dni', ['apellido1', 'apellido2', 'nombre']):[];
    }

    public function scopeQTutor($query, $profesor = null, $dual = false)
    {
        $profesor = $profesor ?? AuthUser()->dni;
        if ($dual)
            if (($sustituido = Profesor::findOrFail($profesor)->sustituye_a) != ' ')
                return $query->where('tutorDual', $sustituido)->orWhere('tutorDual', $profesor);
            else
                return $query->where('tutorDual', $profesor);
        else
            if (($sustituido = Profesor::findOrFail($profesor)->sustituye_a) != ' ')
                return $query->where('tutor', $sustituido)->orWhere('tutor', $profesor);
            else
                return $query->where('tutor', $profesor);
    }

    public function scopeMisGrupos($query, $profesor = null)
    {
        $profesor = $profesor ?? AuthUser();
        $grupos = Horario::select('idGrupo')
                ->Profesor($profesor->dni)
                ->whereNotNull('idGrupo')
                ->distinct()
                ->get()
                ->toarray();
        return $query->whereIn('codigo', $grupos);
    }
    
    public function scopeMiGrupoModulo($query,$dni,$modulo)
    {
        $grupo = Horario::select('idGrupo')
                ->Profesor($dni)
                ->whereNotNull('idGrupo')
                ->where('modulo',$modulo)
                ->distinct()
                ->get()
                ->toarray();
        return $query->whereIn('codigo', $grupo);
    }

    public function scopeMatriculado($query, $alumno)
    {
        $grupos = AlumnoGrupo::select('idGrupo')->where('idAlumno', $alumno)->get()->toarray();
        return $query->whereIn('codigo', $grupos);
    }

    public function scopeDepartamento($query, $dep)
    {
        $ciclos = Ciclo::select('id')->where('departamento', $dep)->get()->toarray();
        return $query->whereIn('idCiclo', $ciclos);
    }

    public function scopeCurso($query, $curso)
    {
        return $query->where('curso', $curso);
    }

    public function getProyectoAttribute()
    {
        return ($this->curso == 2 && $this->Ciclo->normativa == 'LOE' && $this->Ciclo->tipo == 2);
    }

    public function getXcicloAttribute()
    {
        return isset($this->Ciclo->ciclo) ? $this->Ciclo->ciclo : $this->idCiclo;
    }


    public function getXtutorAttribute()
    {
        return isset($this->Tutor->Sustituye->dni) ? $this->Tutor->Sustituye->FullName : (isset($this->Tutor->nombre) ? $this->Tutor->FullName : '');
    }
    public function getXDualAttribute()
    {
        return isset($this->TutorDual->Sustituye->dni) ? $this->TutorDual->Sustituye->FullName : (isset($this->TutorDual->nombre) ? $this->TutorDual->FullName : '');
    }

    public function getActaAttribute()
    {
        return $this->acta_pendiente ? 'Pendiente' : '';
    }

    public function getCalidadAttribute()
    {
        return (Documento::where('tipoDocumento', 'Qualitat')->where('grupo', $this->nombre)->where('curso', Curso())->count()) ? 'O' : 'X';
    }

    public function getMatriculadosAttribute()
    {
        return AlumnoGrupo::where('idGrupo', $this->codigo)->count();
    }

    public function getAvalFctAttribute()
    {
        $todos = $this->Alumnos;
        $aprob = 0;
        foreach ($todos as $alumno) 
            foreach ($alumno->Fcts as $fct)
                if (!$fct->dual && $fct->Colaboracion && $fct->Colaboracion->Ciclo == $this->Ciclo )
                    if (isset($fct->pivot->calificacion))    
                        $aprob++;
        
        return $aprob;
    }

    public function getAprobFctAttribute()
    {
        $todos = $this->Alumnos;
        $aprob = 0;
        foreach ($todos as $alumno) 
            foreach ($alumno->Fcts as $fct)
                if (!$fct->dual && $fct->Colaboracion && $fct->Colaboracion->Ciclo == $this->Ciclo )
                    if (isset($fct->pivot->calificacion) && $fct->pivot->calificacion == 1 )    
                        $aprob++;
        
        return $aprob;
    }

    public function getAvalProAttribute()
    {
        $todos = $this->Alumnos;
        $aprob = 0;
        foreach ($todos as $alumno) 
            foreach ($alumno->Fcts as $fct)
                if (!$fct->dual && $fct->Colaboracion && $fct->Colaboracion->Ciclo == $this->Ciclo)
                    if (isset($fct->pivot->calProyecto))    
                        $aprob++;
        
        return $aprob;
    }

    public function getAprobProAttribute()
    {
        $todos = $this->Alumnos;
        $aprob = 0;
        foreach ($todos as $alumno) 
            foreach ($alumno->Fcts as $fct)
                if (!$fct->dual && $fct->Colaboracion && $fct->Colaboracion->Ciclo == $this->Ciclo )
                    if (isset($fct->pivot->calProyecto) && $fct->pivot->calificacion >= 5)    
                        $aprob++;
        
        return $aprob;
    }

    public function getColocadosAttribute()
    {
        
        $todos = $this->Alumnos;
        $aprob = 0;
        foreach ($todos as $alumno) 
            foreach ($alumno->Fcts as $fct)
                if (!$fct->dual && $fct->Colaboracion && $fct->Colaboracion->Ciclo == $this->Ciclo  )
                    if (isset($fct->pivot->insercion)&&$fct->pivot->insercion)    
                        $aprob++;
        
        return $aprob;
    }
    public function getExentosAttribute()
    {
        $todos = $this->Alumnos;
        $aprob = 0;
        foreach ($todos as $alumno) 
            foreach ($alumno->Fcts as $fct)
                if ($fct->exento) $aprob++;
        return $aprob;
    }

    public function getResfctAttribute()
    {
        return $this->AprobFct . " de $this->AvalFct";
    }

    public function getResempresaAttribute()
    {
        return $this->Colocados . " de $this->AprobFct";
    }

    public function getResproAttribute()
    {
        return $this->AprobPro . " de $this->AvalPro";
    }

}
