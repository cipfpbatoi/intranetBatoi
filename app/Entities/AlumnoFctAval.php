<?php

namespace Intranet\Entities;

/**
 * @deprecated Model temporal per al CRUD històric d'avaluació de FCT.
 *
 * Es recomana migrar casos d'ús cap a AlumnoFctService.
 */
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
     public function scopePendienteNotificar($query )
     {
         return $query->where('calificacion',1)
             ->where('correoAlumno', 0);
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
         $fcts = Fct::select('id')->esDual()->pluck('id');
         return $query->whereIn('idProfesor', $profesor)->whereNotIn('idFct', $fcts);
    }

    public function scopeRealFcts($query, $profesor= null)
    {
        $profesor = Profesor::getSubstituts($profesor??authUser()->dni);
        return $query->whereIn('idProfesor', $profesor)->estaSao();
    }

    public function scopeAvaluables($query, $profesor= null)
    {
        $profesor = Profesor::getSubstituts($profesor??authUser()->dni);
        return $query->whereIn('idProfesor', $profesor)->esAval();
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
            static::where('idAlumno', $this->idAlumno)
            ->where('correoAlumno', 0)
            ->sum('horas');
    }
 
}
