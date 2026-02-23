<?php

declare(strict_types=1);

namespace Intranet\Presentation\Crud;

/**
 * Esquema de configuracio CRUD per a TutoriaGrupo.
 */
final class TutoriaGrupoCrudSchema
{
    /**
     * Camps visibles en el grid principal.
     *
     * @var array<int, string>
     */
    public const GRID_FIELDS = [
        'Nombre',
        'observaciones',
        'fecha',
    ];

    /**
     * Tipus de camp legacy del model.
     *
     * @var array<string, array<string, mixed>>
     */
    public const INPUT_TYPES = [
        'idTutoria' => ['disabled' => 'disabled'],
        'idGrupo' => ['disabled' => 'disabled'],
        'observaciones' => ['type' => 'textarea'],
        'fecha' => ['type' => 'date'],
    ];

    /**
     * Regles de validacio.
     *
     * @var array<string, string>
     */
    public const RULES = [
        'idTutoria' => 'required',
        'idGrupo' => 'required',
        'fecha' => 'required|date',
        'observaciones' => 'required',
    ];
}

