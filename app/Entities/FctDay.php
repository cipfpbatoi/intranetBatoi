<?php

namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Model;
use Intranet\Entities\Poll\Vote;
use Jenssegers\Date\Date;
use Intranet\Events\ActivityReport;
use Intranet\Events\FctCreated;
use Illuminate\Support\Arr;


class FctDay extends Model
{
    use \Intranet\Entities\Concerns\BatoiModels;

    protected $table = 'fct_days';

    protected $fillable = [
        'nia',
        'colaboracion_id',
        'dia',
        'hores_previstes',
        'hores_realitzades',
        'descripcio',
    ];

    protected $casts = [
        'colaboracion_id' => 'integer',
        'hores_previstes' => 'float',
        'hores_realitzades' => 'float',
    ];

    
    public function getHorariAttribute()
    {
        return $this->alumnoFct->Fct->Colaboracion->Centro->horarios ?? null;
    }

    /**
     * Normalitza valors buits perquè la BBDD no reba '' en una FK integer nullable.
     */
    public function setColaboracionIdAttribute($value): void
    {
        $this->attributes['colaboracion_id'] = ($value === '' || $value === null) ? null : (int) $value;
    }

}
