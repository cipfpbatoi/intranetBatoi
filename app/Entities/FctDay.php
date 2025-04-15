<?php

namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Model;
use Intranet\Entities\Poll\Vote;
use Carbon\Carbon;
use Intranet\Events\ActivityReport;
use Intranet\Events\FctCreated;
use Illuminate\Support\Arr;


class FctDay extends Model
{
    use BatoiModels;

    protected $table = 'fct_days';

    protected $fillable = [
        'alumno_fct_id',
        'dia',
        'hores_previstes',
        'hores_realitzades',
        'descripcio',
    ];

    /**
     * Relació amb AlumnoFct (molts a un)
     */
    public function alumnoFct()
    {
        return $this->belongsTo(AlumnoFct::class, 'alumno_fct_id');
    }
    public function getHorariAttribute()
    {
        return $this->alumnoFct->Fct->Colaboracion->Centro->horarios ?? null;
    }

}
