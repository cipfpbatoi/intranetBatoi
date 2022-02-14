<?php

namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Model;
use Intranet\Events\ActivityReport;
use Intranet\Events\PreventAction;

class Programacion extends Model
{

    use BatoiModels;
    
    public $fileField = 'idModulo';
    protected $table = "programaciones";
    protected $fillable = [
        'idModuloCiclo',
        'curso',
        'fichero',
    ];
    protected $rules = [
        'fichero' => 'mimes:pdf'
    ];
    protected $inputTypes = [
        'fichero' => ['type' => 'file'],
        'idModuloCiclo' => ['type' => 'select']
    ];
    
    protected $dispatchesEvents = [
        'deleting' => PreventAction::class,
        'created' => ActivityReport::class,
        'deleted' => ActivityReport::class,
    ];
    protected $hidden = ['created_at', 'updated_at'];

    protected $attributes = ['criterios'=>0,'metodologia'=>0];
    
    public function ModuloCiclo()
    {
        return $this->belongsTo(Modulo_ciclo::class, 'idModuloCiclo', 'id');
    }

    public function Departament()
    {
        return $this->hasOneThrough(Departamento::class,Modulo_ciclo::class,'id','id','idModuloCiclo','idDepartamento');
    }
    public function Ciclo()
    {
        return $this->hasOneThrough(Ciclo::class,Modulo_ciclo::class,'id','id','idModuloCiclo','idCiclo');
    }
    public function Modulo()
    {
        return $this->hasOneThrough(Modulo::class,Modulo_ciclo::class,'id','codigo','idModuloCiclo','idModulo');
    }
    public function Profesor()
    {
        return $this->belongsTo(Profesor::class, 'profesor', 'dni');
    }

    public function getidModuloCicloOptions()
    {
        $horas = Horario::select()
                ->Profesor(AuthUser()->dni)
                ->whereNotNull('idGrupo')
                ->whereNotIn('modulo',config('constants.modulosNoLectivos'))
                ->distinct()
                ->get();
        $todos = [];
        foreach ($horas as $hora){
            $mc = Modulo_ciclo::where('idModulo',$hora->modulo)
                    ->where('idCiclo',$hora->Grupo->idCiclo)
                    ->first();
            $todos[$mc->id] = $mc->Xmodulo.' - '.$mc->Xciclo;
        }
        return $todos;
    }

    public function scopeMisProgramaciones($query,$dni = null)
    {
        $profesor = $dni??AuthUser()->dni;
        $horas = Horario::select('modulo','idGrupo')
                ->distinct()
                ->whereNotIn('modulo', config('constants.modulosSinProgramacion'))
                ->Profesor($profesor)
                ->where('modulo', '!=', null)
                ->get();
        $modulos = [];
        foreach ($horas as $hora){
            if ($mc = Modulo_ciclo::where('idModulo',$hora->modulo)
                    ->where('idCiclo',$hora->Grupo->idCiclo)
                    ->first()) {
                $modulos[] = $mc->id;
            }
        }

        return $query->whereIn('idModuloCiclo', $modulos)
                ->where('curso',Curso());
    }

    public function scopeDepartamento($query)
    {
        return $query->whereIn('idModuloCiclo', 
                hazArray(Modulo_ciclo::where('idDepartamento', AuthUser()->departamento)->get(), 'id', 'id'))
                ->where('curso',Curso());
    }
    
    public function nomFichero()
    {
        $fichero = basename($this->fichero);
        $partido = explode('_',$fichero);
        if (count($partido) == 3){
            return $partido[0].'_'.$partido[1];
        }
        else
        {
            return $partido[0];
        }
    }

    public function getXdepartamentoAttribute(){
        return $this->Departament->literal??'';
    }
    public function getXModuloAttribute(){
        return $this->Modulo->literal??'';
    }
    public function getXCicloAttribute(){
        return $this->Ciclo->literal??'';
    }
    public function getDescripcionAttribute(){
        return isset($this->ModuloCiclo->idCiclo)?$this->ModuloCiclo->Aciclo." - ".$this->ModuloCiclo->Xmodulo:'';
    }
    public function getXnombreAttribute(){
        return $this->Profesor->ShortName??'';
    }

    
    
    public function getSituacionAttribute(){
        return isblankTrans('models.Comision.' . $this->estado) ? trans('messages.situations.' . $this->estado) : trans('models.Comision.' . $this->estado);
    }
    public static function resolve($id,$mensaje = null)
    {
        return static::putEstado($id, config('modelos.' . getClass(static::class) . '.resolve'), $mensaje);
    }
    
}
