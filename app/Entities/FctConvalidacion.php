<?php

namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Model;
use Intranet\Entities\Centro;
use Intranet\Entities\Colaboracion;
use Intranet\Entities\Grupo;
use Jenssegers\Date\Date;
use Intranet\Events\ActivityReport;
use Intranet\Entities\Alumno;
use Intranet\Entities\Fct;

class FctConvalidacion extends Fct
{
    protected $fillable = [
        'idAlumno',
        'horas','asociacion',
        'correoAlumno','correoInstructor'];
    protected $rules = [
        'idAlumno' => 'required',
        'asociacion' => 'required',
    ];
    
    protected $inputTypes = [
        'idAlumno' => ['type' => 'select'],
        'asociacion' => ['type' => 'hidden'],
        'correoAlumno' => ['type' => 'hidden'],
        'correoInstructor' => ['type' => 'hidden'],
        
    ];
    
    public function __construct()
    {
        $this->asociacion = 2;
        $this->horas = 400;
        $this->correoAlumno = 1;
        $this->correoInstructor = 1;
    }
}