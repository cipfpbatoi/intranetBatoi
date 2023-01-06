<?php

namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Model;
use Intranet\Entities\Alumno;
use Jenssegers\Date\Date;
use Intranet\Entities\AlumnoFct;

class AlumnoFctAval extends AlumnoFct
{

   
    
    protected $table = 'alumno_fcts';
    protected $fillable = ['id','idFct','idAlumno', 'calificacion','calProyecto'];
    
    public $timestamps = false;

    protected $rules = [
        'id' => 'required',
        'idAlumno' => 'required',
        'idFct' => 'required',
        'calificacion' => 'numeric',
        'calProyecto' => 'numeric',
        
    ];
    protected $inputTypes = [
        'id' => ['type' => 'hidden'],
        'idAlumno' => ['type' => 'hidden'],
        'idFct' => ['type' => 'hidden'],
        'calificacion' => ['type' => 'hidden'],
    ];
    public function scopeNoAval($query)
     {
         return $query->where('actas', '<', 2);
     }
     public function scopePendiente($query)
     {
         return $query->where('actas', '=', 3);
     }
     public function scopeAval($query)
     {
         return $query->where('actas', '=', 2);
     }
     public function scopePendienteNotificar($query, array $alumnos)
     {
         return $query->whereIn('idAlumno', $alumnos)->where('correoAlumno', false);
     }
     public function scopeCalificados($query)
     {
         return $query->whereNotNull('calificacion');
     }
    public function scopeAprobados($query)
    {
        return $query->where('calificacion', 1);
    }
    public function scopeTitulan($query)
    {
        return $query->where('calificacion', '>', 0)->where('calProyecto', '>', 4);
    }
     
     public function scopeMisFcts($query, $profesor= null)
     {
        $profesor = $profesor?$profesor:authUser()->dni;
        $alumnos = Alumno::select('nia')->misAlumnos($profesor)->get()->toArray();
        if (count($alumnos)) {
            $cicloC = Grupo::select('idCiclo')->QTutor($profesor)->first()->idCiclo;
            $colaboraciones = Colaboracion::select('id')->where('idCiclo', $cicloC)->get()->toArray();
            $fcts = Fct::select('id')->whereIn('idColaboracion', $colaboraciones)
                ->orWhere('asociacion', 2)
                ->orWhere('asociacion', 3)
                ->get()
                ->toArray();
            return $query->whereIn('idAlumno', $alumnos)->whereIn('idFct', $fcts);
        }
        return $query->whereIn('idAlumno', $alumnos);
    }

    public function scopeRealFcts($query, $profesor= null)
    {
        $profesor = $profesor?$profesor:authUser()->dni;
        $alumnos = Alumno::select('nia')->misAlumnos($profesor)->get()->toArray();
        if (count($alumnos)) {
            $cicloC = Grupo::select('idCiclo')->QTutor($profesor)->first()->idCiclo;
            $colaboraciones = Colaboracion::select('id')->where('idCiclo', $cicloC)->get()->toArray();
            $fcts = Fct::select('id')->whereIn('idColaboracion', $colaboraciones)
                ->orWhere('asociacion', 2)
                ->get()
                ->toArray();
            return $query->whereIn('idAlumno', $alumnos)->whereIn('idFct', $fcts);
        }
        return $query->whereIn('idAlumno', $alumnos);
    }

    public function scopeMisErasmus($query, $profesor= null)
    {
        $profesor = $profesor?$profesor:authUser()->dni;
        $alumnos = Alumno::select('nia')->misAlumnos($profesor)->get()->toArray();
        if (count($alumnos)) {
            $fcts = Fct::select('id')->where('asociacion', 2)->get()->toArray();
            return $query->whereIn('idAlumno', $alumnos)->whereIn('idFct', $fcts);
        }
        return $query->whereIn('idAlumno', $alumnos);
    }
 
}
