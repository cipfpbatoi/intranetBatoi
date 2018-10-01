<?php

namespace Intranet\Entities;

use Intranet\Entities\Fct;


class Dual extends Fct
{
    
    protected $table = 'fcts';
    
    protected $fillable = ['idAlumno', 'idColaboracion', 'desde','hasta'
        ,'horas','asociacion','horas_semanales'];
    protected $inputTypes = [
        'idAlumno' => ['type' => 'select'],
        'idColaboracion' => ['type' => 'select'],
        'asociacion' => ['type' => 'hidden'],
        'desde' => ['type' => 'date'],
        'hasta' => ['type' => 'date'],
    ];
    
    public function __construct()
    {
        $this->asociacion = 3;
        $this->horas_semanales = 20;
        $this->horas = 600;
    }
    
    
    
    public function getIdColaboracionOptions(){
        $cicloC = Grupo::select('idCiclo')->QTutor(AuthUser()->dni)->get();
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
