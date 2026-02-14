<?php

namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Model;
use Intranet\Events\ActivityReport;

class Espacio extends Model
{
    protected $table = 'espacios';
    public $timestamps = false;
    protected $primaryKey = 'aula';
    protected $keyType = 'string';
    protected $fillable = [ 'aula','descripcion','idDepartamento','gMati', 'gVesprada','reservable'];
    protected $inputTypes = [
        'reservable' => ['type' => 'checkbox'],
    ];

    use \Intranet\Entities\Concerns\BatoiModels;



    protected $dispatchesEvents = [
        'saved' => ActivityReport::class,
        'deleted' => ActivityReport::class,
    ];

    public function Departamento()
    {
        return $this->belongsTo(Departamento::class, 'idDepartamento');
    }

    public function GruposMati()
    {
        return $this->belongsTo(Grupo::class, 'gMati', 'codigo');
    }

    public function GruposVesprada()
    {
        return $this->belongsTo(Grupo::class, 'gVesprada', 'codigo');
    }

    public function getIdDepartamentoOptions()
    {
        return hazArray(Departamento::all(), 'id', 'literal');
    }

    public function getGMatiOptions()
    {
        return hazArray(Grupo::all(), 'codigo', 'nombre');
    }

    public function getGVespradaOptions()
    {
        return $this->getGMatiOptions();
    }
    public function getXDepartamentoAttribute()
    {
        return $this->Departamento->literal;
    }
    public function Materiales()
    {
        return $this->hasMany(Material::class, 'espacio', 'aula')->where('inventariable', 1);
    }

}
