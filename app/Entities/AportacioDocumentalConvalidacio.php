<?php

declare(strict_types=1);

namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Aportació addicional de documentació registrada pel personal del centre. */
class AportacioDocumentalConvalidacio extends Model
{
    protected $table = 'convalidacio_aportacions';

    protected $fillable = [
        'convalidacio_id', 'actor_dni', 'rols_actor', 'comentari', 'descripcio',
        'path', 'original_name', 'mime',
    ];

    protected $casts = [
        'rols_actor' => 'array',
    ];

    /** Petició a la qual s'afig l'aportació. */
    public function convalidacio(): BelongsTo
    {
        return $this->belongsTo(Convalidacio::class, 'convalidacio_id');
    }

    /** Persona que va registrar l'aportació. */
    public function actor(): BelongsTo
    {
        return $this->belongsTo(Profesor::class, 'actor_dni', 'dni');
    }
}
