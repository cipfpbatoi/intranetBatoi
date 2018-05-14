<?php

namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Intranet\Events\ActivityReport;

class Ciclo extends Model
{

    use BatoiModels;
    
    protected $table = "ciclos";
    public $timestamps = false;
    protected $fillable = ['codigo', 'ciclo','departamento','tipo','normativa','titol','rd','rd2'];
    protected $rules = [];
    protected $inputTypes = [
        'departamento' => ['type' => 'select'],
        'tipo' => ['type' => 'select'],
    ];
    protected $dispatchesEvents = [
        'saved' => ActivityReport::class,
        'deleted' => ActivityReport::class,
    ];

    public function Departament()
    {
        return $this->belongsTo(Departamento::class, 'departamento', 'id');
    }

    public function scopeQueDepartamento($query, $dep = null)
    {
        $dep = $dep ? $dep : AuthUser()->departamento;
        return $query->where('departamento', '=', $dep);
    }
    public function getTipoOptions()
    {
        return config('constants.tipoEstudio');
    }
    public function getDepartamentoOptions()
    {
        return hazArray(Departamento::all(),'id', 'literal');
    }
    public function getXtipoAttribute(){
        return config('constants.tipoEstudio')[$this->tipo];
    }
    public function getXdepartamentoAttribute(){
        return $this->Departament->literal;
    }
    
}
