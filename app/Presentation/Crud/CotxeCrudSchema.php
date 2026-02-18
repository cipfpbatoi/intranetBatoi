<?php

declare(strict_types=1);

namespace Intranet\Presentation\Crud;

use Illuminate\Validation\Rule;

/**
 * Esquema de configuració CRUD per a Cotxe.
 */
final class CotxeCrudSchema
{
    /**
     * Camps visibles en el grid principal.
     *
     * @var array<int, string>
     */
    public const GRID_FIELDS = [
        'matricula',
        'marca',
    ];

    /**
     * Configuració de formulari modal.
     *
     * @var array<string, array<string, mixed>>
     */
    public const FORM_FIELDS = [
        'matricula' => ['type' => 'text'],
        'marca' => ['type' => 'text'],
    ];

    /**
     * Regles de validació del formulari de cotxe.
     *
     * @return array<string, mixed>
     */
    public static function requestRules(string|int|null $cotxeId, string $dni): array
    {
        return [
            'matricula' => [
                'required',
                'string',
                'max:8',
                Rule::unique('cotxes')
                    ->ignore($cotxeId)
                    ->where(static function ($query) use ($dni) {
                        return $query->where('idProfesor', $dni);
                    }),
            ],
            'marca' => 'required|string|max:80',
        ];
    }
}

