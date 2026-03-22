<?php

declare(strict_types=1);

namespace Intranet\Presentation\Crud;

/**
 * Esquema de configuració CRUD per a Expediente.
 */
final class ExpedienteCrudSchema
{
    /**
     * Camps visibles en el grid d'expedients.
     *
     * @var array<int, string>
     */
    public const GRID_FIELDS = [
        'id',
        'nomAlum',
        'fecha',
        'Xtipo',
        'Xmodulo',
        'situacion',
    ];

    /**
     * Configuració de formulari modal.
     *
     * @var array<string, array<string, mixed>>
     */
    public const FORM_FIELDS = [
        'tipo' => ['type' => 'select'],
        'idModulo' => ['type' => 'select'],
        'idAlumno' => ['type' => 'select'],
        'idProfesor' => ['type' => 'hidden'],
        'explicacion' => ['type' => 'textarea'],
        'fecha' => ['type' => 'date'],
        'fechatramite' => ['type' => 'date'],
    ];

    /**
     * Tipus de camp per a persistència/normalització en model legacy.
     *
     * @var array<string, array<string, mixed>>
     */
    public const INPUT_TYPES = [
        'tipo' => ['type' => 'select'],
        'idModulo' => ['type' => 'select'],
        'idAlumno' => ['type' => 'select'],
        'idProfesor' => ['type' => 'hidden'],
        'explicacion' => ['type' => 'textarea'],
        'fecha' => ['type' => 'date'],
        'fechatramite' => ['type' => 'date'],
    ];
}

