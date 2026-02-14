<?php

namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Model;
use Intranet\Services\Auth\JWTTokenService;
use Styde\Html\Facades\Alert;

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
        $dni = $dni??authUser()->dni;
        if ($modulo) {
            $modulos = Horario::select('modulo', 'idGrupo')
                ->Profesor($dni)
                ->whereNotNull('idGrupo')
                ->where('modulo', $modulo)
                ->distinct()
                ->get();
        }
        else {
            $modulos = Horario::select('modulo', 'idGrupo')
                ->Profesor($dni)
                ->whereNotNull('idGrupo')
                ->whereNotIn('modulo', config('constants.modulosNoLectivos'))
                ->distinct()
                ->get();
        }
        $todos = [];
        foreach ($modulos as $modulo){
            if ($mc = Modulo_ciclo::where('idModulo',$modulo->modulo)
                    ->where('idCiclo',$modulo->Grupo->idCiclo)->first()) {
                if ($mg = Modulo_grupo::where('idGrupo', $modulo->idGrupo)
                    ->where('idModuloCiclo', $mc->id)->first()) {
                    {
                        $todos[] = $mg;
                    }
                }
            } else {
                Alert::danger('No se encuentra el ciclo para el modulo ' . $modulo->modulo);
            }
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

    public function getXcicloAttribute(){
        return $this->ModuloCiclo->Xciclo;
    }

    public function getXdepartamentoAttribute(){
        return $this->ModuloCiclo->Departamento->literal;
    }

    public function getXtornAttribute(){
        return $this->Grupo->turno === 'S'?'half-presential':'presential';
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
        }
        if (count($this->profesores())) {
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
    public function getProgramacioLinkAttribute(){
        $centerId = config('contacto.codi');
        $cycleId = $this->ModuloCiclo->idCiclo;
        $moduleCode = $this->ModuloCiclo->idModulo;
        $turn = $this->Xtorn;

        // Construye la URL
        return "https://pcompetencies.cipfpbatoi.es/public/syllabus/{$centerId}/{$cycleId}/{$moduleCode}/{$turn}";

    }

    /*
    public function getTokenLinkAttribute(){
        $service = new JWTTokenService();
        $token = $service->createTokenProgramacio($this->id);
        return "https://pcompetencies.cipfpbatoi.es/login/auth/{$token}";
    }
    */
    
}
