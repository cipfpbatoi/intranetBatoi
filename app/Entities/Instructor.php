<?php

namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Model;
use Intranet\Events\ActivityReport;
use Intranet\Entities\Centro;

class Instructor extends Model
{

    use \Intranet\Entities\Concerns\BatoiModels;

    protected $table = 'instructores';
    protected $primaryKey = 'dni';
    protected $keyType = 'string';
    protected $fillable = ['dni', 'email', 'name','surnames','telefono','departamento'];
    protected $rules = [
        'dni' => 'required|max:12',
        'name' => 'required|max:60',
        'surnames' => 'required|max:60',
        'email' => 'required|email|max:60',
        'telefono' => 'max:20'
    ];
    protected $inputTypes = [
        'dni' => ['type' => 'card'],
        'name' => ['type' => 'name'],
        'surnames' => ['type' => 'name'],
        'email' => ['type' => 'email'],
        'telefono' => ['type' => 'number']
    ];
    
    public $timestamps = false;
    protected $dispatchesEvents = [
        'saved' => ActivityReport::class,
        'deleted' => ActivityReport::class,
    ];
    
    
    public function Fcts()
    {
        return $this->belongsTo(Fct::class, 'dni', 'idInstructor');
    }

    public function Centros()
    {
        return $this->belongsToMany(Centro::class, 'centros_instructores', 'idInstructor', 'idCentro', 'dni', 'id');
    }


    public function getXcentrosAttribute()
    {
        $centros = '';
        foreach ($this->Centros as $centro) {
            $centros .= '- '.$centro->nombre.' -';
        }
        return $centros;
    }
    public function getXNcentrosAttribute()
    {
        return $this->Centros->count()??0;
    }
    public function getNfctsAttribute()
    {
        return $this->relationLoaded('Fcts')
            ? $this->Fcts->count()
            : $this->Fcts()->count();
    }

    
    
    public function getNombreAttribute()
    {
        return ucwords(mb_strtolower($this->name . ' ' . $this->surnames, 'UTF-8'));
    }
    public function getContactoAttribute()
    {
        return $this->getNombreAttribute();
    }
    public function getIdAttribute()
    {
        return $this->dni;
    }
}
