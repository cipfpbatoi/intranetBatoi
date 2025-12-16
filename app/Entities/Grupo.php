<?php

namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Model;
use Intranet\Events\ActivityReport;

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



    public function Ciclo()
    {
        return $this->belongsto(Ciclo::class, 'idCiclo', 'id');
    }

    public function Horario()
    {
        return $this->hasMany(Horario::class, 'idGrupo', 'codigo');
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



    public function scopeQTutor($query, $profesor = null)
    {
        $profesor = $profesor ?? authUser()->dni;
        $sustituido = optional(Profesor::find($profesor))->sustituye_a;

        return $query->where(function ($q) use ($profesor, $sustituido) {
            $q->where('tutor', $profesor);
            if ($sustituido && trim($sustituido) !== '') {
                $q->orWhere('tutor', $sustituido);
            }
        });
    }


    public function scopeLargestByAlumnes($query)
    {
        return $query
            ->withCount('alumnos')
            ->orderByDesc('alumnos_count')
            ->orderBy('codigo'); // criteri de desempat opcional
    }

    public function scopeMisGrupos($query, $profesor = null)
    {
        $profesor = $profesor ?? authUser();
        $grupos = Horario::select('idGrupo')
            ->Profesor($profesor->dni)
            ->Lectivos()
            ->whereNotNull('idGrupo')
            ->distinct()
            ->pluck('idGrupo')
            ->toArray();

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
        return $this->Ciclo->ciclo ?? $this->idCiclo;
    }


    public function getXtutorAttribute()
    {
        return $this->Tutor->Sustituye->FullName ?? $this->Tutor->FullName ?? '';
    }

    public function getActaAttribute()
    {
        return $this->acta_pendiente ? 'Pendiente' : '';
    }

    public function getCalidadAttribute()
    {
        return (Documento::where('tipoDocumento', 'FCT')->where('grupo', $this->nombre)->where('curso', curso())->count()) ? 'O' : 'X';
    }

    public function getMatriculadosAttribute()
    {
        return AlumnoGrupo::where('idGrupo', $this->codigo)->count();
    }

    public function getAvalFctAttribute()
    {
        $todos = $this->Alumnos;
        $aprob = 0;
        foreach ($todos as $alumno){
            foreach ($alumno->Fcts as $fct){
                if (!$fct->dual && $fct->Colaboracion && $fct->Colaboracion->Ciclo == $this->Ciclo && isset($fct->pivot->calificacion)){
                    $aprob++;
                }
            }

        }

        return $aprob;
    }

    public function getEnDualAttribute()
    {
        $todos = $this->Alumnos;
        $dual = 0;
        foreach ($todos as $alumno){
            foreach ($alumno->Fcts as $fct){
                if ($fct->dual){
                    $dual++;
                }
            }

        }

        return $dual;
    }

    public function getAprobFctAttribute()
    {
        $todos = $this->Alumnos;
        $aprob = 0;
        foreach ($todos as $alumno) {
            foreach ($alumno->Fcts as $fct){
                if (!$fct->dual && $fct->Colaboracion && $fct->Colaboracion->Ciclo == $this->Ciclo && isset($fct->pivot->calificacion) && $fct->pivot->calificacion == 1 )  {
                    $aprob++;
                }
            }

        }

        return $aprob;
    }

    public function getAvalProAttribute()
    {
        $todos = $this->Alumnos;
        $aprob = 0;
        foreach ($todos as $alumno) {
            foreach ($alumno->Fcts as $fct){
                if (!$fct->dual && $fct->Colaboracion && $fct->Colaboracion->Ciclo == $this->Ciclo && $fct->pivot->calProyecto > 0) {
                    $aprob++;
                }
            }

        }
        return $aprob;
    }

    public function getAprobProAttribute()
    {
        $todos = $this->Alumnos;
        $aprob = 0;
        foreach ($todos as $alumno) {
            foreach ($alumno->Fcts as $fct){
                if (!$fct->dual && $fct->Colaboracion && $fct->Colaboracion->Ciclo == $this->Ciclo && isset($fct->pivot->calificacion) && $fct->pivot->calProyecto >= 5){
                    $aprob++;
                }
            }

        }

        return $aprob;
    }

    public function getColocadosAttribute()
    {
        
        $todos = $this->Alumnos;
        $aprob = 0;
        foreach ($todos as $alumno){
            foreach ($alumno->Fcts as $fct){
                if (!$fct->dual && $fct->Colaboracion && $fct->Colaboracion->Ciclo == $this->Ciclo && isset($fct->pivot->insercion) && $fct->pivot->insercion)    {
                        $aprob++;
                }
            }

        }

        return $aprob;
    }
    public function getExentosAttribute()
    {
        $todos = $this->Alumnos;
        $aprob = 0;
        foreach ($todos as $alumno) {
            foreach ($alumno->Fcts as $fct){
                if ($fct->exento){
                    $aprob++;
                }
            }
        }

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
    public function getIsSemiAttribute()
    {
        return ($this->turno == 'S');
    }

    public function getTornAttribute(){
        if  ($this->turno == 'S') {
            return $this->turno;
        }
        $turno = $this->Horario->where('dia_semana','L')->where('modulo','<>','TU01CF')->where('modulo','<>','TU02CF')->sortBy('sesion_orden',0)->first();
        if ($turno) {
            return ucfirst(substr($turno->Hora->turno,0,1));
        }
        return '??';
    }

}
