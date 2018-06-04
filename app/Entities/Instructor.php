<?php

namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Model;
use Intranet\Events\ActivityReport;
use Intranet\Entities\Centro;

class Instructor extends Model
{

    use BatoiModels;

    protected $table = 'instructores';
    protected $primaryKey = 'dni';
    protected $keyType = 'string';
    protected $fillable = ['dni', 'email', 'nombre','telefono','departamento'];
    protected $rules = [
        'dni' => 'max:10',
        'nombre' => 'required|max:60',
        'email' => 'email|max:60',
        'telefono' => 'max:20'
    ];
    protected $inputTypes = [
        'dni' => ['type' => 'card'],
        'nombre' => ['type' => 'name'],
        'email' => ['type' => 'email'],
        'telefono' => ['type' => 'number']
    ];
    
    public $timestamps = false;
    protected $dispatchesEvents = [
        'saved' => ActivityReport::class,
        'deleted' => ActivityReport::class,
    ];
    
    
    public function Centros()
    {
        return $this->belongsToMany(Centro::class,'centros_instructores', 'idInstructor', 'idCentro', 'dni','id');
    }
    public function Fcts()
    {
        return $this->belongsToMany(Fct::class,'instructor_fcts', 'idInstructor', 'idFct', 'dni','id');
    }
    public function getXcentrosAttribute()
    {
        $centros = '';
        foreach ($this->Centros as $centro){
            $centros .= '- '.$centro->nombre.' -';
        }
        return $centros;
    }
    public function getXNcentrosAttribute()
    {
        return $this->Centros->count();
    }
    public function getNfctsAttribute()
    {
        return $this->Fcts->count();
    }
}
