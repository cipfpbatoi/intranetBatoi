<?php

namespace Intranet\Entities;

use Intranet\Application\Grupo\GrupoService;

/**
 * @deprecated Model legacy de FP Dual.
 *
 * Mantingut temporalment per compatibilitat amb fluxos antics.
 * No afegir noves funcionalitats ni nous punts d'entrada.
 */
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
    protected $attributes = ['asociacion'=>3,'correoInstructor'=>1];
    

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
        $ciclo = app(GrupoService::class)->firstByTutor(authUser()->dni)?->idCiclo;
        if (!$ciclo) {
            return [];
        }
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
