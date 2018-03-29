<?php

namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Model;
use Intranet\Entities\Centro;
use Intranet\Entities\Colaboracion;
use Intranet\Entities\Grupo;
use Jenssegers\Date\Date;
use Intranet\Events\ActivityReport;

class Fct extends Model
{
    
    protected $table = 'instructor_fcts';
    public $timestamps = false;

    protected $fillable = ['idFct', 'idInstructor', 'horas','descripcion','certificado'];
    protected $rules = [
        'idFct' => 'required',
        'idInstructor' => 'required|max:10',
    ];
    
    protected $dispatchesEvents = [
        'saved' => ActivityReport::class,
        'deleted' => ActivityReport::class,
    ];
    
    public function __construct()
    {
        $this->certificado = 1;
    }
    
}
