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
        'correoInstructor'];
    protected $notfillable = ['horas'];
    protected $rules = [
        'idAlumno' => 'required',
        'asociacion' => 'required',
        'horas' => 'required|numeric'
    ];
    
    protected $inputTypes = [
        'idAlumno' => ['type' => 'select'],
        'asociacion' => ['type' => 'hidden'],
        'correoInstructor' => ['type' => 'hidden'],
        
    ];
    
    public function __construct()
    {
        $this->asociacion = 2;
        $this->horas = 400;
        $this->correoInstructor = 1;
        
    }
   
}