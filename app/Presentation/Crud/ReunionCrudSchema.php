<?php

declare(strict_types=1);

namespace Intranet\Presentation\Crud;

/**
 * Esquema de configuracio CRUD per a Reunion.
 */
final class ReunionCrudSchema
{
    /**
     * Camps visibles en el grid principal.
     *
     * @var array<int, string>
     */
    public const GRID_FIELDS = [
        'XGrupo',
        'XTipo',
        'Xnumero',
        'descripcion',
        'fecha',
        'curso',
        'id',
    ];

    /**
     * Configuracio de formulari create/edit.
     *
     * @var array<string, array<string, mixed>>
     */
    public const FORM_FIELDS = [
        'idProfesor' => ['type' => 'hidden'],
        'numero' => ['type' => 'select'],
        'tipo' => ['type' => 'select'],
        'grupo' => ['type' => 'select'],
        'curso' => ['disabled' => 'disabled'],
        'fecha' => ['type' => 'datetime'],
        'objetivos' => ['type' => 'textarea'],
        'idEspacio' => ['type' => 'select'],
        'fichero' => ['type' => 'file'],
    ];

    /**
     * Tipus de camp legacy usats per fillAll().
     *
     * @var array<string, array<string, mixed>>
     */
    public const INPUT_TYPES = self::FORM_FIELDS;

    /**
     * Regles de validacio.
     *
     * @var array<string, string>
     */
    public const RULES = [
        'tipo' => 'required',
        'curso' => 'required',
        'fecha' => 'required|date',
        'descripcion' => 'required|between:0,120',
        'idProfesor' => 'required',
        'idEspacio' => 'required',
    ];
}

