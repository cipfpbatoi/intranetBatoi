<?php

namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Model;


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

    public function Colaboracion()
    {
        return $this->belongsTo(Colaboracion::class, 'colaboracion_id', 'id');
    }

    
    public function getHorariAttribute()
    {
        return $this->Colaboracion?->Centro?->horarios ?? null;
    }

    /**
     * Normalitza valors buits perquè la BBDD no reba '' en una FK integer nullable.
     */
    public function setColaboracionIdAttribute($value): void
    {
        $this->attributes['colaboracion_id'] = ($value === '' || $value === null) ? null : (int) $value;
    }

}
