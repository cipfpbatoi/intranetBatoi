<?php

namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Model;
use Jenssegers\Date\Date;
use Intranet\Events\ActivityReport;

class Guardia extends Model
{

    use BatoiModels;

    protected $fillable = ['idProfesor', 'dia', 'hora', 'realizada', 'observaciones', 'obs_personal'];
    protected $hidden = ['created_at', 'updated_at'];
    protected $rules = [
        'realizada' => 'integer',
        'dia' => 'date',
    ];
    protected $dispatchesEvents = [
        'saved' => ActivityReport::class,
        'deleted' => ActivityReport::class,
    ];
    
    public function Profesor()
    {
        return $this->belongsTo(Profesor::class, 'idProfesor', 'dni');
    }
    
}
