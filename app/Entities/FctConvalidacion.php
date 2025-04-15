<?php

namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Model;
use Intranet\Entities\Centro;
use Intranet\Entities\Colaboracion;
use Intranet\Entities\Grupo;
use Carbon\Carbon;
use Intranet\Events\ActivityReport;
use Intranet\Entities\Alumno;
use Intranet\Entities\Fct;

class FctConvalidacion extends Fct
{
    protected $fillable = [
        'idAlumno',
        'horas',
        'asociacion',
    ];
    protected $notFillable = ['idAlumno','horas'];
    protected $rules = [
        'idAlumno' => 'required',
        'asociacion' => 'required',
    ];
    
    protected $inputTypes = [
        'idAlumno' => ['type' => 'select'],
        'asociacion' => ['type' => 'hidden'],
    ];
    protected $attributes=[
        'asociacion'=>2,
        'correoInstructor'=>1
    ];
}
