<?php

namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Intranet\Events\PreventAction;
use Intranet\Events\ActivityReport;
use Intranet\Entities\Profesor;
use Intranet\Entities\Alumno_grupo;
use Intranet\Entities\Horario;

class Resultado extends Model
{

    use BatoiModels;

    protected $table = 'resultados';
    public $timestamps = false;
    protected $fillable = [
        'idGrupo',
        'idModulo',
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
        'idGrupo' => 'required',
        'idModulo' => 'required',
        'evaluacion' => 'required',
        'matriculados' => 'required|numeric|max:60',
        'evaluados' => 'required|numeric|max:60',
        'aprobados' => 'required|numeric|max:60',
        'udProg' => 'required|numeric|max:30',
        'udImp' => 'required|numeric|max:30',
    ];
    protected $inputTypes = [
        'idGrupo' => ['type' => 'select'],
        'idModulo' => ['type' => 'select'],
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

    public function getIdGrupoOptions()
    {
        return hazArray(Grupo::MisGrupos()->get(), 'codigo', 'nombre');
    }

    public function getIdModuloOptions()
    {
        $misModulos = [];
        $modulos = Modulo::Mismodulos()->Lectivos()->get();
        foreach ($modulos as $modulo){
            if (isset($modulo->Ciclo->ciclo))
                $misModulos[$modulo->codigo] = $modulo->literal.' - '.$modulo->Ciclo->ciclo.' - ';
            else
                $misModulos[$modulo->codigo] = $modulo->literal.' - ';
        }
        return $misModulos;
        return hazArray(Modulo::Mismodulos()->get(), 'codigo', 'literal');
    }

    public function scopeQGrupo($query, $grupo)
    {
        return $query->where('idGrupo', $grupo);
    }
    public function scopeQueDepartamento($query,$dep){
        $profesores = Profesor::select('dni')->where('departamento',$dep)->get()->toarray();
        return $query->whereIn('idProfesor',$profesores);
    }
    public function scopeTrimestreCurso($query,$trimestre,$curso){
        $evaluaciones = config("constants.trimestres.$trimestre");
        return $query->where('evaluacion',$evaluaciones[$curso])
                ->whereIn('idGrupo',Grupo::Curso($curso)->get()->toarray());
    }
    

    public function Grupo()
    {
        return $this->belongsTo(Grupo::class, 'idGrupo', 'codigo');
    }
    public function Profesor()
    {
        return $this->belongsTo(Profesor::class, 'idProfesor', 'dni');
    }

    public function Modulo()
    {
        return $this->belongsTo(Modulo::class, 'idModulo', 'codigo');
    }
    
    public function getXGrupoAttribute(){
        return $this->Grupo->nombre;
    }
    public function getXModuloAttribute(){
        if (isset($this->Modulo->Ciclo->ciclo))
            return $this->Modulo->literal . ' - ' . $this->Modulo->Ciclo->ciclo . ' -';
        else return $this->Modulo->literal ;
    }
    public function getXEvaluacionAttribute(){
        return config("constants.nombreEval.$this->evaluacion");
    }
    public function getXProfesorAttribute(){
        return Profesor::find($this->idProfesor)->shortName;
    }

}
