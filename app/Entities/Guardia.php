<?php

namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Model;
use Intranet\Events\ActivityReport;

class Guardia extends Model
{

    use \Intranet\Entities\Concerns\BatoiModels;

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

    public function scopeProfesor($query, $idProfesor)
    {
        return $query->where('idProfesor', $idProfesor);
    }

    public function scopeDiaHora($query, $dia, $hora)
    {
        return $query->where('dia', $dia)->where('hora', $hora);
    }

}
