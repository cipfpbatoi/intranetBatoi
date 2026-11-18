<?php

namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Model;

/**
 * Auditoria de lectures i resultats dels accessos al pàrquing.
 */
class CotxeAcces extends Model
{
    public $table = 'cotxe_accessos';

    /**
     * Camps persistibles de l'auditoria d'accessos al pàrquing.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'matricula', 'matricula_canonica', 'idProfesor', 'autoritzat',
        'porta_oberta', 'device', 'tipus', 'resultat_resolucio',
        'diferencia_ocr', 'intents_domotica', 'error_domotica',
    ];
}
