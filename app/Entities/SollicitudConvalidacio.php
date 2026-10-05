<?php

declare(strict_types=1);

namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Capçalera d'una sol·licitud de convalidació.
 */
class SollicitudConvalidacio extends Model
{
    protected $table = 'sollicituds_convalidacions';

    protected $fillable = [
        'alumno_id',
        'submission_token',
        'submitted_at',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
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
}
