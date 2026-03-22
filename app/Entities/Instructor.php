<?php

namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Intranet\Events\ActivityReport;
use Intranet\Entities\Centro;
use Intranet\Presentation\Crud\InstructorCrudSchema;

class Instructor extends Model
{

    use \Intranet\Entities\Concerns\BatoiModels;

    protected $table = 'instructores';
    protected $primaryKey = 'dni';
    protected $keyType = 'string';
    protected $fillable = ['dni', 'email', 'name','surnames','telefono','departamento'];
    protected $rules = InstructorCrudSchema::RULES;
    protected $inputTypes = InstructorCrudSchema::INPUT_TYPES;
    
    public $timestamps = false;
    protected $dispatchesEvents = [
        'saved' => ActivityReport::class,
        'deleted' => ActivityReport::class,
    ];
    
    
    public function Fcts(): HasMany
    {
        return $this->hasMany(Fct::class, 'idInstructor', 'dni');
    }

    public function Centros(): BelongsToMany
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
        return $this->Centros->count();
    }
    public function getNfctsAttribute()
    {
        if ($this->relationLoaded('Fcts')) {
            return $this->getRelation('Fcts')->count();
        }

        return $this->Fcts()->count();
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
