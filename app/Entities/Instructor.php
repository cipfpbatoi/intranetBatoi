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
    
    
    public function Centros()
    {
        return $this->belongsToMany(Centro::class,'centros_instructores', 'idInstructor', 'idCentro', 'dni','id');
    }
    public function Fcts()
    {
        return $this->belongsToMany(Fct::class,'instructor_fcts', 'idInstructor', 'idFct', 'dni','id')->withPivot('horas');
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
    public function getTutoresFctAttribute()
    {
        $tutors = [];
        foreach ($this->Fcts as $fct){
            $dni = isset($fct->Alumno->Grupo->first()->Tutor->dni)?$fct->Alumno->Grupo->first()->Tutor->dni: authUser()->dni;
            if (!in_array($dni, $tutors)
                &&(authUser()->dni != $dni))
                    $tutors[] = $dni;
        }
        $todos = '';
        foreach ($tutors as $tutor){
            $todos .= '- '.Profesor::find($tutor)->ShortName.' -';
        }
        return $todos;
    }
    
    public function getNombreAttribute()
    {
        return ucwords(mb_strtolower($this->name . ' ' . $this->surnames,'UTF-8'));
    }
    public function getContactoAttribute()
    {
        return ucwords(mb_strtolower($this->name . ' ' . $this->surnames,'UTF-8'));
    }
    public function getIdAttribute()
    {
        return $this->dni;
    }
}
