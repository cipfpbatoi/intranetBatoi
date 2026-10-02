<?php

declare(strict_types=1);

namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Document privat adjunt a una petició individual de convalidació. */
class DocumentConvalidacio extends Model
{
    protected $table = 'documents_convalidacions';

    protected $fillable = [
        'convalidacio_id', 'descripcio', 'path', 'original_name', 'mime',
    ];

    /** Petició individual propietària de l'adjunt. */
    public function convalidacio(): BelongsTo
    {
        return $this->belongsTo(Convalidacio::class, 'convalidacio_id');
    }
}
