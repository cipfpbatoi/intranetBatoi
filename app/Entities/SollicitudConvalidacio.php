<?php

declare(strict_types=1);

namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Intranet\Entities\Alumno;

/**
 * Sol·licitud de convalidació de mòduls del cicle actual.
 * Una sol·licitud pot contenir varis mòduls (Convalidacio).
 */
class SollicitudConvalidacio extends Model
{
    public const ESTAT_PENDENT = 'pendent';
    public const ESTAT_APROVAT = 'aprovat';
    public const ESTAT_REBUTJAT = 'rebutjat';
    public const ESTAT_DOCUMENTS_REQUERITS = 'documents_requerits';

    protected $table = 'sollicituds_convalidacions';

    protected $fillable = [
        'alumno_id',
        'estat',
        'data_sol·licitud',
        'data_resolucio',
        'observacions',
    ];

    protected $attributes = [
        'estat' => self::ESTAT_PENDENT,
    ];

    protected $casts = [
        'data_sol·licitud' => 'datetime',
        'data_resolucio' => 'datetime',
    ];

    /**
     * Alumne que sol·licita la convalidació.
     */
    public function alumno(): BelongsTo
    {
        return $this->belongsTo(Alumno::class, 'alumno_id', 'nia');
    }

    /**
     * Convalidacions (mòduls) associats a esta sol·licitud.
     */
    public function convalidacions(): HasMany
    {
        return $this->hasMany(Convalidacio::class, 'sollicitud_convalidacio_id', 'id');
    }

    /**
     * Indica si la sol·licitud encara es pot tramitar.
     */
    public function estaPendent(): bool
    {
        return $this->estat === self::ESTAT_PENDENT;
    }
}
