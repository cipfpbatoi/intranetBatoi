<?php

namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Intranet\Events\ActivityReport;
use Illuminate\Support\Facades\App;
use Jenssegers\Date\Date;

class Ciclo extends Model
{

    use BatoiModels;
    
    protected $table = "ciclos";
    public $timestamps = false;
    protected $fillable = [ 'ciclo','vliteral','cliteral', 'departamento','tipo','normativa','titol','rd','rd2','horasFct','acronim','llocTreball','dataSignaturaDual'];
    protected $rules = [
        'tipo' => 'required',
        'departamento' => 'required'
    ];
    protected $inputTypes = [
        'departamento' => ['type' => 'select'],
        'tipo' => ['type' => 'select'],
        'dataSignaturaDual' => ['type' => 'date']
    ];
    protected $dispatchesEvents = [
        'saved' => ActivityReport::class,
        'deleted' => ActivityReport::class,
    ];

    public function Departament()
    {
        return $this->belongsTo(Departamento::class, 'departamento', 'id');
    }

    public function colaboraciones()
    {
        return $this->hasMany(Colaboracion::class, 'idCiclo', 'id');
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
    public function getDataSignaturaDualAttribute($salida)
    {
        $fecha = new Date($salida);
        return $fecha->format('d-m-Y');
    }
}
