<?php

namespace Intranet\Entities;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cotxe extends Model
{
    use BatoiModels;

    protected $fillable = ['matricula', 'marca'    ];

    public function professor(): BelongsTo
    {
        return $this->belongsTo(Profesor::class, 'idProfesor', 'dni');
    }
}
