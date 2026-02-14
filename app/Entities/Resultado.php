<?php

namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Intranet\Events\PreventAction;
use Intranet\Events\ActivityReport;
use Intranet\Entities\Profesor;
use Intranet\Entities\AlumnoGrupo;
use Intranet\Entities\Horario;
use Intranet\Entities\Modulo_ciclo;

class Resultado extends Model
{

    use \Intranet\Entities\Concerns\BatoiModels;

    protected $table = 'resultados';
    public $timestamps = false;
    protected $fillable = [
        'idModuloGrupo',
        'evaluacion',
        'matriculados',
        'evaluados',
        'aprobados',
        'udProg',
        'udImp',
        'observaciones',
        'idProfesor',
    ];


    protected $inputTypes = [
        'idModuloGrupo' => ['type' => 'select'],
        'evaluacion' => ['type' => 'select'],
        'observaciones' => ['type' => 'textarea'],
        'idProfesor' => ['type' => 'text'],
        
    ];
    protected $dispatchesEvents = [
        'deleting' => PreventAction::class,
        'updating' => PreventAction::class,
        'saved' => ActivityReport::class,
        'deleted' => ActivityReport::class,
        'created' => ActivityReport::class,
    ];


    
    public function getEvaluacionOptions()
    {
        return config('auxiliares.nombreEval');
    }


    public function getIdModuloGrupoOptions()
    {
        $todos = [];
        foreach (Modulo_grupo::MisModulos() as $uno) {
            $todos[$uno->id] = $uno->Grupo->nombre . ' - ' . $uno->ModuloCiclo->Modulo->literal;
        }
        return $todos;
    }

    public function scopeQGrupo($query, $grupo)
    {
        return $query->whereIn('idModuloGrupo', hazArray(Modulo_grupo::where('idGrupo', $grupo)->get(), 'id', 'id'));
    }
    public function scopeDepartamento($query, $dep)
    {
        $modulos = Modulo_ciclo::select('id')->where('idDepartamento', $dep)->get()->toArray();
        $modulosGrupos = Modulo_grupo::select('id')->whereIn('idModuloCiclo', $modulos)->get()->toArray();
        return $query->whereIn('idModuloGrupo', $modulosGrupos);
    }
    public function scopeTrimestreCurso($query, $trimestre, $ciclo, $curso)
    {
        $evaluaciones = config("curso.trimestres.$ciclo.$trimestre");
        return $query->where('evaluacion', $evaluaciones[$curso])
                    ->whereIn('idModuloGrupo', hazArray(Modulo_grupo::Curso($curso)->get(), 'id', 'id'));
    }
    
    public function Grupo()
    {
        return $this->belongsTo(Grupo::class, 'idGrupo', 'codigo');
    }
    public function Profesor()
    {
        return $this->belongsTo(Profesor::class, 'idProfesor', 'dni');
    }

    public function ModuloGrupo()
    {
        return $this->belongsTo(Modulo_grupo::class, 'idModuloGrupo', 'id');
    }
    
    public function getModuloAttribute()
    {
        return $this->ModuloGrupo->literal;
    }
    public function getXEvaluacionAttribute()
    {
        return config("auxiliares.nombreEval.$this->evaluacion");
    }
    public function getXProfesorAttribute()
    {
        return Profesor::find($this->idProfesor)->shortName;
    }

}
