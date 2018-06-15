<?php

namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Intranet\Events\PreventAction;
use Intranet\Events\ActivityReport;
use Intranet\Entities\Profesor;
use Intranet\Entities\Alumno_grupo;
use Intranet\Entities\Horario;
use Intranet\Entities\Modulo_ciclo;

class Resultado extends Model
{

    use BatoiModels;

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
    protected $rules = [
        'evaluacion' => 'required',
        'matriculados' => 'required|numeric|max:60',
        'evaluados' => 'required|numeric|max:60',
        'aprobados' => 'required|numeric|max:60',
        'udProg' => 'required|numeric|max:30',
        'udImp' => 'required|numeric|max:30',
    ];
    protected $inputTypes = [
        'idModuloGrupo' => ['type' => 'select'],
        'evaluacion' => ['type' => 'select'],
        'observaciones' => ['type' => 'textarea'],
        'idProfesor' => ['type' => 'hidden'],
        
    ];
    protected $dispatchesEvents = [
        'deleting' => PreventAction::class,
        'updating' => PreventAction::class,
        'saved' => ActivityReport::class,
        'deleted' => ActivityReport::class,
        'created' => ActivityReport::class,
    ];

    public function __construct()
    {
        if (AuthUser()) {
            $this->idProfesor = AuthUser()->dni;
        }
    }
    
    
    public function getEvaluacionOptions()
    {
        return config('constants.nombreEval');
    }

//    public function getIdGrupoOptions()
//    {
//        return hazArray(Grupo::MisGrupos()->get(), 'codigo', 'nombre');
//    }

    public function getIdModuloGrupoOptions()
    {
  //      $todos = [];
        foreach (Modulo_grupo::MisModulos() as $uno)
            $todos[$uno->id] = $uno->Grupo->nombre.' - '.$uno->ModuloCiclo->Modulo->literal;
        return $todos;
    }

    public function scopeQGrupo($query,$grupo)
    {
        return $query->whereIn('idModuloGrupo',hazArray(Modulo_grupo::where('idGrupo',$grupo)->get(),'id','id'));
    }
    public function scopeDepartamento($query,$dep){
        $profesores = Profesor::select('dni')->where('departamento',$dep)->get()->toarray();
        return $query->whereIn('idProfesor',$profesores);
    }
    public function scopeTrimestreCurso($query,$trimestre,$curso){
        $evaluaciones = config("curso.trimestres.$trimestre");
        //dd(Modulo_grupo::Curso($curso)->get()->toarray());
        return $query->where('evaluacion',$evaluaciones[$curso])
                    ->whereIn('idModuloGrupo', hazArray(Modulo_grupo::Curso($curso)->get(),'id','id'));
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
    
    public function getModuloAttribute(){
        return $this->ModuloGrupo->Grupo->nombre.'-'.$this->ModuloGrupo->ModuloCiclo->Modulo->literal;
    }
    public function getXEvaluacionAttribute(){
        return config("constants.nombreEval.$this->evaluacion");
    }
    public function getXProfesorAttribute(){
        return Profesor::find($this->idProfesor)->shortName;
    }

}
