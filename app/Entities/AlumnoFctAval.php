<?php

namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Model;
use Jenssegers\Date\Date;
use Intranet\Entities\AlumnoFct;
use Intranet\Entities\Alumno;

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
         return $query->where('actas','<', 2);
     }
     public function scopePendiente($query)
     {
         return $query->where('actas','=', 3);
     }
     public function scopeAval($query)
     {
         return $query->where('actas','=', 2);
     }
     public function scopePendienteNotificar($query)
     {
         return $query->whereNotNull('calificacion')->where('correoAlumno');
     }
     
 
}
