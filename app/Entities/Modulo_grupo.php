<?php

namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Model;
use Intranet\Application\Grupo\GrupoService;
use Intranet\Services\School\ModuloGrupoService;

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
        return app(ModuloGrupoService::class)->profesoresArray($this);
    }
    
    public static function MisModulos($dni=null,$modulo=null)
    {
        return app(ModuloGrupoService::class)->misModulos($dni ?? authUser()->dni, $modulo);
    }
    
    public function scopeCurso($query,$curso)
    {
        $codigos = app(GrupoService::class)->byCurso((int) $curso)
            ->pluck('codigo')
            ->all();

        return $query->whereIn('idGrupo', $codigos);
    }
    public function getXGrupoAttribute(){

        return $this->Grupo->nombre??$this->idGrupo;
    }
    public function getXModuloAttribute(){
        return $this->ModuloCiclo->Xmodulo ?? '';
    }

    public function getXcicloAttribute(){
        return $this->ModuloCiclo->Xciclo ?? '';
    }

    public function getXdepartamentoAttribute(){
        return $this->ModuloCiclo->Departamento->literal ?? '';
    }

    public function getXtornAttribute(){
        return $this->Grupo && $this->Grupo->turno === 'S' ? 'half-presential' : 'presential';
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
        $profesorIds = app(ModuloGrupoService::class)->profesorIds($this)->values()->all();
        if ($profesorIds === []) {
            return '';
        }

        $profesores = Profesor::query()
            ->whereIn('dni', $profesorIds)
            ->get()
            ->keyBy('dni');

        $nombres = [];
        foreach ($profesorIds as $dni) {
            $profesor = $profesores->get($dni);
            if ($profesor) {
                $nombres[] = $profesor->FullName;
            }
        }

        return $nombres === [] ? '' : implode(' ', $nombres) . ' ';
    }
    public function getProgramacioLinkAttribute(){
        if (!$this->ModuloCiclo) {
            return '';
        }

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
