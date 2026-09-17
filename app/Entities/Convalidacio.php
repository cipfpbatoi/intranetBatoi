<?php

declare(strict_types=1);

namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Intranet\Entities\Modulo;

/**
 * Sol·licitud de convalidació individual d'un mòdul.
 * Una Convalidacio pot ser per mateix centre, altre centre, escola d'idiomes, etc.
 */
class Convalidacio extends Model
{
    public const TIPUS_MATEIX_CENTRE = 'mateix_centre';
    public const TIPUS_ALTRE_CENTRE = 'altre_centre';
    public const TIPUS_ESCOOLA_IDIOMES = 'escola_idiomes';
    public const TIPUS_TITOL_UNIVERSITARI = 'titol_universitari';
    public const TIPUS_TITOL_FP = 'titol_fp';

    public const ESTAT_PENDENT = 'pendent';
    public const ESTAT_APROVAT = 'aprovat';
    public const ESTAT_REBUTJAT = 'rebutjat';

    protected $table = 'convalidacions';

    protected $fillable = [
        'sollicitud_convalidacio_id',
        'modulo_id',
        'tipus_convalidacio',
        'cicle_formatiu_cursat_id',
        'certificat_path',
        'certificat_autentic',
        'estat',
        'motiu_rebutj',
    ];

    protected $attributes = [
        'estat' => self::ESTAT_PENDENT,
    ];

    protected $casts = [
        'certificat_autentic' => 'boolean',
    ];

    /**
     * Sol·licitud de convalidació a la que pertany.
     */
    public function sollicitud(): BelongsTo
    {
        return $this->belongsTo(SollicitudConvalidacio::class, 'sollicitud_convalidacio_id', 'id');
    }

    /**
     * Mòdul a convalidar (opcional, pot ser null per casos 2-3).
     */
    public function modulo(): BelongsTo
    {
        return $this->belongsTo(Modulo::class, 'modulo_id', 'codigo');
    }

    /**
     * Cicle formatiu cursat (opcional, només per tipus 'mateix_centre').
     */
    public function cicleFormatiuCursat(): BelongsTo
    {
        return $this->belongsTo(CicleFormatiuCursat::class, 'cicle_formatiu_cursat_id', 'id');
    }

    /**
     * Indica si la convalidació encara es pot tramitar.
     */
    public function estaPendent(): bool
    {
        return $this->estat === self::ESTAT_PENDENT;
    }

    /**
     * Retorna les opcions de tipus de convalidació disponibles.
     *
     * @return array<string, string>
     */
    public function getTipusConvalidacioOptions(): array
    {
        return [
            self::TIPUS_MATEIX_CENTRE => 'Mateix centre (cicle cursat)',
            self::TIPUS_ALTRE_CENTRE => 'Altre centre (certificat)',
            self::TIPUS_ESCOOLA_IDIOMES => 'Escola d\'idiomes (certificat)',
            self::TIPUS_TITOL_UNIVERSITARI => 'Títol universitari',
            self::TIPUS_TITOL_FP => 'Títol FP1 o FP2',
        ];
    }

    /**
     * Retorna les opcions d'estat disponibles.
     *
     * @return array<string, string>
     */
    public function getEstatOptions(): array
    {
        return [
            self::ESTAT_PENDENT => 'Pendent',
            self::ESTAT_APROVAT => 'Aprovat',
            self::ESTAT_REBUTJAT => 'Rebutjat',
        ];
    }
}
