<?php

declare(strict_types=1);

namespace Intranet\Presentation\Crud;

use Illuminate\Validation\Rule;

/**
 * Esquema de configuració CRUD per a Empresa.
 */
final class EmpresaCrudSchema
{
    /**
     * Camps visibles al grid principal d'empreses.
     *
     * @var array<int, string>
     */
    public const GRID_FIELDS = [
        'concierto',
        'nombre',
        'direccion',
        'localidad',
        'telefono',
        'email',
        'cif',
        'actividad',
    ];

    /**
     * Configuració de camps del formulari CRUD.
     *
     * @var array<string, array<string, mixed>>
     */
    public const FORM_FIELDS = [
        'europa' => ['type' => 'checkbox'],
        'sao' => ['type' => 'checkbox'],
        'concierto' => ['type' => 'text'],
        'cif' => ['type' => 'text'],
        'nombre' => ['type' => 'text'],
        'email' => ['type' => 'email'],
        'direccion' => ['type' => 'text'],
        'localidad' => ['type' => 'text'],
        'telefono' => ['type' => 'number'],
        'dual' => ['type' => 'checkbox'],
        'actividad' => ['type' => 'text'],
        'delitos' => ['type' => 'checkbox'],
        'menores' => ['type' => 'checkbox'],
        'observaciones' => ['type' => 'textarea'],
        'gerente' => ['type' => 'text'],
        'fichero' => ['type' => 'file'],
        'creador' => ['type' => 'hidden'],
        'idSao' => ['type' => 'hidden'],
        'data_signatura' => ['type' => 'date'],
    ];

    /**
     * Regles de validació per al formulari d'empresa.
     *
     * @return array<string, mixed>
     */
    public static function requestRules(string|int|null $empresaId): array
    {
        return [
            'europa' => 'boolean',
            'sao' => 'boolean',
            'concierto' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('empresas', 'concierto')->ignore($empresaId),
            ],
            'cif' => [
                'required',
                'alpha_num',
                'max:20',
                Rule::unique('empresas', 'cif')->ignore($empresaId),
            ],
            'nombre' => 'required|string|max:100',
            'email' => 'nullable|email|max:255',
            'direccion' => 'required|string|max:100',
            'localidad' => 'required|string|max:30',
            'telefono' => 'required|string|max:20',
            'dual' => 'boolean',
            'actividad' => 'nullable|string|max:255',
            'delitos' => 'boolean',
            'menores' => 'boolean',
            'observaciones' => 'nullable|string',
            'gerente' => 'nullable|string|max:255',
            'fichero' => 'nullable|file|mimes:pdf,docx,xlsx,jpg,png,webp,heic,heif,zip',
            'creador' => 'nullable|string|max:12',
            'idSao' => 'nullable|integer',
            'data_signatura' => 'nullable|date',
        ];
    }
}

