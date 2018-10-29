<?php

namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Model;
use Intranet\Entities\Centro;
use Intranet\Entities\Colaboracion;
use Intranet\Entities\Grupo;
use Jenssegers\Date\Date;
use Intranet\Events\ActivityReport;
use Intranet\Entities\Alumno;

class Fct1 extends Model
{
    use BatoiModels;
    
    protected $table = 'fcts1';
    public $timestamps = false;

    
    
    public function Alumno()
    {
           return $this->belongsTo(Alumno::class, 'idAlumno', 'nia');
    }
    
    public function Colaboracion()
    {
        return $this->belongsTo(Colaboracion::class, 'idColaboracion', 'id');
    }
    
}
