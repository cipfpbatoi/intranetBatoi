<?php

declare(strict_types=1);

namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Registre d'un cicle formatiu que l'alumne ha cursat anteriorment.
 * Pot ser un cicle actual o ja extint (FK opcional a cicles).
 */
class CicleFormatiuCursat extends Model
{
    protected $table = 'cicles_formatius_cursats';

    protected $fillable = [
        'alumno_id',
        'cicle_formatiu_id',
        'any_curs',
    ];

    protected $casts = [
        'any_curs' => 'integer',
    ];

    /**
     * Alumne que va cursar el cicle.
     */
    public function alumno(): BelongsTo
    {
        return $this->belongsTo(Alumno::class, 'alumno_id', 'nia');
    }

    /**
     * Cicle formatiu cursat (opcional, pot ser null si ja no existeix a la BD viva).
     */
    public function cicle(): BelongsTo
    {
        return $this->belongsTo(Ciclo::class, 'cicle_formatiu_id', 'id');
    }

    /**
     * Sol·licituds de convalidació associades a este cicle cursat.
     */
    public function sollicituds(): HasMany
    {
        return $this->hasMany(SollicitudConvalidacio::class, 'cicle_formatiu_cursat_id', 'id');
    }
}
