<?php

namespace Intranet\Entities;


class Dual extends Fct
{
    
    protected $table = 'fcts';
    
    protected $fillable = ['idAlumno', 'idColaboracion','idInstructor','desde','hasta'
        ,'horas','asociacion','beca'];
    protected $notFillable = ['desde','hasta','idAlumno','horas','beca'];
    protected $inputTypes = [
        'idAlumno' => ['type' => 'select'],
        'idColaboracion' => ['type' => 'select'],
        'idInstructor' => ['type' => 'select'],
        'asociacion' => ['type' => 'hidden'],
        'desde' => ['type' => 'date'],
        'hasta' => ['type' => 'date'],
    ];
    protected $rules = [
        'idAlumno' => 'sometimes|required',
        'idColaboracion' => 'sometimes|required',
        'idInstructor' => 'sometimes|required',
        'desde' => 'sometimes|required|date',
        'hasta' => 'sometimes|required|date',
        'beca' => 'numeric'
    ];
    protected $attributes = ['asociacion'=>4,'correoInstructor'=>1];
    

    public function getIdAlumnoOptions()
    {
        return hazArray(
            Alumno::misAlumnos(authUser()->dni, true)
                ->orderBy('apellido1')
                ->orderBy('apellido2')
                ->get(),
            'nia',
            ['nameFull']
        );
    }
    
    public function getIdColaboracionOptions()
    {
        $cicloC = Grupo::select('idCiclo')->QTutor(authUser()->dni, true)->get();
        $ciclo = $cicloC->count()>0?$cicloC->first()->idCiclo:'';
        $colaboraciones = Colaboracion::where('idCiclo', $ciclo)->get();
        $todos = [];
        
        foreach ($colaboraciones as $colaboracion) {
            $todos[$colaboracion->id] = $colaboracion->Centro->nombre;
            if ($colaboracion->Centro->direccion) {
                $todos[$colaboracion->id].=' ('.$colaboracion->Centro->direccion.')';
            }
        }

        asort($todos);
        return $todos;
    }
        
}
