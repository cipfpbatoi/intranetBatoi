<?php

namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Model;
use Intranet\Entities\Ciclo;
use Intranet\Entities\Modulo;
use Intranet\Events\ActivityReport;

class Modulo_ciclo extends Model
{

    protected $table = 'modulo_ciclos';
    public $timestamps = false;
    
    use BatoiModels;

    protected $fillable = [
        'idModulo',
        'idCiclo',
        'curso',
        'enlace',
        'idDepartamento'
    ];
    protected $rules = [
        'idModulo' => 'required',
        'idCiclo' => 'required',
        'curso' => 'required'
    ];
    protected $inputTypes = [
        'idCiclo' => ['type' => 'select'],
        'idModulo' => ['type' => 'select'],
        'idDepartamento' => ['type' => 'select']
    ];
    protected $dispatchesEvents = [
        'saved' => ActivityReport::class,
        'deleted' => ActivityReport::class,
    ];

    public function Ciclo()
    {
        return $this->belongsto(Ciclo::class, 'idCiclo', 'id');
    }
    public function Modulo()
    {
        return $this->belongsto(Modulo::class, 'idModulo', 'codigo');
    }
    public function Departamento()
    {
        return $this->belongsto(Departamento::class, 'idDepartamento', 'id');
    }
    
    public function Programacion()
    {
        return $this->hasOne(Programacion::class, 'idModuloCiclo');
    }

    public function getXmoduloAttribute(){
        return $this->Modulo->literal;
    }
    public function getXcicloAttribute(){
        return $this->Ciclo->literal;
    }
    public function getAcicloAttribute(){
        return $this->Ciclo->ciclo;
    }
    public function getNombreAttribute(){
        return $this->Programacion->Profesor->ShortName;
    }
    public function getIdCicloOptions()
    {
        return hazArray(Ciclo::all(), 'id', 'ciclo');
    }
    public function getIdModuloOptions()
    {
        return hazArray(Modulo::all(), 'codigo', 'literal');
    }
    public function getIdDepartamentoOptions()
    {
        return hazArray(Departamento::all(), 'id', 'literal');
    }
    
    public function getEstadoAttribute()
    {
        return isset($this->Programacion->estado) ? $this->Programacion->estado : 0;
    }
    public function getSituacionAttribute()
    {
        return isblankTrans('models.Modulo.'.$this->estado) ? trans('messages.situations.' . $this->estado) : trans('models.Modelo.'.$this->estado);
    }
    
    
}
