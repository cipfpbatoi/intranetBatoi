<?php

namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Model;
use Intranet\Entities\Centro;
use Intranet\Entities\Colaboracion;
use Intranet\Entities\Grupo;
use Illuminate\Support\Carbon;
use Intranet\Events\ActivityReport;
use Intranet\Entities\Alumno;
use Intranet\Entities\Fct;

/**
 * Model per a crear FCT fictícies de convalidació, exempció o renúncia/no realitzada.
 */
class FctConvalidacion extends Fct
{
    protected $fillable = [
        'idAlumno',
        'horas',
        'asociacion',
        'calificacion',
    ];
    protected $notFillable = ['idAlumno','horas', 'calificacion'];
    protected $rules = [
        'idAlumno' => 'required',
        'asociacion' => 'required',
        'calificacion' => 'required|in:2,5',
    ];
    
    protected $inputTypes = [
        'idAlumno' => ['type' => 'select'],
        'asociacion' => ['type' => 'hidden'],
        'calificacion' => ['type' => 'select'],
    ];
    protected $attributes=[
        'asociacion'=>2,
        'correoInstructor'=>1
    ];

    /**
     * Retorna les qualificacions disponibles per a FCT fictícies.
     *
     * @return array<int, string>
     */
    public function getCalificacionOptions(): array
    {
        return [
            2 => 'Convalidat/Exempt',
            5 => 'Renúncia / No realitzada',
        ];
    }
}
