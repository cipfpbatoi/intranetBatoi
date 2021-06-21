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
    
    public static function MisModulos($dni=null,$modulo=null)
    {
        $dni = $dni??AuthUser()->dni;
        if ($modulo)
            $modulos = Horario::select('modulo','idGrupo')
                ->Profesor($dni)
                ->whereNotNull('idGrupo')
                ->where('modulo',$modulo)
                ->distinct()
                ->get();
        else
            $modulos = Horario::select('modulo','idGrupo')
                    ->Profesor($dni)
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

        return $this->Grupo->nombre??$this->idGrupo;
    }
    public function getXModuloAttribute(){
        return $this->ModuloCiclo->Xmodulo;
    }
    public function getliteralAttribute(){
        return $this->XGrupo.'-'.$this->XModulo;
    }
    
    public function getseguimientoAttribute(){
        $tr = evaluacion() - 1??1;
        $tipoCiclo = $this->ModuloCiclo->Ciclo->tipo??1;
        $curso = $this->ModuloCiclo->curso??1;
        $trimestre = config("curso.trimestres.$tipoCiclo.$tr.$curso");
        $quants = $this->resultados->where('evaluacion',$trimestre)->count();
        if ($quants){
            return true;
        }elseif (count($this->profesores())) {
            return false;
        }
        return true;
    }

    public function getprofesorAttribute(){
        $a = '';
        foreach ($this->profesores() as $profesor){
            $a .= Profesor::find($profesor['idProfesor'])->FullName.' ';
        }
        return $a;
    }
    
}
