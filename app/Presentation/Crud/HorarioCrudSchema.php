<?php

declare(strict_types=1);

namespace Intranet\Presentation\Crud;

/**
 * Esquema de configuració CRUD per a Horario.
 *
 * Centralitza metadades de presentació (grid/form) fora del model Eloquent.
 */
final class HorarioCrudSchema
{
    /**
     * Camps visibles en el grid principal.
     *
     * @var array<int, string>
     */
    public const GRID_FIELDS = [
        'XModulo',
        'XOcupacion',
        'dia_semana',
        'desde',
        'aula',
    ];

    /**
     * Configuració de formulari per al CRUD d'horari.
     *
     * @var array<string, array<string, mixed>>
     */
    public const FORM_FIELDS = [
        'idProfesor' => ['type' => 'text', 'disabled' => 'disabled'],
        'modulo' => ['type' => 'select'],
        'idGrupo' => ['type' => 'select'],
        'ocupacion' => ['type' => 'select'],
        'aula' => ['type' => 'text'],
        'dia_semana' => ['type' => 'text'],
        'sesion_orden' => ['type' => 'text'],
        'plantilla' => ['type' => 'text'],
    ];
}

