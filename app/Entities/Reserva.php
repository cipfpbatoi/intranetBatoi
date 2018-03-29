<?php

namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Model;

class Reserva extends Model
{
    public $timestamps = false;
    
    protected $fillable = [
        'idProfesor',
        'dia',
        'hora',
        'idEspacio',
        'observaciones'
    ];
    
    public function Profesor()
    {
        return $this->belongsTo(Profesor::class, 'idProfesor', 'dni');
    }
}
