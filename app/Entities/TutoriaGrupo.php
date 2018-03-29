<?php

namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Model;
use Jenssegers\Date\Date;
use Intranet\Events\ActivityReport;

class TutoriaGrupo extends Model
{

    use BatoiModels;

    protected $table = 'tutorias_grupos';
    public $timestamps = false;
    protected $fillable = [
        'idTutoria',
        'idGrupo',
        'observaciones',
        'fecha',
    ];
    protected $rules = [
        'idTutoria' => 'required',
        'idGrupo' => 'required',
        'fecha' => 'required|date',
        ];
    protected $inputTypes = [
        'idTutoria' => ['disabled' => 'disabled'],
        'idGrupo' => ['disabled' => 'disabled'],
        'observaciones' => ['type' => 'textarea'],
        'fecha' => ['type' => 'date'],
    ];
    protected $dispatchesEvents = [
        'saved' => ActivityReport::class,
        'deleted' => ActivityReport::class,
    ];
    
    public function getFechaAttribute($entrada)
    {
        $fecha = new Date($entrada);
        return $fecha->format('d-m-Y');
    }
    public function Grupo()
    {
        return $this->belongsTo(Grupo::class, 'idGrupo', 'codigo');
    }

}
