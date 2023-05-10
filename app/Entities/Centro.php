<?php

namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Model;
use Jenssegers\Date\Date;
use Intranet\Events\ActivityReport;

class Centro extends Model
{

    use BatoiModels;

    protected $table = 'centros';
    protected $fillable = [
        'idEmpresa',
        'nombre',
        'direccion',
        'localidad',
        'horarios',
        'observaciones',
        'idioma',
        'codiPostal',
        'idSao'];
    protected $rules = [
        'idEmpresa' => 'required',
        'nombre' => 'required',
        'direccion' => 'required',
        'localidad' => 'required',
    ];
    protected $inputTypes = [
        'idEmpresa' => ['disabled' => 'disabled'],
        'observaciones' => ['type' => 'textarea'],
        'idioma' => ['type' => 'select'],
    ];
    protected $hidden = ['created_at', 'updated_at'];
    protected $dispatchesEvents = [
        'saved' => ActivityReport::class,
        'deleted' => ActivityReport::class,
    ];

    public function Empresa()
    {
        return $this->belongsTo(Empresa::class, 'idEmpresa', 'id');
    }
    public function scopeEmpresa($query, $empresa)
    {
        return $query->where('idEmpresa', $empresa);
    }
    
    public function colaboraciones()
    {
        return $this->hasMany(Colaboracion::class, 'idCentro', 'id');
    }
    public function instructores()
    {
        return $this->belongsToMany(Instructor::class, 'centros_instructores', 'idCentro', 'idInstructor', 'id', 'dni');
    }
    public function getIdiomaOptions()
    {
        return config('auxiliares.idiomas');
    }
    

}
