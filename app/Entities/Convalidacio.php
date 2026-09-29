<?php

declare(strict_types=1);

namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Petició individual d'una sol·licitud de convalidació.
 */
class Convalidacio extends Model
{
    public const ORIGEN_PROPI_CENTRE = 'propi_centre';
    public const ORIGEN_ALTRE_CENTRE = 'altre_centre';
    public const ORIGEN_EOI = 'certificat_eoi';
    public const ORIGEN_PRL_LOGSE = 'prl_logse';

    public const ESTAT_EN_PROCES = 'en_proces';
    public const ESTAT_REALITZADA = 'realitzada';
    public const ESTAT_REALITZADA_PENDENT_REVISIO = 'realitzada_pendent_revisio';
    public const ESTAT_DENEGADA = 'denegada';
    public const ESTAT_REVISAR_DOCUMENTACIO = 'revisar_documentacio';
    public const ESTAT_APORTAR_ORIGINAL = 'aportar_original_secretaria';

    protected $table = 'convalidacions';

    /** @var list<string> Camps editables i instantànies acadèmiques de la petició. */
    protected $fillable = [
        'sollicitud_convalidacio_id',
        'modulo_destino_id',
        'origen',
        'modulo_origen_codigo',
        'modulo_origen_nombre',
        'modulo_origen_nombre_val',
        'modulo_origen_nombre_cas',
        'ciclo_origen_codigo',
        'ciclo_origen_nombre',
        'ciclo_origen_nombre_val',
        'ciclo_origen_nombre_cas',
        'familia_professional_codigo',
        'familia_professional_nombre_val',
        'familia_professional_nombre_cas',
        'ciclo_matricula_id',
        'ciclo_matricula_codigo',
        'ciclo_matricula_nombre_val',
        'ciclo_matricula_nombre_cas',
        'departamento_matricula_id',
        'familia_matricula_nombre_val',
        'familia_matricula_nombre_cas',
        'familia_matricula_codigo_xml',
        'familia_matricula_abreviatura_xml',
        'ciclo_matricula_tipo',
        'ciclo_matricula_tipo_nombre_val',
        'ciclo_matricula_tipo_nombre_cas',
        'ciclo_matricula_normativa',
        'nivel_origen_codigo',
        'nivel_origen_nombre_val',
        'nivel_origen_nombre_cas',
        'any_origen',
        'nota_origen',
        'convocatoria_origen',
        'document_path',
        'document_original_name',
        'document_mime',
        'declaracio_responsable',
        'estat',
        'observacions',
        'revisat_per',
        'revisat_at',
    ];

    protected $attributes = [
        'estat' => self::ESTAT_EN_PROCES,
    ];

    protected $casts = [
        'declaracio_responsable' => 'boolean',
        'any_origen' => 'integer',
        'nota_origen' => 'float',
        'revisat_at' => 'datetime',
    ];

    /**
     * Sol·licitud de convalidació a la que pertany.
     */
    public function sollicitud(): BelongsTo
    {
        return $this->belongsTo(SollicitudConvalidacio::class, 'sollicitud_convalidacio_id', 'id');
    }

    /** Mòdul destí de la matrícula vigent. */
    public function moduloDestino(): BelongsTo
    {
        return $this->belongsTo(Modulo::class, 'modulo_destino_id', 'codigo');
    }

    /** Última persona de Direcció que ha revisat la petició. */
    public function revisor(): BelongsTo
    {
        return $this->belongsTo(Profesor::class, 'revisat_per', 'dni');
    }

    /**
     * Retorna les opcions de tipus de convalidació disponibles.
     *
     * @return array<string, string>
     */
    public static function origenOptions(): array
    {
        return [
            self::ORIGEN_PROPI_CENTRE => 'Estudis cursats al propi centre',
            self::ORIGEN_ALTRE_CENTRE => 'Estudis o certificats acadèmics d\'un altre centre',
            self::ORIGEN_EOI => 'Certificat d\'escola oficial d\'idiomes',
            self::ORIGEN_PRL_LOGSE => 'Prevenció de riscos (LOGSE)',
        ];
    }

    /**
     * Retorna les opcions d'estat disponibles.
     *
     * @return array<string, string>
     */
    public static function estatOptions(): array
    {
        return [
            self::ESTAT_EN_PROCES => 'En procés',
            self::ESTAT_REALITZADA => 'Realitzada',
            self::ESTAT_REALITZADA_PENDENT_REVISIO => 'Realitzada pendent de revisió',
            self::ESTAT_DENEGADA => 'Denegada',
            self::ESTAT_REVISAR_DOCUMENTACIO => 'Revisar documentació',
            self::ESTAT_APORTAR_ORIGINAL => 'Aportar documentació original a Secretaria',
        ];
    }

    /** Indica si l'origen necessita documentació. */
    public function esOrigenExtern(): bool
    {
        return $this->origen !== self::ORIGEN_PROPI_CENTRE;
    }

    /** Indica si la petició ja no admet cap canvi. */
    public function esTerminal(): bool
    {
        return $this->estat === self::ESTAT_REALITZADA;
    }
}
