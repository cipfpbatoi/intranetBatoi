<?php

namespace Intranet\OpenApi;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: '1.0.0',
    title: 'Intranet Batoi API',
    description: 'Documentacio OpenAPI de les rutes API d\'Intranet Batoi.'
)]
#[OA\Server(
    url: '/api',
    description: 'Servidor API'
)]
#[OA\SecurityScheme(
    securityScheme: 'sanctum',
    type: 'http',
    scheme: 'bearer',
    bearerFormat: 'JWT',
    description: 'Token Bearer emes amb Sanctum.'
)]
#[OA\Tag(name: 'Auth', description: 'Autenticacio i gestio de token')]
#[OA\Schema(
    schema: 'ApiError',
    type: 'object',
    required: ['success', 'message'],
    properties: [
        new OA\Property(property: 'success', type: 'boolean', example: false),
        new OA\Property(property: 'message', type: 'string', example: 'Unauthorized'),
    ]
)]
#[OA\Schema(
    schema: 'ApiSuccess',
    type: 'object',
    required: ['success', 'data'],
    properties: [
        new OA\Property(property: 'success', type: 'boolean', example: true),
        new OA\Property(property: 'data', type: 'object', additionalProperties: true),
    ]
)]
#[OA\Schema(
    schema: 'ValidationError',
    type: 'object',
    required: ['message', 'errors'],
    properties: [
        new OA\Property(property: 'message', type: 'string', example: 'The given data was invalid.'),
        new OA\Property(
            property: 'errors',
            type: 'object',
            additionalProperties: new OA\AdditionalProperties(
                type: 'array',
                items: new OA\Items(type: 'string')
            )
        ),
    ]
)]
/**
 * Especificacio global OpenAPI per a la API del projecte.
 */
class OpenApiSpec
{
}
