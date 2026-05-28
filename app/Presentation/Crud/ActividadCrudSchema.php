<?php

declare(strict_types=1);

namespace Intranet\Presentation\Crud;

/**
 * Esquema de configuració CRUD per a Actividad.
 *
 * Centralitza metadades de presentació (grid/form) fora del model Eloquent.
 */
final class ActividadCrudSchema
{
    /**
     * Camps visibles en el grid general d'activitats.
     *
     * @var array<int, string>
     */
    public const GRID_FIELDS = [
        'name',
        'desde',
        'hasta',
        'situacion',
    ];

    /**
     * Camps visibles en el grid d'orientació.
     *
     * @var array<int, string>
     */
    public const ORIENTACION_GRID_FIELDS = [
        'name',
        'desde',
        'hasta',
    ];

    /**
     * Configuració de formulari modal per a activitats.
     *
     * @var array<string, array<string, mixed>>
     */
    public const FORM_FIELDS = [
        'id' => ['type' => 'hidden'],
        'tipo_actividad_id' => ['type' => 'select'],
        'name' => ['type' => 'text'],
        'desde' => ['type' => 'datetime'],
        'hasta' => ['type' => 'datetime'],
        'poll' => ['type' => 'hidden'],
        'complementaria' => ['type' => 'checkbox'],
        'fueraCentro' => ['type' => 'checkbox'],
        'transport' => ['type' => 'checkbox'],
        'descripcion' => ['type' => 'textarea'],
        'objetivos' => ['type' => 'textarea'],
        'extraescolar' => ['type' => 'hidden'],
        'comentarios' => ['type' => 'textarea'],
        'recomanada' => ['type' => 'hidden'],
    ];

    /**
     * Tipus de camp per a persistència/normalització en model legacy.
     *
     * `fillAll()` usa estos tipus per tractar checkbox/date/select.
     *
     * @var array<string, array<string, mixed>>
     */
    public const INPUT_TYPES = [
        'id' => ['type' => 'hidden'],
        'tipo_actividad_id' => ['type' => 'select'],
        'objetivos' => ['type' => 'textarea'],
        'extraescolar' => ['type' => 'hidden'],
        'descripcion' => ['type' => 'textarea'],
        'comentarios' => ['type' => 'textarea'],
        'desde' => ['type' => 'datetime'],
        'hasta' => ['type' => 'datetime'],
        'fueraCentro' => ['type' => 'checkbox'],
        'transport' => ['type' => 'checkbox'],
        'complementaria' => ['type' => 'checkbox'],
    ];
}
