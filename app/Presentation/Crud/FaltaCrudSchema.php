<?php

declare(strict_types=1);

namespace Intranet\Presentation\Crud;

/**
 * Esquema de configuració CRUD per a Falta.
 */
final class FaltaCrudSchema
{
    /**
     * Camps visibles en el grid principal de FaltaController.
     *
     * @var array<int, string>
     */
    public const GRID_FIELDS = ['id', 'desde', 'hasta', 'motivo', 'situacion', 'observaciones'];

    /**
     * Camps visibles en el grid de PanelFaltaController.
     *
     * @var array<int, string>
     */
    public const PANEL_GRID_FIELDS = ['id', 'nombre', 'desde', 'hasta', 'motivo', 'situacion'];

    /**
     * Configuració de formulari per a panell/modal de faltes.
     *
     * @var array<string, array<string, mixed>>
     */
    public const FORM_FIELDS = [
        'idProfesor' => ['type' => 'select'],
        'estado' => ['type' => 'hidden'],
        'desde' => ['type' => 'date'],
        'hasta' => ['type' => 'date'],
        'baja' => ['type' => 'checkbox'],
        'dia_completo' => ['type' => 'checkbox'],
        'hora_ini' => ['type' => 'time'],
        'hora_fin' => ['type' => 'time'],
        'motivos' => ['type' => 'select'],
        'observaciones' => ['type' => 'text'],
        'fichero' => ['type' => 'file'],
    ];

    /**
     * Tipus d'input per a persistència/normalització en el model legacy.
     *
     * @var array<string, array<string, mixed>>
     */
    public const INPUT_TYPES = [
        'idProfesor' => ['type' => 'hidden'],
        'estado' => ['type' => 'hidden'],
        'desde' => ['type' => 'date'],
        'hasta' => ['type' => 'date'],
        'baja' => ['type' => 'hidden'],
        'dia_completo' => ['type' => 'checkbox'],
        'hora_ini' => ['type' => 'select'],
        'hora_fin' => ['type' => 'select'],
        'motivos' => ['type' => 'select'],
        'fecha' => ['type' => 'date'],
        'fichero' => ['type' => 'file'],
    ];

    /**
     * Regles de validació actuals per a Falta.
     *
     * @var array<string, string>
     */
    public const RULES = [
        'idProfesor' => 'required',
        'desde' => 'required|date',
        'hasta' => 'date',
        'motivos' => 'required',
        'observaciones' => 'max:200',
        'hora_ini' => 'required_if:dia_completo,0',
        'hora_fin' => 'required_if:dia_completo,0',
        'fichero' => 'mimes:pdf,jpg,jpeg,png',
    ];
}
