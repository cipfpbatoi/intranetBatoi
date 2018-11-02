<?php

namespace Intranet\Entities;

use Intranet\Entities\Fct;


class Dual extends Fct
{
    
    protected $table = 'fcts';
    
    protected $fillable = ['idAlumno', 'idColaboracion', 'desde'
        ,'horas','asociacion'];
    protected $inputTypes = [
        'idAlumno' => ['type' => 'select'],
        'idColaboracion' => ['type' => 'select'],
        'asociacion' => ['type' => 'hidden'],
        'desde' => ['type' => 'date'],
    ];
    
    public function __construct()
    {
        $this->asociacion = 3;
        $this->horas = 600;
        $this->correoInstructor = 1;
    }
    
    public function getIdAlumnoOptions(){
        return hazArray(Alumno::misAlumnos(AuthUser()->dni,true)->get(),'nia',['apellido1','apellido2','nombre']);
    }
    
    public function getIdColaboracionOptions(){
        $cicloC = Grupo::select('idCiclo')->QTutor(AuthUser()->dni,true)->get();
        $ciclo = $cicloC->count()>0?$cicloC->first()->idCiclo:'';
        $colaboraciones = Colaboracion::where('idCiclo',$ciclo)->get();
        $todos = [];
        
        foreach ($colaboraciones as $colaboracion){
            $todos[$colaboracion->id] = $colaboracion->Centro->nombre;
            if ($colaboracion->Centro->direccion) $todos[$colaboracion->id].=' ('.$colaboracion->Centro->direccion.')';
                
        }
        return array_sort($todos, function ($value) {
            return $value;
        });
    }
        
}
