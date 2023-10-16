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
         return $query->whereIn('idAlumno', $alumnos)
             ->where('correoAlumno', false);
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
         $profesor = Profesor::getSubstituts($profesor??authUser()->dni);
         $fcts = Fct::select('id')->esDual()->get()->toArray();
         return $query->whereIn('idProfesor', $profesor)->whereNotIn('idFct', $fcts);
    }

    public function scopeRealFcts($query, $profesor= null)
    {
        $profesor = Profesor::getSubstituts($profesor??authUser()->dni);
        return $query->whereIn('idProfesor', $profesor)->esFct();
    }

    public function scopeMisErasmus($query, $profesor= null)
    {
        $profesor = Profesor::getSubstituts($profesor??authUser()->dni);
        return $query->whereIn('idProfesor', $profesor)->esErasmus();
    }

    public function getHorasTotalAttribute()
    {
        return $this->correoAlumno ?
            $this->horas :
            AlumnoFctAval::where('idAlumno', $this->idAlumno)
            ->where('correoAlumno', 0)
            ->sum('horas');
    }
 
}
