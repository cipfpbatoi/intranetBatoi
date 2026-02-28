<?php

namespace Intranet\OpenApi;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'GenericResource',
    type: 'object',
    properties: [
        new OA\Property(property: 'id', type: 'integer', nullable: true, example: 1),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time', nullable: true),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time', nullable: true),
    ],
    additionalProperties: true
)]
#[OA\Schema(
    schema: 'GenericUpsertRequest',
    type: 'object',
    additionalProperties: true
)]
#[OA\Schema(
    schema: 'GenericItemResponse',
    type: 'object',
    required: ['success', 'data'],
    properties: [
        new OA\Property(property: 'success', type: 'boolean', example: true),
        new OA\Property(property: 'data', ref: '#/components/schemas/GenericResource'),
    ]
)]
#[OA\Schema(
    schema: 'GenericCollectionResponse',
    type: 'object',
    required: ['success', 'data'],
    properties: [
        new OA\Property(property: 'success', type: 'boolean', example: true),
        new OA\Property(
            property: 'data',
            type: 'array',
            items: new OA\Items(ref: '#/components/schemas/GenericResource')
        ),
    ]
)]
#[OA\Schema(
    schema: 'ProfesorResource',
    type: 'object',
    properties: [
        new OA\Property(property: 'dni', type: 'string', example: '12345678A'),
        new OA\Property(property: 'nombre', type: 'string', example: 'Maria'),
        new OA\Property(property: 'apellido1', type: 'string', example: 'Garcia'),
        new OA\Property(property: 'apellido2', type: 'string', nullable: true, example: 'Perez'),
        new OA\Property(property: 'email', type: 'string', nullable: true, example: 'maria@example.com'),
        new OA\Property(property: 'rol', type: 'string', nullable: true, example: 'professor'),
    ],
    additionalProperties: true
)]
#[OA\Schema(
    schema: 'ProfesorUpsertRequest',
    type: 'object',
    properties: [
        new OA\Property(property: 'dni', type: 'string', example: '12345678A'),
        new OA\Property(property: 'nombre', type: 'string', example: 'Maria'),
        new OA\Property(property: 'apellido1', type: 'string', example: 'Garcia'),
        new OA\Property(property: 'apellido2', type: 'string', nullable: true, example: 'Perez'),
        new OA\Property(property: 'email', type: 'string', nullable: true, example: 'maria@example.com'),
        new OA\Property(property: 'rol', type: 'string', nullable: true, example: 'professor'),
    ],
    additionalProperties: true
)]
#[OA\Schema(
    schema: 'ProfesorItemResponse',
    type: 'object',
    required: ['success', 'data'],
    properties: [
        new OA\Property(property: 'success', type: 'boolean', example: true),
        new OA\Property(property: 'data', ref: '#/components/schemas/ProfesorResource'),
    ]
)]
#[OA\Schema(
    schema: 'ProfesorCollectionResponse',
    type: 'object',
    required: ['success', 'data'],
    properties: [
        new OA\Property(property: 'success', type: 'boolean', example: true),
        new OA\Property(
            property: 'data',
            type: 'array',
            items: new OA\Items(ref: '#/components/schemas/ProfesorResource')
        ),
    ]
)]
#[OA\Schema(
    schema: 'MaterialResource',
    type: 'object',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 12),
        new OA\Property(property: 'descripcion', type: 'string', nullable: true, example: 'Portatil Dell'),
        new OA\Property(property: 'unidades', type: 'integer', nullable: true, example: 4),
        new OA\Property(property: 'estado', type: 'string', nullable: true, example: 'Disponible'),
        new OA\Property(property: 'inventariable', type: 'boolean', nullable: true, example: true),
    ],
    additionalProperties: true
)]
#[OA\Schema(
    schema: 'MaterialUpsertRequest',
    type: 'object',
    properties: [
        new OA\Property(property: 'descripcion', type: 'string', nullable: true),
        new OA\Property(property: 'unidades', type: 'integer', nullable: true),
        new OA\Property(property: 'estado', type: 'string', nullable: true),
        new OA\Property(property: 'inventariable', type: 'boolean', nullable: true),
    ],
    additionalProperties: true
)]
#[OA\Schema(
    schema: 'MaterialItemResponse',
    type: 'object',
    required: ['success', 'data'],
    properties: [
        new OA\Property(property: 'success', type: 'boolean', example: true),
        new OA\Property(property: 'data', ref: '#/components/schemas/MaterialResource'),
    ]
)]
#[OA\Schema(
    schema: 'MaterialCollectionResponse',
    type: 'object',
    required: ['success', 'data'],
    properties: [
        new OA\Property(property: 'success', type: 'boolean', example: true),
        new OA\Property(
            property: 'data',
            type: 'array',
            items: new OA\Items(ref: '#/components/schemas/MaterialResource')
        ),
    ]
)]
#[OA\Schema(
    schema: 'GuardiaResource',
    type: 'object',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'dia', type: 'string', nullable: true, example: '2026-02-28'),
        new OA\Property(property: 'hora', type: 'string', nullable: true, example: '10:00'),
        new OA\Property(property: 'profesor', type: 'string', nullable: true, example: '12345678A'),
    ],
    additionalProperties: true
)]
#[OA\Schema(
    schema: 'GuardiaUpsertRequest',
    type: 'object',
    properties: [
        new OA\Property(property: 'dia', type: 'string', nullable: true),
        new OA\Property(property: 'hora', type: 'string', nullable: true),
        new OA\Property(property: 'profesor', type: 'string', nullable: true),
    ],
    additionalProperties: true
)]
#[OA\Schema(
    schema: 'GuardiaItemResponse',
    type: 'object',
    required: ['success', 'data'],
    properties: [
        new OA\Property(property: 'success', type: 'boolean', example: true),
        new OA\Property(property: 'data', ref: '#/components/schemas/GuardiaResource'),
    ]
)]
#[OA\Schema(
    schema: 'GuardiaCollectionResponse',
    type: 'object',
    required: ['success', 'data'],
    example: [
        'success' => true,
        'data' => [
            ['id' => 1, 'dia' => '2026-03-02', 'hora' => '10:00', 'profesor' => '12345678A'],
            ['id' => 2, 'dia' => '2026-03-03', 'hora' => '11:00', 'profesor' => '87654321B'],
        ],
    ],
    properties: [
        new OA\Property(property: 'success', type: 'boolean', example: true),
        new OA\Property(
            property: 'data',
            type: 'array',
            items: new OA\Items(ref: '#/components/schemas/GuardiaResource')
        ),
    ]
)]
#[OA\Schema(
    schema: 'HorarioResource',
    type: 'object',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'profesor', type: 'string', nullable: true, example: '12345678A'),
        new OA\Property(property: 'dia', type: 'string', nullable: true, example: 'Dilluns'),
        new OA\Property(property: 'hora', type: 'string', nullable: true, example: '1'),
        new OA\Property(property: 'grupo', type: 'string', nullable: true, example: '2DAM'),
    ],
    additionalProperties: true
)]
#[OA\Schema(
    schema: 'HorarioUpsertRequest',
    type: 'object',
    properties: [
        new OA\Property(property: 'profesor', type: 'string', nullable: true),
        new OA\Property(property: 'dia', type: 'string', nullable: true),
        new OA\Property(property: 'hora', type: 'string', nullable: true),
        new OA\Property(property: 'grupo', type: 'string', nullable: true),
    ],
    additionalProperties: true
)]
#[OA\Schema(
    schema: 'HorarioItemResponse',
    type: 'object',
    required: ['success', 'data'],
    properties: [
        new OA\Property(property: 'success', type: 'boolean', example: true),
        new OA\Property(property: 'data', ref: '#/components/schemas/HorarioResource'),
    ]
)]
#[OA\Schema(
    schema: 'HorarioCollectionResponse',
    type: 'object',
    required: ['success', 'data'],
    properties: [
        new OA\Property(property: 'success', type: 'boolean', example: true),
        new OA\Property(
            property: 'data',
            type: 'array',
            items: new OA\Items(ref: '#/components/schemas/HorarioResource')
        ),
    ]
)]
#[OA\Schema(
    schema: 'AlumnoFctResource',
    type: 'object',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 55),
        new OA\Property(property: 'alumno', type: 'string', nullable: true, example: '20444555X'),
        new OA\Property(property: 'empresa', type: 'integer', nullable: true, example: 8),
        new OA\Property(property: 'grupo', type: 'string', nullable: true, example: '2DAW'),
        new OA\Property(property: 'estado', type: 'string', nullable: true, example: 'actiu'),
    ],
    additionalProperties: true
)]
#[OA\Schema(
    schema: 'AlumnoFctUpsertRequest',
    type: 'object',
    properties: [
        new OA\Property(property: 'alumno', type: 'string', nullable: true),
        new OA\Property(property: 'empresa', type: 'integer', nullable: true),
        new OA\Property(property: 'grupo', type: 'string', nullable: true),
        new OA\Property(property: 'estado', type: 'string', nullable: true),
    ],
    additionalProperties: true
)]
#[OA\Schema(
    schema: 'AlumnoFctItemResponse',
    type: 'object',
    required: ['success', 'data'],
    properties: [
        new OA\Property(property: 'success', type: 'boolean', example: true),
        new OA\Property(property: 'data', ref: '#/components/schemas/AlumnoFctResource'),
    ]
)]
#[OA\Schema(
    schema: 'AlumnoFctCollectionResponse',
    type: 'object',
    required: ['success', 'data'],
    properties: [
        new OA\Property(property: 'success', type: 'boolean', example: true),
        new OA\Property(
            property: 'data',
            type: 'array',
            items: new OA\Items(ref: '#/components/schemas/AlumnoFctResource')
        ),
    ]
)]
#[OA\Schema(
    schema: 'ComisionResource',
    type: 'object',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'dni', type: 'string', nullable: true, example: '12345678A'),
        new OA\Property(property: 'importe', type: 'number', format: 'float', nullable: true, example: 123.45),
        new OA\Property(property: 'estado', type: 'string', nullable: true, example: 'pendent'),
    ],
    additionalProperties: true
)]
#[OA\Schema(
    schema: 'ComisionUpsertRequest',
    type: 'object',
    properties: [
        new OA\Property(property: 'dni', type: 'string', nullable: true),
        new OA\Property(property: 'importe', type: 'number', format: 'float', nullable: true),
        new OA\Property(property: 'estado', type: 'string', nullable: true),
    ],
    additionalProperties: true
)]
#[OA\Schema(
    schema: 'ComisionItemResponse',
    type: 'object',
    required: ['success', 'data'],
    properties: [
        new OA\Property(property: 'success', type: 'boolean', example: true),
        new OA\Property(property: 'data', ref: '#/components/schemas/ComisionResource'),
    ]
)]
#[OA\Schema(
    schema: 'ComisionCollectionResponse',
    type: 'object',
    required: ['success', 'data'],
    properties: [
        new OA\Property(property: 'success', type: 'boolean', example: true),
        new OA\Property(
            property: 'data',
            type: 'array',
            items: new OA\Items(ref: '#/components/schemas/ComisionResource')
        ),
    ]
)]
#[OA\Schema(
    schema: 'MaterialCambiarUbicacionRequest',
    type: 'object',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 12),
        new OA\Property(property: 'espacio', type: 'string', example: 'AULA-101'),
    ],
    additionalProperties: true
)]
#[OA\Schema(
    schema: 'MaterialCambiarEstadoRequest',
    type: 'object',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 12),
        new OA\Property(property: 'estado', type: 'string', example: 'Averiat'),
    ],
    additionalProperties: true
)]
#[OA\Schema(
    schema: 'MaterialCambiarUnidadRequest',
    type: 'object',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 12),
        new OA\Property(property: 'unidades', type: 'integer', example: 5),
    ],
    additionalProperties: true
)]
#[OA\Schema(
    schema: 'MaterialCambiarInventarioRequest',
    type: 'object',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 12),
        new OA\Property(property: 'inventariable', type: 'boolean', example: true),
        new OA\Property(property: 'inventario', type: 'string', nullable: true, example: 'INV-2026-0012'),
    ],
    additionalProperties: true
)]
#[OA\Schema(
    schema: 'HorarioChangeRequest',
    type: 'object',
    example: [
        'dia' => 'Dilluns',
        'hora' => '3',
        'aula' => 'A-12',
        'motiu' => 'Canvi puntual',
    ],
    properties: [
        new OA\Property(property: 'dia', type: 'string', example: 'Dilluns'),
        new OA\Property(property: 'hora', type: 'string', example: '3'),
        new OA\Property(property: 'aula', type: 'string', nullable: true, example: 'A-12'),
        new OA\Property(property: 'motiu', type: 'string', nullable: true, example: 'Canvi puntual'),
    ],
    additionalProperties: true
)]
#[OA\Schema(
    schema: 'ColaboracionTelefonicoRequest',
    type: 'object',
    properties: [
        new OA\Property(property: 'telefon', type: 'string', example: '600112233'),
        new OA\Property(property: 'observacions', type: 'string', nullable: true, example: 'Trucada de seguiment'),
    ],
    additionalProperties: true
)]
#[OA\Schema(
    schema: 'ColaboracionBookRequest',
    type: 'object',
    properties: [
        new OA\Property(property: 'data', type: 'string', format: 'date', example: '2026-03-10'),
        new OA\Property(property: 'hora', type: 'string', example: '11:00'),
        new OA\Property(property: 'lloc', type: 'string', nullable: true, example: 'Empresa'),
        new OA\Property(property: 'observacions', type: 'string', nullable: true, example: 'Visita de seguiment'),
    ],
    additionalProperties: true
)]
#[OA\Schema(
    schema: 'CentroFusionarRequest',
    type: 'object',
    required: ['fusion'],
    properties: [
        new OA\Property(
            property: 'fusion',
            type: 'array',
            minItems: 2,
            items: new OA\Items(type: 'integer'),
            example: [12, 27, 35]
        ),
    ]
)]
#[OA\Schema(
    schema: 'PresenciaResumenDay',
    type: 'object',
    properties: [
        new OA\Property(property: 'status', type: 'string', nullable: true, example: 'present'),
        new OA\Property(property: 'planned_docencia_minutes', type: 'integer', nullable: true, example: 300),
        new OA\Property(property: 'planned_altres_minutes', type: 'integer', nullable: true, example: 60),
        new OA\Property(property: 'covered_docencia_minutes', type: 'integer', nullable: true, example: 280),
        new OA\Property(property: 'covered_altres_minutes', type: 'integer', nullable: true, example: 55),
        new OA\Property(property: 'in_center_minutes', type: 'integer', nullable: true, example: 360),
        new OA\Property(property: 'has_open_stay', type: 'boolean', nullable: true, example: false),
        new OA\Property(property: 'first_entry', type: 'string', nullable: true, example: '08:05:00'),
    ],
    additionalProperties: true
)]
#[OA\Schema(
    schema: 'PresenciaResumenProfesor',
    type: 'object',
    properties: [
        new OA\Property(property: 'dni', type: 'string', example: '12345678A'),
        new OA\Property(property: 'nombre', type: 'string', example: 'Maria'),
        new OA\Property(property: 'apellido1', type: 'string', nullable: true, example: 'Garcia'),
        new OA\Property(property: 'apellido2', type: 'string', nullable: true, example: 'Perez'),
        new OA\Property(property: 'departamento', type: 'string', nullable: true, example: 'INF'),
        new OA\Property(
            property: 'days',
            type: 'object',
            additionalProperties: new OA\AdditionalProperties(ref: '#/components/schemas/PresenciaResumenDay')
        ),
    ],
    additionalProperties: true
)]
#[OA\Schema(
    schema: 'PresenciaResumenRangoResponse',
    type: 'array',
    items: new OA\Items(ref: '#/components/schemas/PresenciaResumenProfesor'),
    example: [
        [
            'dni' => '12345678A',
            'nombre' => 'Maria',
            'apellido1' => 'Garcia',
            'apellido2' => 'Perez',
            'departamento' => 'INF',
            'days' => [
                '2026-03-01' => [
                    'status' => 'present',
                    'planned_docencia_minutes' => 300,
                    'planned_altres_minutes' => 60,
                    'covered_docencia_minutes' => 280,
                    'covered_altres_minutes' => 55,
                    'in_center_minutes' => 360,
                    'has_open_stay' => false,
                    'first_entry' => '08:05:00',
                ],
            ],
        ],
    ]
)]
#[OA\Schema(
    schema: 'GrupoListItem',
    type: 'object',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 101),
        new OA\Property(property: 'texto', type: 'string', example: 'Cognom Nom'),
        new OA\Property(property: 'marked', type: 'integer', example: 1),
    ]
)]
#[OA\Schema(
    schema: 'GrupoListResponse',
    type: 'object',
    example: [
        'data' => [
            ['id' => 101, 'texto' => 'Garcia Pons Maria', 'marked' => 1],
            ['id' => 102, 'texto' => 'Perez Sanchis Joan', 'marked' => 1],
        ],
    ],
    properties: [
        new OA\Property(
            property: 'data',
            type: 'array',
            items: new OA\Items(ref: '#/components/schemas/GrupoListItem')
        ),
    ]
)]
#[OA\Schema(
    schema: 'HorariosDiaResponse',
    type: 'object',
    additionalProperties: new OA\AdditionalProperties(type: 'string'),
    example: ['12345678A' => '08:00 - 14:00', '87654321B' => '09:00 - 13:00']
)]
#[OA\Schema(
    schema: 'MaterialEspacioResponse',
    type: 'array',
    items: new OA\Items(ref: '#/components/schemas/MaterialResource'),
    example: [
        ['id' => 12, 'descripcion' => 'Portatil Dell', 'unidades' => 2, 'estado' => 'Disponible', 'inventariable' => true],
        ['id' => 13, 'descripcion' => 'Projector Epson', 'unidades' => 1, 'estado' => 'Prestec', 'inventariable' => true],
    ]
)]
#[OA\Schema(
    schema: 'ItacaPotencialItem',
    type: 'object',
    properties: [
        new OA\Property(property: 'idProfesor', type: 'string', example: '12345678A'),
        new OA\Property(property: 'desde', type: 'string', nullable: true, example: '10:00:00'),
        new OA\Property(property: 'hasta', type: 'string', nullable: true, example: '11:00:00'),
        new OA\Property(property: 'idGrupo', type: 'string', nullable: true, example: '2DAM'),
        new OA\Property(property: 'sesion_orden', type: 'integer', nullable: true, example: 3),
        new OA\Property(property: 'dia', type: 'string', format: 'date', example: '2026-03-02'),
        new OA\Property(property: 'checked', type: 'boolean', example: false),
        new OA\Property(property: 'justificacion', type: 'string', nullable: true, example: ''),
        new OA\Property(property: 'enCentro', type: 'boolean', example: true),
        new OA\Property(property: 'estado', type: 'integer', example: 0),
    ],
    additionalProperties: true
)]
#[OA\Schema(
    schema: 'ItacaPotencialResponse',
    type: 'array',
    items: new OA\Items(ref: '#/components/schemas/ItacaPotencialItem'),
    example: [
        [
            'idProfesor' => '12345678A',
            'desde' => '10:00:00',
            'hasta' => '11:00:00',
            'idGrupo' => '2DAM',
            'sesion_orden' => 3,
            'dia' => '2026-03-02',
            'checked' => false,
            'justificacion' => '',
            'enCentro' => true,
            'estado' => 0,
        ],
    ]
)]
#[OA\Schema(
    schema: 'ItacaGuardarItem',
    type: 'object',
    properties: [
        new OA\Property(property: 'idProfesor', type: 'string', example: '12345678A'),
        new OA\Property(property: 'sesion_orden', type: 'integer', example: 3),
        new OA\Property(property: 'dia', type: 'string', format: 'date', example: '2026-03-02'),
        new OA\Property(property: 'idGrupo', type: 'string', nullable: true, example: '2DAM'),
        new OA\Property(property: 'enCentro', type: 'boolean', example: true),
        new OA\Property(property: 'justificacion', type: 'string', nullable: true, example: 'Sortida medica'),
        new OA\Property(property: 'checked', type: 'boolean', example: true),
    ],
    additionalProperties: true
)]
#[OA\Schema(
    schema: 'ItacaGuardarRequest',
    type: 'array',
    items: new OA\Items(ref: '#/components/schemas/ItacaGuardarItem'),
    example: [
        [
            'idProfesor' => '12345678A',
            'sesion_orden' => 3,
            'dia' => '2026-03-02',
            'idGrupo' => '2DAM',
            'enCentro' => true,
            'justificacion' => 'Sortida medica',
            'checked' => true,
        ],
    ]
)]
#[OA\Schema(
    schema: 'ItacaGuardarResponse',
    type: 'object',
    additionalProperties: new OA\AdditionalProperties(type: 'integer'),
    example: ['1' => 0, '2' => 1]
)]
#[OA\Schema(
    schema: 'MiIpResponse',
    type: 'object',
    required: ['success', 'data'],
    properties: [
        new OA\Property(property: 'success', type: 'boolean', example: true),
        new OA\Property(property: 'data', type: 'string', example: '192.168.1.20'),
    ]
)]
#[OA\Schema(
    schema: 'ServerTimeResponse',
    type: 'object',
    required: ['date', 'time'],
    properties: [
        new OA\Property(property: 'date', type: 'string', format: 'date', example: '2026-03-01'),
        new OA\Property(property: 'time', type: 'string', example: '10:34:12'),
    ]
)]
/**
 * Esquemes de domini reutilitzables per a la documentacio OpenAPI.
 */
class ApiDomainSchemas
{
}
