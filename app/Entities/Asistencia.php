<?php

namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Model;

/**
 * Assistència d'un professor a una reunió.
 */
class Asistencia extends Model
{

    protected $table = 'asistencias';
    public $timestamps = false;
    protected $fillable = [
        'idReunion',
        'idProfesor',
    ];

    public function Profesor()
    {
        return $this->belongsTo(Profesor::class, 'idProfesor', 'dni');
    }

    /**
     * Retorna la reunió a la qual pertany l'assistència.
     */
    public function Reunion()
    {
        return $this->belongsTo(Reunion::class, 'idReunion', 'id');
    }
}
