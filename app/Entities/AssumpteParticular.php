<?php

declare(strict_types=1);

namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Petició de permís retribuït per assumptes particulars.
 */
class AssumpteParticular extends Model
{
    public const ESTAT_PENDENT = 'pendent';
    public const ESTAT_AUTORITZADA = 'autoritzada';
    public const ESTAT_DENEGADA = 'denegada';
    public const ESTAT_CANCEL_LADA = 'cancel_lada';

    public const ORIGEN_SOLLICITUD = 'sollicitud';
    public const ORIGEN_REGULARITZACIO = 'regularitzacio';

    public const TIPUS_LECTIU = 'lectiu';
    public const TIPUS_NO_LECTIU = 'no_lectiu';

    public const TORN_MATI = 'mati';
    public const TORN_VESPRADA = 'vesprada';
    public const TORN_AMBDOS = 'ambdos';
    public const TORN_SENSE_DOCENCIA = 'sense_docencia';

    protected $table = 'assumptes_particulars';

    protected $fillable = [
        'idProfesor',
        'data_gaudi',
        'curs',
        'tipus',
        'torn',
        'estat',
        'origen',
        'motivacio_excepcional',
        'pla_activitats',
        'resolucio',
        'sollicitada_at',
        'resolta_at',
        'cancel_lada_at',
        'resolta_per',
        'falta_id',
        'resolucio_document',
    ];

    protected $attributes = [
        'estat' => self::ESTAT_PENDENT,
        'origen' => self::ORIGEN_SOLLICITUD,
    ];

    protected $casts = [
        'data_gaudi' => 'date',
        'sollicitada_at' => 'datetime',
        'resolta_at' => 'datetime',
        'cancel_lada_at' => 'datetime',
    ];

    /**
     * Professor que demana el permís.
     */
    public function profesor(): BelongsTo
    {
        return $this->belongsTo(Profesor::class, 'idProfesor', 'dni');
    }

    /**
     * Persona de Direcció que resol la petició.
     */
    public function resolutor(): BelongsTo
    {
        return $this->belongsTo(Profesor::class, 'resolta_per', 'dni');
    }

    /**
     * Falta creada després de l'autorització, si ja existeix.
     */
    public function falta(): BelongsTo
    {
        return $this->belongsTo(Falta::class, 'falta_id');
    }

    /**
     * Indica si la petició encara es pot tramitar.
     */
    public function estaPendent(): bool
    {
        return $this->estat === self::ESTAT_PENDENT;
    }
}
