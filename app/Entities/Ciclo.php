<?php

namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Intranet\Events\ActivityReport;
use Illuminate\Support\Facades\App;
use Jenssegers\Date\Date;

class Ciclo extends Model
{

    use \Intranet\Entities\Concerns\BatoiModels;
    
    protected $table = "ciclos";
    public $timestamps = false;
    protected $fillable = [ 'ciclo','vliteral','cliteral', 'departamento','tipo','normativa','titol','rd','rd2','horasFct','acronim','llocTreball','dataSignaturaDual','competencies'];
    protected $notFillable = ['competencies'];
    protected $inputTypes = [
        'departamento' => ['type' => 'select'],
        'tipo' => ['type' => 'select'],
        'dataSignaturaDual' => ['type' => 'date'],
        'competencies' => ['type' => 'file'],
    ];
    protected $dispatchesEvents = [
        'saved' => ActivityReport::class,
        'deleted' => ActivityReport::class,
    ];
    public $casts = [
        'dataSignaturaDual' => 'date'
    ];


    public function Grupos()
    {
        return $this->hasMany(Grupo::class, 'idCiclo','id');
    }
    public function Departament()
    {
        return $this->belongsTo(Departamento::class, 'departamento', 'id');
    }

    public function TutoresFct()
    {
        return $this->hasManyThrough(Profesor::class,Grupo::Class,'idCiclo','dni','id','tutor')->where('curso',2);
    }


    public function colaboraciones()
    {
        return $this->hasMany(Colaboracion::class, 'idCiclo', 'id');
    }

    public function fcts()
    {
        return $this->hasManyThrough(Fct::class,Colaboracion::Class,'idCiclo','idColaboracion');
    }


    public function getTipoOptions()
    {
        return config('auxiliares.tipoEstudio');
    }
    
    public function getDepartamentoOptions()
    {
        return hazArray(Departamento::all(),'id', 'literal');
    }
    public function getXtipoAttribute(){
        return config('auxiliares.tipoEstudio')[$this->tipo];
    }
    public function getCtipoAttribute(){
        return config('auxiliares.tipoEstudioC')[$this->tipo];
    }
    public function getXdepartamentoAttribute(){
        return $this->Departament->literal;
    }
     public function getLiteralAttribute()
    {
        return App::getLocale(session('lang')) == 'es' ? $this->cliteral : $this->vliteral;
    }

    public function getCompleteDualAttribute()
    {
        return isset($this->acronim) && isset($this->llocTreball) && isset($this->dataSignaturaDual);
    }

}
