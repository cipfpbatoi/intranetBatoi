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
        return app(ModuloGrupoService::class)->hasSeguimiento($this);
    }

    public function getprofesorAttribute(){
        return app(ModuloGrupoService::class)->profesorNombres($this);
    }
    
    public function getProgramacioLinkAttribute(){
        return app(ModuloGrupoService::class)->programacioLink($this);
    }

    /*
    public function getTokenLinkAttribute(){
        $service = new JWTTokenService();
        $token = $service->createTokenProgramacio($this->id);
        return "https://pcompetencies.cipfpbatoi.es/login/auth/{$token}";
    }
    */
    
}
