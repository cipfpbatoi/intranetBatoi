<?php

namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Model;
use Intranet\Entities\Ciclo;
use Intranet\Entities\Modulo;
use Intranet\Events\ActivityReport;
use Intranet\Entities\Profesor;

class Modulo_grupo extends Model
{

    protected $table = 'modulo_grupos';
    public $timestamps = false;
    
    public function Grupo()
    {
        return $this->belongsto(Grupo::class, 'idGrupo', 'codigo');
    }
    public function ModuloCiclo()
    {
        return $this->belongsto(Modulo_ciclo::class, 'idModuloCiclo','id');
    }
    public function resultados()
    {
        return $this->hasMany(Resultado::class,'idModuloGrupo', 'id');
    }
    
    public function Profesores()
    {
        return Horario::select('idProfesor')->distinct()->where('idGrupo',$this->idGrupo)
                ->where('modulo',$this->ModuloCiclo->idModulo)->get()->toArray();
    }
    
    public static function MisModulos()
    {
        $modulos = Horario::select('modulo','idGrupo')
                ->Profesor(AuthUser()->dni)
                ->whereNotNull('idGrupo')
                ->whereNotIn('modulo',config('constants.modulosNoLectivos'))
                ->distinct()
                ->get();
        $todos = [];
        foreach ($modulos as $modulo){
            if ($mc = Modulo_ciclo::where('idModulo',$modulo->modulo)
                    ->where('idCiclo',$modulo->Grupo->idCiclo)->first())
            if ($mg = Modulo_grupo::where('idGrupo',$modulo->idGrupo)
                    ->where('idModuloCiclo',$mc->id)->first() )
            $todos[] = $mg ;
        }
       return $todos;
    }
    
    public function scopeCurso($query,$curso)
    {
        return $query->whereIn('idGrupo',hazArray(Grupo::Curso($curso)->get(),'codigo','codigo'));
    }
    public function getXGrupoAttribute(){

        return isset($this->Grupo->nombre)?$this->Grupo->nombre:$this->idGrupo;
    }
    public function getXModuloAttribute(){
        return $this->ModuloCiclo->Xmodulo;
    }
    public function getliteralAttribute(){
        return $this->XGrupo.'-'.$this->XModulo;
    }
    
    public function getseguimientoAttribute(){
        $tr = evaluacion() - 1;
        if (!$tr) $tr = 1;
        $trimestre = config("curso.trimestres.$tr")[$this->ModuloCiclo->curso];
        return $this->resultados->where('evaluacion',$trimestre)->count();
    }
    public function getprofesorAttribute(){
        $a = '';
        foreach ($this->profesores() as $profesor){
            $a .= Profesor::find($profesor['idProfesor'])->FullName.' ';
        }
        return $a;
    }
    
}
