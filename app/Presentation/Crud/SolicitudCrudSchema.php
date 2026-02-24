<?php

declare(strict_types=1);

namespace Intranet\Presentation\Crud;

/**
 * Esquema de configuracio CRUD per a Solicitud.
 */
final class SolicitudCrudSchema
{
    /**
     * Camps visibles en el grid del professor.
     *
     * @var array<int, string>
     */
    public const GRID_FIELDS = [
        'id',
        'nomAlum',
        'fecha',
        'situacion',
    ];

    /**
     * Camps visibles en el grid d'orientacio.
     *
     * @var array<int, string>
     */
    public const ORIENTACION_GRID_FIELDS = [
        'id',
        'nomAlum',
        'fecha',
        'motiu',
        'situacion',
    ];

    /**
     * Tipus de camp legacy per al model.
     *
     * @var array<string, array<string, mixed>>
     */
    public const INPUT_TYPES = [
        'idAlumno' => ['type' => 'select'],
        'idProfesor' => ['type' => 'hidden'],
        'text1' => ['type' => 'textarea'],
        'text2' => ['type' => 'textarea'],
        'text3' => ['type' => 'textarea'],
        'idOrientador' => ['type' => 'select'],
        'fecha' => ['type' => 'date'],
    ];

    /**
     * Regles de validacio de request.
     *
     * @var array<string, string>
     */
    public const RULES = [
        'fecha' => 'required',
        'text1' => 'required',
        'idAlumno' => 'required',
        'idOrientador' => 'required',
    ];
}

