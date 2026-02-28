<?php

namespace Intranet\OpenApi;

use OpenApi\Attributes as OA;

/**
 * Documentacio OpenAPI de rutes REST definides amb Route::resource.
 */
class ApiResourceDocumentation
{

    /**
     * Operacions REST de /alumnofct (public).
     */
    #[OA\Get(
        path: '/alumnofct',
        operationId: 'alumnofctIndex',
        summary: 'Llista de registres de alumnofct',
        tags: ['FCT (Public)'],

        responses: [
            new OA\Response(response: 200, description: 'Llistat', content: new OA\JsonContent(ref: '#/components/schemas/AlumnoFctCollectionResponse')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Post(
        path: '/alumnofct',
        operationId: 'alumnofctStore',
        summary: 'Crea un registre de alumnofct',
        tags: ['FCT (Public)'],

        requestBody: new OA\RequestBody(required: false, content: new OA\JsonContent(ref: '#/components/schemas/AlumnoFctUpsertRequest')),
        responses: [
            new OA\Response(response: 200, description: 'Creat', content: new OA\JsonContent(ref: '#/components/schemas/AlumnoFctItemResponse')),
            new OA\Response(response: 422, description: 'Validacio incorrecta', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Get(
        path: '/alumnofct/{id}',
        operationId: 'alumnofctShow',
        summary: 'Obte el detall de alumnofct',
        tags: ['FCT (Public)'],

        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        responses: [
            new OA\Response(response: 200, description: 'Detall', content: new OA\JsonContent(ref: '#/components/schemas/AlumnoFctItemResponse')),
            new OA\Response(response: 404, description: 'No trobat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Put(
        path: '/alumnofct/{id}',
        operationId: 'alumnofctUpdate',
        summary: 'Actualitza un registre de alumnofct',
        tags: ['FCT (Public)'],

        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        requestBody: new OA\RequestBody(required: false, content: new OA\JsonContent(ref: '#/components/schemas/AlumnoFctUpsertRequest')),
        responses: [
            new OA\Response(response: 200, description: 'Actualitzat', content: new OA\JsonContent(ref: '#/components/schemas/AlumnoFctItemResponse')),
            new OA\Response(response: 404, description: 'No trobat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 422, description: 'Validacio incorrecta', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Delete(
        path: '/alumnofct/{id}',
        operationId: 'alumnofctDestroy',
        summary: 'Elimina un registre de alumnofct',
        tags: ['FCT (Public)'],

        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        responses: [
            new OA\Response(response: 200, description: 'Eliminat', content: new OA\JsonContent(ref: '#/components/schemas/ApiSuccess')),
            new OA\Response(response: 404, description: 'No trobat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    public function alumnofctResourceEndpoints(): void
    {
    }

    /**
     * Operacions REST de /projecte (public).
     */
    #[OA\Get(
        path: '/projecte',
        operationId: 'projecteIndex',
        summary: 'Llista de registres de projecte',
        tags: ['FCT (Public)'],

        responses: [
            new OA\Response(response: 200, description: 'Llistat', content: new OA\JsonContent(ref: '#/components/schemas/GenericCollectionResponse')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Post(
        path: '/projecte',
        operationId: 'projecteStore',
        summary: 'Crea un registre de projecte',
        tags: ['FCT (Public)'],

        requestBody: new OA\RequestBody(required: false, content: new OA\JsonContent(ref: '#/components/schemas/GenericUpsertRequest')),
        responses: [
            new OA\Response(response: 200, description: 'Creat', content: new OA\JsonContent(ref: '#/components/schemas/GenericItemResponse')),
            new OA\Response(response: 422, description: 'Validacio incorrecta', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Get(
        path: '/projecte/{id}',
        operationId: 'projecteShow',
        summary: 'Obte el detall de projecte',
        tags: ['FCT (Public)'],

        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        responses: [
            new OA\Response(response: 200, description: 'Detall', content: new OA\JsonContent(ref: '#/components/schemas/GenericItemResponse')),
            new OA\Response(response: 404, description: 'No trobat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Put(
        path: '/projecte/{id}',
        operationId: 'projecteUpdate',
        summary: 'Actualitza un registre de projecte',
        tags: ['FCT (Public)'],

        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        requestBody: new OA\RequestBody(required: false, content: new OA\JsonContent(ref: '#/components/schemas/GenericUpsertRequest')),
        responses: [
            new OA\Response(response: 200, description: 'Actualitzat', content: new OA\JsonContent(ref: '#/components/schemas/GenericItemResponse')),
            new OA\Response(response: 404, description: 'No trobat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 422, description: 'Validacio incorrecta', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Delete(
        path: '/projecte/{id}',
        operationId: 'projecteDestroy',
        summary: 'Elimina un registre de projecte',
        tags: ['FCT (Public)'],

        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        responses: [
            new OA\Response(response: 200, description: 'Eliminat', content: new OA\JsonContent(ref: '#/components/schemas/ApiSuccess')),
            new OA\Response(response: 404, description: 'No trobat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    public function projecteResourceEndpoints(): void
    {
    }

    /**
     * Operacions REST de /actividad (protegides).
     */
    #[OA\Get(
        path: '/actividad',
        operationId: 'actividadIndex',
        summary: 'Llista de registres de actividad',
        tags: ['Activitats'],
        security: [['sanctum' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Llistat', content: new OA\JsonContent(ref: '#/components/schemas/GenericCollectionResponse')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Post(
        path: '/actividad',
        operationId: 'actividadStore',
        summary: 'Crea un registre de actividad',
        tags: ['Activitats'],
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(required: false, content: new OA\JsonContent(ref: '#/components/schemas/GenericUpsertRequest')),
        responses: [
            new OA\Response(response: 200, description: 'Creat', content: new OA\JsonContent(ref: '#/components/schemas/GenericItemResponse')),
            new OA\Response(response: 422, description: 'Validacio incorrecta', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Get(
        path: '/actividad/{id}',
        operationId: 'actividadShow',
        summary: 'Obte el detall de actividad',
        tags: ['Activitats'],
        security: [['sanctum' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        responses: [
            new OA\Response(response: 200, description: 'Detall', content: new OA\JsonContent(ref: '#/components/schemas/GenericItemResponse')),
            new OA\Response(response: 404, description: 'No trobat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Put(
        path: '/actividad/{id}',
        operationId: 'actividadUpdate',
        summary: 'Actualitza un registre de actividad',
        tags: ['Activitats'],
        security: [['sanctum' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        requestBody: new OA\RequestBody(required: false, content: new OA\JsonContent(ref: '#/components/schemas/GenericUpsertRequest')),
        responses: [
            new OA\Response(response: 200, description: 'Actualitzat', content: new OA\JsonContent(ref: '#/components/schemas/GenericItemResponse')),
            new OA\Response(response: 404, description: 'No trobat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 422, description: 'Validacio incorrecta', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Delete(
        path: '/actividad/{id}',
        operationId: 'actividadDestroy',
        summary: 'Elimina un registre de actividad',
        tags: ['Activitats'],
        security: [['sanctum' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        responses: [
            new OA\Response(response: 200, description: 'Eliminat', content: new OA\JsonContent(ref: '#/components/schemas/ApiSuccess')),
            new OA\Response(response: 404, description: 'No trobat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    public function actividadResourceEndpoints(): void
    {
    }

    /**
     * Operacions REST de /programacion (protegides).
     */
    #[OA\Get(
        path: '/programacion',
        operationId: 'programacionIndex',
        summary: 'Llista de registres de programacion',
        tags: ['Activitats'],
        security: [['sanctum' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Llistat', content: new OA\JsonContent(ref: '#/components/schemas/GenericCollectionResponse')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Post(
        path: '/programacion',
        operationId: 'programacionStore',
        summary: 'Crea un registre de programacion',
        tags: ['Activitats'],
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(required: false, content: new OA\JsonContent(ref: '#/components/schemas/GenericUpsertRequest')),
        responses: [
            new OA\Response(response: 200, description: 'Creat', content: new OA\JsonContent(ref: '#/components/schemas/GenericItemResponse')),
            new OA\Response(response: 422, description: 'Validacio incorrecta', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Get(
        path: '/programacion/{id}',
        operationId: 'programacionShow',
        summary: 'Obte el detall de programacion',
        tags: ['Activitats'],
        security: [['sanctum' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        responses: [
            new OA\Response(response: 200, description: 'Detall', content: new OA\JsonContent(ref: '#/components/schemas/GenericItemResponse')),
            new OA\Response(response: 404, description: 'No trobat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Put(
        path: '/programacion/{id}',
        operationId: 'programacionUpdate',
        summary: 'Actualitza un registre de programacion',
        tags: ['Activitats'],
        security: [['sanctum' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        requestBody: new OA\RequestBody(required: false, content: new OA\JsonContent(ref: '#/components/schemas/GenericUpsertRequest')),
        responses: [
            new OA\Response(response: 200, description: 'Actualitzat', content: new OA\JsonContent(ref: '#/components/schemas/GenericItemResponse')),
            new OA\Response(response: 404, description: 'No trobat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 422, description: 'Validacio incorrecta', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Delete(
        path: '/programacion/{id}',
        operationId: 'programacionDestroy',
        summary: 'Elimina un registre de programacion',
        tags: ['Activitats'],
        security: [['sanctum' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        responses: [
            new OA\Response(response: 200, description: 'Eliminat', content: new OA\JsonContent(ref: '#/components/schemas/ApiSuccess')),
            new OA\Response(response: 404, description: 'No trobat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    public function programacionResourceEndpoints(): void
    {
    }

    /**
     * Operacions REST de /reunion (protegides).
     */
    #[OA\Get(
        path: '/reunion',
        operationId: 'reunionIndex',
        summary: 'Llista de registres de reunion',
        tags: ['Activitats'],
        security: [['sanctum' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Llistat', content: new OA\JsonContent(ref: '#/components/schemas/GenericCollectionResponse')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Post(
        path: '/reunion',
        operationId: 'reunionStore',
        summary: 'Crea un registre de reunion',
        tags: ['Activitats'],
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(required: false, content: new OA\JsonContent(ref: '#/components/schemas/GenericUpsertRequest')),
        responses: [
            new OA\Response(response: 200, description: 'Creat', content: new OA\JsonContent(ref: '#/components/schemas/GenericItemResponse')),
            new OA\Response(response: 422, description: 'Validacio incorrecta', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Get(
        path: '/reunion/{id}',
        operationId: 'reunionShow',
        summary: 'Obte el detall de reunion',
        tags: ['Activitats'],
        security: [['sanctum' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        responses: [
            new OA\Response(response: 200, description: 'Detall', content: new OA\JsonContent(ref: '#/components/schemas/GenericItemResponse')),
            new OA\Response(response: 404, description: 'No trobat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Put(
        path: '/reunion/{id}',
        operationId: 'reunionUpdate',
        summary: 'Actualitza un registre de reunion',
        tags: ['Activitats'],
        security: [['sanctum' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        requestBody: new OA\RequestBody(required: false, content: new OA\JsonContent(ref: '#/components/schemas/GenericUpsertRequest')),
        responses: [
            new OA\Response(response: 200, description: 'Actualitzat', content: new OA\JsonContent(ref: '#/components/schemas/GenericItemResponse')),
            new OA\Response(response: 404, description: 'No trobat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 422, description: 'Validacio incorrecta', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Delete(
        path: '/reunion/{id}',
        operationId: 'reunionDestroy',
        summary: 'Elimina un registre de reunion',
        tags: ['Activitats'],
        security: [['sanctum' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        responses: [
            new OA\Response(response: 200, description: 'Eliminat', content: new OA\JsonContent(ref: '#/components/schemas/ApiSuccess')),
            new OA\Response(response: 404, description: 'No trobat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    public function reunionResourceEndpoints(): void
    {
    }

    /**
     * Operacions REST de /falta (protegides).
     */
    #[OA\Get(
        path: '/falta',
        operationId: 'faltaIndex',
        summary: 'Llista de registres de falta',
        tags: ['Incidencies'],
        security: [['sanctum' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Llistat', content: new OA\JsonContent(ref: '#/components/schemas/GenericCollectionResponse')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Post(
        path: '/falta',
        operationId: 'faltaStore',
        summary: 'Crea un registre de falta',
        tags: ['Incidencies'],
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(required: false, content: new OA\JsonContent(ref: '#/components/schemas/GenericUpsertRequest')),
        responses: [
            new OA\Response(response: 200, description: 'Creat', content: new OA\JsonContent(ref: '#/components/schemas/GenericItemResponse')),
            new OA\Response(response: 422, description: 'Validacio incorrecta', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Get(
        path: '/falta/{id}',
        operationId: 'faltaShow',
        summary: 'Obte el detall de falta',
        tags: ['Incidencies'],
        security: [['sanctum' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        responses: [
            new OA\Response(response: 200, description: 'Detall', content: new OA\JsonContent(ref: '#/components/schemas/GenericItemResponse')),
            new OA\Response(response: 404, description: 'No trobat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Put(
        path: '/falta/{id}',
        operationId: 'faltaUpdate',
        summary: 'Actualitza un registre de falta',
        tags: ['Incidencies'],
        security: [['sanctum' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        requestBody: new OA\RequestBody(required: false, content: new OA\JsonContent(ref: '#/components/schemas/GenericUpsertRequest')),
        responses: [
            new OA\Response(response: 200, description: 'Actualitzat', content: new OA\JsonContent(ref: '#/components/schemas/GenericItemResponse')),
            new OA\Response(response: 404, description: 'No trobat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 422, description: 'Validacio incorrecta', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Delete(
        path: '/falta/{id}',
        operationId: 'faltaDestroy',
        summary: 'Elimina un registre de falta',
        tags: ['Incidencies'],
        security: [['sanctum' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        responses: [
            new OA\Response(response: 200, description: 'Eliminat', content: new OA\JsonContent(ref: '#/components/schemas/ApiSuccess')),
            new OA\Response(response: 404, description: 'No trobat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    public function faltaResourceEndpoints(): void
    {
    }

    /**
     * Operacions REST de /documento (protegides).
     */
    #[OA\Get(
        path: '/documento',
        operationId: 'documentoIndex',
        summary: 'Llista de registres de documento',
        tags: ['Altres'],
        security: [['sanctum' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Llistat', content: new OA\JsonContent(ref: '#/components/schemas/GenericCollectionResponse')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Post(
        path: '/documento',
        operationId: 'documentoStore',
        summary: 'Crea un registre de documento',
        tags: ['Altres'],
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(required: false, content: new OA\JsonContent(ref: '#/components/schemas/GenericUpsertRequest')),
        responses: [
            new OA\Response(response: 200, description: 'Creat', content: new OA\JsonContent(ref: '#/components/schemas/GenericItemResponse')),
            new OA\Response(response: 422, description: 'Validacio incorrecta', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Get(
        path: '/documento/{id}',
        operationId: 'documentoShow',
        summary: 'Obte el detall de documento',
        tags: ['Altres'],
        security: [['sanctum' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        responses: [
            new OA\Response(response: 200, description: 'Detall', content: new OA\JsonContent(ref: '#/components/schemas/GenericItemResponse')),
            new OA\Response(response: 404, description: 'No trobat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Put(
        path: '/documento/{id}',
        operationId: 'documentoUpdate',
        summary: 'Actualitza un registre de documento',
        tags: ['Altres'],
        security: [['sanctum' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        requestBody: new OA\RequestBody(required: false, content: new OA\JsonContent(ref: '#/components/schemas/GenericUpsertRequest')),
        responses: [
            new OA\Response(response: 200, description: 'Actualitzat', content: new OA\JsonContent(ref: '#/components/schemas/GenericItemResponse')),
            new OA\Response(response: 404, description: 'No trobat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 422, description: 'Validacio incorrecta', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Delete(
        path: '/documento/{id}',
        operationId: 'documentoDestroy',
        summary: 'Elimina un registre de documento',
        tags: ['Altres'],
        security: [['sanctum' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        responses: [
            new OA\Response(response: 200, description: 'Eliminat', content: new OA\JsonContent(ref: '#/components/schemas/ApiSuccess')),
            new OA\Response(response: 404, description: 'No trobat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    public function documentoResourceEndpoints(): void
    {
    }

    /**
     * Operacions REST de /modulo_ciclo (protegides).
     */
    #[OA\Get(
        path: '/modulo_ciclo',
        operationId: 'modulo_cicloIndex',
        summary: 'Llista de registres de modulo_ciclo',
        tags: ['Academica'],
        security: [['sanctum' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Llistat', content: new OA\JsonContent(ref: '#/components/schemas/GenericCollectionResponse')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Post(
        path: '/modulo_ciclo',
        operationId: 'modulo_cicloStore',
        summary: 'Crea un registre de modulo_ciclo',
        tags: ['Academica'],
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(required: false, content: new OA\JsonContent(ref: '#/components/schemas/GenericUpsertRequest')),
        responses: [
            new OA\Response(response: 200, description: 'Creat', content: new OA\JsonContent(ref: '#/components/schemas/GenericItemResponse')),
            new OA\Response(response: 422, description: 'Validacio incorrecta', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Get(
        path: '/modulo_ciclo/{id}',
        operationId: 'modulo_cicloShow',
        summary: 'Obte el detall de modulo_ciclo',
        tags: ['Academica'],
        security: [['sanctum' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        responses: [
            new OA\Response(response: 200, description: 'Detall', content: new OA\JsonContent(ref: '#/components/schemas/GenericItemResponse')),
            new OA\Response(response: 404, description: 'No trobat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Put(
        path: '/modulo_ciclo/{id}',
        operationId: 'modulo_cicloUpdate',
        summary: 'Actualitza un registre de modulo_ciclo',
        tags: ['Academica'],
        security: [['sanctum' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        requestBody: new OA\RequestBody(required: false, content: new OA\JsonContent(ref: '#/components/schemas/GenericUpsertRequest')),
        responses: [
            new OA\Response(response: 200, description: 'Actualitzat', content: new OA\JsonContent(ref: '#/components/schemas/GenericItemResponse')),
            new OA\Response(response: 404, description: 'No trobat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 422, description: 'Validacio incorrecta', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Delete(
        path: '/modulo_ciclo/{id}',
        operationId: 'modulo_cicloDestroy',
        summary: 'Elimina un registre de modulo_ciclo',
        tags: ['Academica'],
        security: [['sanctum' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        responses: [
            new OA\Response(response: 200, description: 'Eliminat', content: new OA\JsonContent(ref: '#/components/schemas/ApiSuccess')),
            new OA\Response(response: 404, description: 'No trobat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    public function modulo_cicloResourceEndpoints(): void
    {
    }

    /**
     * Operacions REST de /resultado (protegides).
     */
    #[OA\Get(
        path: '/resultado',
        operationId: 'resultadoIndex',
        summary: 'Llista de registres de resultado',
        tags: ['Academica'],
        security: [['sanctum' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Llistat', content: new OA\JsonContent(ref: '#/components/schemas/GenericCollectionResponse')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Post(
        path: '/resultado',
        operationId: 'resultadoStore',
        summary: 'Crea un registre de resultado',
        tags: ['Academica'],
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(required: false, content: new OA\JsonContent(ref: '#/components/schemas/GenericUpsertRequest')),
        responses: [
            new OA\Response(response: 200, description: 'Creat', content: new OA\JsonContent(ref: '#/components/schemas/GenericItemResponse')),
            new OA\Response(response: 422, description: 'Validacio incorrecta', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Get(
        path: '/resultado/{id}',
        operationId: 'resultadoShow',
        summary: 'Obte el detall de resultado',
        tags: ['Academica'],
        security: [['sanctum' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        responses: [
            new OA\Response(response: 200, description: 'Detall', content: new OA\JsonContent(ref: '#/components/schemas/GenericItemResponse')),
            new OA\Response(response: 404, description: 'No trobat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Put(
        path: '/resultado/{id}',
        operationId: 'resultadoUpdate',
        summary: 'Actualitza un registre de resultado',
        tags: ['Academica'],
        security: [['sanctum' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        requestBody: new OA\RequestBody(required: false, content: new OA\JsonContent(ref: '#/components/schemas/GenericUpsertRequest')),
        responses: [
            new OA\Response(response: 200, description: 'Actualitzat', content: new OA\JsonContent(ref: '#/components/schemas/GenericItemResponse')),
            new OA\Response(response: 404, description: 'No trobat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 422, description: 'Validacio incorrecta', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Delete(
        path: '/resultado/{id}',
        operationId: 'resultadoDestroy',
        summary: 'Elimina un registre de resultado',
        tags: ['Academica'],
        security: [['sanctum' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        responses: [
            new OA\Response(response: 200, description: 'Eliminat', content: new OA\JsonContent(ref: '#/components/schemas/ApiSuccess')),
            new OA\Response(response: 404, description: 'No trobat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    public function resultadoResourceEndpoints(): void
    {
    }

    /**
     * Operacions REST de /comision (protegides).
     */
    #[OA\Get(
        path: '/comision',
        operationId: 'comisionIndex',
        summary: 'Llista de registres de comision',
        tags: ['Comissions'],
        security: [['sanctum' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Llistat', content: new OA\JsonContent(ref: '#/components/schemas/ComisionCollectionResponse')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Post(
        path: '/comision',
        operationId: 'comisionStore',
        summary: 'Crea un registre de comision',
        tags: ['Comissions'],
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(required: false, content: new OA\JsonContent(ref: '#/components/schemas/ComisionUpsertRequest')),
        responses: [
            new OA\Response(response: 200, description: 'Creat', content: new OA\JsonContent(ref: '#/components/schemas/ComisionItemResponse')),
            new OA\Response(response: 422, description: 'Validacio incorrecta', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Get(
        path: '/comision/{id}',
        operationId: 'comisionShow',
        summary: 'Obte el detall de comision',
        tags: ['Comissions'],
        security: [['sanctum' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        responses: [
            new OA\Response(response: 200, description: 'Detall', content: new OA\JsonContent(ref: '#/components/schemas/ComisionItemResponse')),
            new OA\Response(response: 404, description: 'No trobat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Put(
        path: '/comision/{id}',
        operationId: 'comisionUpdate',
        summary: 'Actualitza un registre de comision',
        tags: ['Comissions'],
        security: [['sanctum' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        requestBody: new OA\RequestBody(required: false, content: new OA\JsonContent(ref: '#/components/schemas/ComisionUpsertRequest')),
        responses: [
            new OA\Response(response: 200, description: 'Actualitzat', content: new OA\JsonContent(ref: '#/components/schemas/ComisionItemResponse')),
            new OA\Response(response: 404, description: 'No trobat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 422, description: 'Validacio incorrecta', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Delete(
        path: '/comision/{id}',
        operationId: 'comisionDestroy',
        summary: 'Elimina un registre de comision',
        tags: ['Comissions'],
        security: [['sanctum' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        responses: [
            new OA\Response(response: 200, description: 'Eliminat', content: new OA\JsonContent(ref: '#/components/schemas/ApiSuccess')),
            new OA\Response(response: 404, description: 'No trobat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    public function comisionResourceEndpoints(): void
    {
    }

    /**
     * Operacions REST de /instructor (protegides).
     */
    #[OA\Get(
        path: '/instructor',
        operationId: 'instructorIndex',
        summary: 'Llista de registres de instructor',
        tags: ['FCT'],
        security: [['sanctum' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Llistat', content: new OA\JsonContent(ref: '#/components/schemas/GenericCollectionResponse')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Post(
        path: '/instructor',
        operationId: 'instructorStore',
        summary: 'Crea un registre de instructor',
        tags: ['FCT'],
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(required: false, content: new OA\JsonContent(ref: '#/components/schemas/GenericUpsertRequest')),
        responses: [
            new OA\Response(response: 200, description: 'Creat', content: new OA\JsonContent(ref: '#/components/schemas/GenericItemResponse')),
            new OA\Response(response: 422, description: 'Validacio incorrecta', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Get(
        path: '/instructor/{id}',
        operationId: 'instructorShow',
        summary: 'Obte el detall de instructor',
        tags: ['FCT'],
        security: [['sanctum' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        responses: [
            new OA\Response(response: 200, description: 'Detall', content: new OA\JsonContent(ref: '#/components/schemas/GenericItemResponse')),
            new OA\Response(response: 404, description: 'No trobat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Put(
        path: '/instructor/{id}',
        operationId: 'instructorUpdate',
        summary: 'Actualitza un registre de instructor',
        tags: ['FCT'],
        security: [['sanctum' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        requestBody: new OA\RequestBody(required: false, content: new OA\JsonContent(ref: '#/components/schemas/GenericUpsertRequest')),
        responses: [
            new OA\Response(response: 200, description: 'Actualitzat', content: new OA\JsonContent(ref: '#/components/schemas/GenericItemResponse')),
            new OA\Response(response: 404, description: 'No trobat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 422, description: 'Validacio incorrecta', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Delete(
        path: '/instructor/{id}',
        operationId: 'instructorDestroy',
        summary: 'Elimina un registre de instructor',
        tags: ['FCT'],
        security: [['sanctum' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        responses: [
            new OA\Response(response: 200, description: 'Eliminat', content: new OA\JsonContent(ref: '#/components/schemas/ApiSuccess')),
            new OA\Response(response: 404, description: 'No trobat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    public function instructorResourceEndpoints(): void
    {
    }

    /**
     * Operacions REST de /ipguardia (protegides).
     */
    #[OA\Get(
        path: '/ipguardia',
        operationId: 'ipguardiaIndex',
        summary: 'Llista de registres de ipguardia',
        tags: ['Professorat'],
        security: [['sanctum' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Llistat', content: new OA\JsonContent(ref: '#/components/schemas/GenericCollectionResponse')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Post(
        path: '/ipguardia',
        operationId: 'ipguardiaStore',
        summary: 'Crea un registre de ipguardia',
        tags: ['Professorat'],
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(required: false, content: new OA\JsonContent(ref: '#/components/schemas/GenericUpsertRequest')),
        responses: [
            new OA\Response(response: 200, description: 'Creat', content: new OA\JsonContent(ref: '#/components/schemas/GenericItemResponse')),
            new OA\Response(response: 422, description: 'Validacio incorrecta', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Get(
        path: '/ipguardia/{id}',
        operationId: 'ipguardiaShow',
        summary: 'Obte el detall de ipguardia',
        tags: ['Professorat'],
        security: [['sanctum' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        responses: [
            new OA\Response(response: 200, description: 'Detall', content: new OA\JsonContent(ref: '#/components/schemas/GenericItemResponse')),
            new OA\Response(response: 404, description: 'No trobat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Put(
        path: '/ipguardia/{id}',
        operationId: 'ipguardiaUpdate',
        summary: 'Actualitza un registre de ipguardia',
        tags: ['Professorat'],
        security: [['sanctum' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        requestBody: new OA\RequestBody(required: false, content: new OA\JsonContent(ref: '#/components/schemas/GenericUpsertRequest')),
        responses: [
            new OA\Response(response: 200, description: 'Actualitzat', content: new OA\JsonContent(ref: '#/components/schemas/GenericItemResponse')),
            new OA\Response(response: 404, description: 'No trobat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 422, description: 'Validacio incorrecta', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Delete(
        path: '/ipguardia/{id}',
        operationId: 'ipguardiaDestroy',
        summary: 'Elimina un registre de ipguardia',
        tags: ['Professorat'],
        security: [['sanctum' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        responses: [
            new OA\Response(response: 200, description: 'Eliminat', content: new OA\JsonContent(ref: '#/components/schemas/ApiSuccess')),
            new OA\Response(response: 404, description: 'No trobat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    public function ipguardiaResourceEndpoints(): void
    {
    }

    /**
     * Operacions REST de /setting (protegides).
     */
    #[OA\Get(
        path: '/setting',
        operationId: 'settingIndex',
        summary: 'Llista de registres de setting',
        tags: ['Professorat'],
        security: [['sanctum' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Llistat', content: new OA\JsonContent(ref: '#/components/schemas/GenericCollectionResponse')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Post(
        path: '/setting',
        operationId: 'settingStore',
        summary: 'Crea un registre de setting',
        tags: ['Professorat'],
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(required: false, content: new OA\JsonContent(ref: '#/components/schemas/GenericUpsertRequest')),
        responses: [
            new OA\Response(response: 200, description: 'Creat', content: new OA\JsonContent(ref: '#/components/schemas/GenericItemResponse')),
            new OA\Response(response: 422, description: 'Validacio incorrecta', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Get(
        path: '/setting/{id}',
        operationId: 'settingShow',
        summary: 'Obte el detall de setting',
        tags: ['Professorat'],
        security: [['sanctum' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        responses: [
            new OA\Response(response: 200, description: 'Detall', content: new OA\JsonContent(ref: '#/components/schemas/GenericItemResponse')),
            new OA\Response(response: 404, description: 'No trobat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Put(
        path: '/setting/{id}',
        operationId: 'settingUpdate',
        summary: 'Actualitza un registre de setting',
        tags: ['Professorat'],
        security: [['sanctum' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        requestBody: new OA\RequestBody(required: false, content: new OA\JsonContent(ref: '#/components/schemas/GenericUpsertRequest')),
        responses: [
            new OA\Response(response: 200, description: 'Actualitzat', content: new OA\JsonContent(ref: '#/components/schemas/GenericItemResponse')),
            new OA\Response(response: 404, description: 'No trobat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 422, description: 'Validacio incorrecta', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Delete(
        path: '/setting/{id}',
        operationId: 'settingDestroy',
        summary: 'Elimina un registre de setting',
        tags: ['Professorat'],
        security: [['sanctum' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        responses: [
            new OA\Response(response: 200, description: 'Eliminat', content: new OA\JsonContent(ref: '#/components/schemas/ApiSuccess')),
            new OA\Response(response: 404, description: 'No trobat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    public function settingResourceEndpoints(): void
    {
    }

    /**
     * Operacions REST de /ppoll (protegides).
     */
    #[OA\Get(
        path: '/ppoll',
        operationId: 'ppollIndex',
        summary: 'Llista de registres de ppoll',
        tags: ['Activitats'],
        security: [['sanctum' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Llistat', content: new OA\JsonContent(ref: '#/components/schemas/GenericCollectionResponse')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Post(
        path: '/ppoll',
        operationId: 'ppollStore',
        summary: 'Crea un registre de ppoll',
        tags: ['Activitats'],
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(required: false, content: new OA\JsonContent(ref: '#/components/schemas/GenericUpsertRequest')),
        responses: [
            new OA\Response(response: 200, description: 'Creat', content: new OA\JsonContent(ref: '#/components/schemas/GenericItemResponse')),
            new OA\Response(response: 422, description: 'Validacio incorrecta', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Get(
        path: '/ppoll/{id}',
        operationId: 'ppollShow',
        summary: 'Obte el detall de ppoll',
        tags: ['Activitats'],
        security: [['sanctum' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        responses: [
            new OA\Response(response: 200, description: 'Detall', content: new OA\JsonContent(ref: '#/components/schemas/GenericItemResponse')),
            new OA\Response(response: 404, description: 'No trobat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Put(
        path: '/ppoll/{id}',
        operationId: 'ppollUpdate',
        summary: 'Actualitza un registre de ppoll',
        tags: ['Activitats'],
        security: [['sanctum' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        requestBody: new OA\RequestBody(required: false, content: new OA\JsonContent(ref: '#/components/schemas/GenericUpsertRequest')),
        responses: [
            new OA\Response(response: 200, description: 'Actualitzat', content: new OA\JsonContent(ref: '#/components/schemas/GenericItemResponse')),
            new OA\Response(response: 404, description: 'No trobat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 422, description: 'Validacio incorrecta', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Delete(
        path: '/ppoll/{id}',
        operationId: 'ppollDestroy',
        summary: 'Elimina un registre de ppoll',
        tags: ['Activitats'],
        security: [['sanctum' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        responses: [
            new OA\Response(response: 200, description: 'Eliminat', content: new OA\JsonContent(ref: '#/components/schemas/ApiSuccess')),
            new OA\Response(response: 404, description: 'No trobat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    public function ppollResourceEndpoints(): void
    {
    }

    /**
     * Operacions REST de /profesor (protegides).
     */
    #[OA\Get(
        path: '/profesor',
        operationId: 'profesorIndex',
        summary: 'Llista de registres de profesor',
        tags: ['Professorat'],
        security: [['sanctum' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Llistat', content: new OA\JsonContent(ref: '#/components/schemas/ProfesorCollectionResponse')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Post(
        path: '/profesor',
        operationId: 'profesorStore',
        summary: 'Crea un registre de profesor',
        tags: ['Professorat'],
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(required: false, content: new OA\JsonContent(ref: '#/components/schemas/ProfesorUpsertRequest')),
        responses: [
            new OA\Response(response: 200, description: 'Creat', content: new OA\JsonContent(ref: '#/components/schemas/ProfesorItemResponse')),
            new OA\Response(response: 422, description: 'Validacio incorrecta', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Get(
        path: '/profesor/{id}',
        operationId: 'profesorShow',
        summary: 'Obte el detall de profesor',
        tags: ['Professorat'],
        security: [['sanctum' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        responses: [
            new OA\Response(response: 200, description: 'Detall', content: new OA\JsonContent(ref: '#/components/schemas/ProfesorItemResponse')),
            new OA\Response(response: 404, description: 'No trobat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Put(
        path: '/profesor/{id}',
        operationId: 'profesorUpdate',
        summary: 'Actualitza un registre de profesor',
        tags: ['Professorat'],
        security: [['sanctum' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        requestBody: new OA\RequestBody(required: false, content: new OA\JsonContent(ref: '#/components/schemas/ProfesorUpsertRequest')),
        responses: [
            new OA\Response(response: 200, description: 'Actualitzat', content: new OA\JsonContent(ref: '#/components/schemas/ProfesorItemResponse')),
            new OA\Response(response: 404, description: 'No trobat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 422, description: 'Validacio incorrecta', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Delete(
        path: '/profesor/{id}',
        operationId: 'profesorDestroy',
        summary: 'Elimina un registre de profesor',
        tags: ['Professorat'],
        security: [['sanctum' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        responses: [
            new OA\Response(response: 200, description: 'Eliminat', content: new OA\JsonContent(ref: '#/components/schemas/ApiSuccess')),
            new OA\Response(response: 404, description: 'No trobat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    public function profesorResourceEndpoints(): void
    {
    }

    /**
     * Operacions REST de /faltaProfesor (protegides).
     */
    #[OA\Get(
        path: '/faltaProfesor',
        operationId: 'faltaprofesorIndex',
        summary: 'Llista de registres de faltaProfesor',
        tags: ['Professorat'],
        security: [['sanctum' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Llistat', content: new OA\JsonContent(ref: '#/components/schemas/GenericCollectionResponse')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Post(
        path: '/faltaProfesor',
        operationId: 'faltaprofesorStore',
        summary: 'Crea un registre de faltaProfesor',
        tags: ['Professorat'],
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(required: false, content: new OA\JsonContent(ref: '#/components/schemas/GenericUpsertRequest')),
        responses: [
            new OA\Response(response: 200, description: 'Creat', content: new OA\JsonContent(ref: '#/components/schemas/GenericItemResponse')),
            new OA\Response(response: 422, description: 'Validacio incorrecta', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Get(
        path: '/faltaProfesor/{id}',
        operationId: 'faltaprofesorShow',
        summary: 'Obte el detall de faltaProfesor',
        tags: ['Professorat'],
        security: [['sanctum' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        responses: [
            new OA\Response(response: 200, description: 'Detall', content: new OA\JsonContent(ref: '#/components/schemas/GenericItemResponse')),
            new OA\Response(response: 404, description: 'No trobat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Put(
        path: '/faltaProfesor/{id}',
        operationId: 'faltaprofesorUpdate',
        summary: 'Actualitza un registre de faltaProfesor',
        tags: ['Professorat'],
        security: [['sanctum' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        requestBody: new OA\RequestBody(required: false, content: new OA\JsonContent(ref: '#/components/schemas/GenericUpsertRequest')),
        responses: [
            new OA\Response(response: 200, description: 'Actualitzat', content: new OA\JsonContent(ref: '#/components/schemas/GenericItemResponse')),
            new OA\Response(response: 404, description: 'No trobat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 422, description: 'Validacio incorrecta', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Delete(
        path: '/faltaProfesor/{id}',
        operationId: 'faltaprofesorDestroy',
        summary: 'Elimina un registre de faltaProfesor',
        tags: ['Professorat'],
        security: [['sanctum' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        responses: [
            new OA\Response(response: 200, description: 'Eliminat', content: new OA\JsonContent(ref: '#/components/schemas/ApiSuccess')),
            new OA\Response(response: 404, description: 'No trobat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    public function faltaprofesorResourceEndpoints(): void
    {
    }

    /**
     * Operacions REST de /material (protegides).
     */
    #[OA\Get(
        path: '/material',
        operationId: 'materialIndex',
        summary: 'Llista de registres de material',
        tags: ['Materials'],
        security: [['sanctum' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Llistat', content: new OA\JsonContent(ref: '#/components/schemas/MaterialCollectionResponse')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Post(
        path: '/material',
        operationId: 'materialStore',
        summary: 'Crea un registre de material',
        tags: ['Materials'],
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(required: false, content: new OA\JsonContent(ref: '#/components/schemas/MaterialUpsertRequest')),
        responses: [
            new OA\Response(response: 200, description: 'Creat', content: new OA\JsonContent(ref: '#/components/schemas/MaterialItemResponse')),
            new OA\Response(response: 422, description: 'Validacio incorrecta', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Get(
        path: '/material/{id}',
        operationId: 'materialShow',
        summary: 'Obte el detall de material',
        tags: ['Materials'],
        security: [['sanctum' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        responses: [
            new OA\Response(response: 200, description: 'Detall', content: new OA\JsonContent(ref: '#/components/schemas/MaterialItemResponse')),
            new OA\Response(response: 404, description: 'No trobat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Put(
        path: '/material/{id}',
        operationId: 'materialUpdate',
        summary: 'Actualitza un registre de material',
        tags: ['Materials'],
        security: [['sanctum' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        requestBody: new OA\RequestBody(required: false, content: new OA\JsonContent(ref: '#/components/schemas/MaterialUpsertRequest')),
        responses: [
            new OA\Response(response: 200, description: 'Actualitzat', content: new OA\JsonContent(ref: '#/components/schemas/MaterialItemResponse')),
            new OA\Response(response: 404, description: 'No trobat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 422, description: 'Validacio incorrecta', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Delete(
        path: '/material/{id}',
        operationId: 'materialDestroy',
        summary: 'Elimina un registre de material',
        tags: ['Materials'],
        security: [['sanctum' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        responses: [
            new OA\Response(response: 200, description: 'Eliminat', content: new OA\JsonContent(ref: '#/components/schemas/ApiSuccess')),
            new OA\Response(response: 404, description: 'No trobat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    public function materialResourceEndpoints(): void
    {
    }

    /**
     * Operacions REST de /materialbaja (protegides).
     */
    #[OA\Get(
        path: '/materialbaja',
        operationId: 'materialbajaIndex',
        summary: 'Llista de registres de materialbaja',
        tags: ['Materials'],
        security: [['sanctum' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Llistat', content: new OA\JsonContent(ref: '#/components/schemas/GenericCollectionResponse')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Post(
        path: '/materialbaja',
        operationId: 'materialbajaStore',
        summary: 'Crea un registre de materialbaja',
        tags: ['Materials'],
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(required: false, content: new OA\JsonContent(ref: '#/components/schemas/GenericUpsertRequest')),
        responses: [
            new OA\Response(response: 200, description: 'Creat', content: new OA\JsonContent(ref: '#/components/schemas/GenericItemResponse')),
            new OA\Response(response: 422, description: 'Validacio incorrecta', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Get(
        path: '/materialbaja/{id}',
        operationId: 'materialbajaShow',
        summary: 'Obte el detall de materialbaja',
        tags: ['Materials'],
        security: [['sanctum' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        responses: [
            new OA\Response(response: 200, description: 'Detall', content: new OA\JsonContent(ref: '#/components/schemas/GenericItemResponse')),
            new OA\Response(response: 404, description: 'No trobat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Put(
        path: '/materialbaja/{id}',
        operationId: 'materialbajaUpdate',
        summary: 'Actualitza un registre de materialbaja',
        tags: ['Materials'],
        security: [['sanctum' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        requestBody: new OA\RequestBody(required: false, content: new OA\JsonContent(ref: '#/components/schemas/GenericUpsertRequest')),
        responses: [
            new OA\Response(response: 200, description: 'Actualitzat', content: new OA\JsonContent(ref: '#/components/schemas/GenericItemResponse')),
            new OA\Response(response: 404, description: 'No trobat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 422, description: 'Validacio incorrecta', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Delete(
        path: '/materialbaja/{id}',
        operationId: 'materialbajaDestroy',
        summary: 'Elimina un registre de materialbaja',
        tags: ['Materials'],
        security: [['sanctum' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        responses: [
            new OA\Response(response: 200, description: 'Eliminat', content: new OA\JsonContent(ref: '#/components/schemas/ApiSuccess')),
            new OA\Response(response: 404, description: 'No trobat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    public function materialbajaResourceEndpoints(): void
    {
    }

    /**
     * Operacions REST de /espacio (protegides).
     */
    #[OA\Get(
        path: '/espacio',
        operationId: 'espacioIndex',
        summary: 'Llista de registres de espacio',
        tags: ['Materials'],
        security: [['sanctum' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Llistat', content: new OA\JsonContent(ref: '#/components/schemas/GenericCollectionResponse')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Post(
        path: '/espacio',
        operationId: 'espacioStore',
        summary: 'Crea un registre de espacio',
        tags: ['Materials'],
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(required: false, content: new OA\JsonContent(ref: '#/components/schemas/GenericUpsertRequest')),
        responses: [
            new OA\Response(response: 200, description: 'Creat', content: new OA\JsonContent(ref: '#/components/schemas/GenericItemResponse')),
            new OA\Response(response: 422, description: 'Validacio incorrecta', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Get(
        path: '/espacio/{id}',
        operationId: 'espacioShow',
        summary: 'Obte el detall de espacio',
        tags: ['Materials'],
        security: [['sanctum' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        responses: [
            new OA\Response(response: 200, description: 'Detall', content: new OA\JsonContent(ref: '#/components/schemas/GenericItemResponse')),
            new OA\Response(response: 404, description: 'No trobat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Put(
        path: '/espacio/{id}',
        operationId: 'espacioUpdate',
        summary: 'Actualitza un registre de espacio',
        tags: ['Materials'],
        security: [['sanctum' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        requestBody: new OA\RequestBody(required: false, content: new OA\JsonContent(ref: '#/components/schemas/GenericUpsertRequest')),
        responses: [
            new OA\Response(response: 200, description: 'Actualitzat', content: new OA\JsonContent(ref: '#/components/schemas/GenericItemResponse')),
            new OA\Response(response: 404, description: 'No trobat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 422, description: 'Validacio incorrecta', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Delete(
        path: '/espacio/{id}',
        operationId: 'espacioDestroy',
        summary: 'Elimina un registre de espacio',
        tags: ['Materials'],
        security: [['sanctum' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        responses: [
            new OA\Response(response: 200, description: 'Eliminat', content: new OA\JsonContent(ref: '#/components/schemas/ApiSuccess')),
            new OA\Response(response: 404, description: 'No trobat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    public function espacioResourceEndpoints(): void
    {
    }

    /**
     * Operacions REST de /guardia (protegides).
     */
    #[OA\Get(
        path: '/guardia',
        operationId: 'guardiaIndex',
        summary: 'Llista de registres de guardia',
        tags: ['Guardies'],
        security: [['sanctum' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Llistat', content: new OA\JsonContent(ref: '#/components/schemas/GuardiaCollectionResponse')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Post(
        path: '/guardia',
        operationId: 'guardiaStore',
        summary: 'Crea un registre de guardia',
        tags: ['Guardies'],
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(required: false, content: new OA\JsonContent(ref: '#/components/schemas/GuardiaUpsertRequest')),
        responses: [
            new OA\Response(response: 200, description: 'Creat', content: new OA\JsonContent(ref: '#/components/schemas/GuardiaItemResponse')),
            new OA\Response(response: 422, description: 'Validacio incorrecta', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Get(
        path: '/guardia/{id}',
        operationId: 'guardiaShow',
        summary: 'Obte el detall de guardia',
        tags: ['Guardies'],
        security: [['sanctum' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        responses: [
            new OA\Response(response: 200, description: 'Detall', content: new OA\JsonContent(ref: '#/components/schemas/GuardiaItemResponse')),
            new OA\Response(response: 404, description: 'No trobat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Put(
        path: '/guardia/{id}',
        operationId: 'guardiaUpdate',
        summary: 'Actualitza un registre de guardia',
        tags: ['Guardies'],
        security: [['sanctum' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        requestBody: new OA\RequestBody(required: false, content: new OA\JsonContent(ref: '#/components/schemas/GuardiaUpsertRequest')),
        responses: [
            new OA\Response(response: 200, description: 'Actualitzat', content: new OA\JsonContent(ref: '#/components/schemas/GuardiaItemResponse')),
            new OA\Response(response: 404, description: 'No trobat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 422, description: 'Validacio incorrecta', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Delete(
        path: '/guardia/{id}',
        operationId: 'guardiaDestroy',
        summary: 'Elimina un registre de guardia',
        tags: ['Guardies'],
        security: [['sanctum' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        responses: [
            new OA\Response(response: 200, description: 'Eliminat', content: new OA\JsonContent(ref: '#/components/schemas/ApiSuccess')),
            new OA\Response(response: 404, description: 'No trobat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    public function guardiaResourceEndpoints(): void
    {
    }

    /**
     * Operacions REST de /departamento (protegides).
     */
    #[OA\Get(
        path: '/departamento',
        operationId: 'departamentoIndex',
        summary: 'Llista de registres de departamento',
        tags: ['Organitzacio'],
        security: [['sanctum' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Llistat', content: new OA\JsonContent(ref: '#/components/schemas/GenericCollectionResponse')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Post(
        path: '/departamento',
        operationId: 'departamentoStore',
        summary: 'Crea un registre de departamento',
        tags: ['Organitzacio'],
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(required: false, content: new OA\JsonContent(ref: '#/components/schemas/GenericUpsertRequest')),
        responses: [
            new OA\Response(response: 200, description: 'Creat', content: new OA\JsonContent(ref: '#/components/schemas/GenericItemResponse')),
            new OA\Response(response: 422, description: 'Validacio incorrecta', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Get(
        path: '/departamento/{id}',
        operationId: 'departamentoShow',
        summary: 'Obte el detall de departamento',
        tags: ['Organitzacio'],
        security: [['sanctum' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        responses: [
            new OA\Response(response: 200, description: 'Detall', content: new OA\JsonContent(ref: '#/components/schemas/GenericItemResponse')),
            new OA\Response(response: 404, description: 'No trobat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Put(
        path: '/departamento/{id}',
        operationId: 'departamentoUpdate',
        summary: 'Actualitza un registre de departamento',
        tags: ['Organitzacio'],
        security: [['sanctum' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        requestBody: new OA\RequestBody(required: false, content: new OA\JsonContent(ref: '#/components/schemas/GenericUpsertRequest')),
        responses: [
            new OA\Response(response: 200, description: 'Actualitzat', content: new OA\JsonContent(ref: '#/components/schemas/GenericItemResponse')),
            new OA\Response(response: 404, description: 'No trobat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 422, description: 'Validacio incorrecta', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Delete(
        path: '/departamento/{id}',
        operationId: 'departamentoDestroy',
        summary: 'Elimina un registre de departamento',
        tags: ['Organitzacio'],
        security: [['sanctum' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        responses: [
            new OA\Response(response: 200, description: 'Eliminat', content: new OA\JsonContent(ref: '#/components/schemas/ApiSuccess')),
            new OA\Response(response: 404, description: 'No trobat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    public function departamentoResourceEndpoints(): void
    {
    }

    /**
     * Operacions REST de /reserva (protegides).
     */
    #[OA\Get(
        path: '/reserva',
        operationId: 'reservaIndex',
        summary: 'Llista de registres de reserva',
        tags: ['Reserves'],
        security: [['sanctum' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Llistat', content: new OA\JsonContent(ref: '#/components/schemas/GenericCollectionResponse')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Post(
        path: '/reserva',
        operationId: 'reservaStore',
        summary: 'Crea un registre de reserva',
        tags: ['Reserves'],
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(required: false, content: new OA\JsonContent(ref: '#/components/schemas/GenericUpsertRequest')),
        responses: [
            new OA\Response(response: 200, description: 'Creat', content: new OA\JsonContent(ref: '#/components/schemas/GenericItemResponse')),
            new OA\Response(response: 422, description: 'Validacio incorrecta', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Get(
        path: '/reserva/{id}',
        operationId: 'reservaShow',
        summary: 'Obte el detall de reserva',
        tags: ['Reserves'],
        security: [['sanctum' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        responses: [
            new OA\Response(response: 200, description: 'Detall', content: new OA\JsonContent(ref: '#/components/schemas/GenericItemResponse')),
            new OA\Response(response: 404, description: 'No trobat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Put(
        path: '/reserva/{id}',
        operationId: 'reservaUpdate',
        summary: 'Actualitza un registre de reserva',
        tags: ['Reserves'],
        security: [['sanctum' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        requestBody: new OA\RequestBody(required: false, content: new OA\JsonContent(ref: '#/components/schemas/GenericUpsertRequest')),
        responses: [
            new OA\Response(response: 200, description: 'Actualitzat', content: new OA\JsonContent(ref: '#/components/schemas/GenericItemResponse')),
            new OA\Response(response: 404, description: 'No trobat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 422, description: 'Validacio incorrecta', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Delete(
        path: '/reserva/{id}',
        operationId: 'reservaDestroy',
        summary: 'Elimina un registre de reserva',
        tags: ['Reserves'],
        security: [['sanctum' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        responses: [
            new OA\Response(response: 200, description: 'Eliminat', content: new OA\JsonContent(ref: '#/components/schemas/ApiSuccess')),
            new OA\Response(response: 404, description: 'No trobat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    public function reservaResourceEndpoints(): void
    {
    }

    /**
     * Operacions REST de /ordenreunion (protegides).
     */
    #[OA\Get(
        path: '/ordenreunion',
        operationId: 'ordenreunionIndex',
        summary: 'Llista de registres de ordenreunion',
        tags: ['Organitzacio'],
        security: [['sanctum' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Llistat', content: new OA\JsonContent(ref: '#/components/schemas/GenericCollectionResponse')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Post(
        path: '/ordenreunion',
        operationId: 'ordenreunionStore',
        summary: 'Crea un registre de ordenreunion',
        tags: ['Organitzacio'],
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(required: false, content: new OA\JsonContent(ref: '#/components/schemas/GenericUpsertRequest')),
        responses: [
            new OA\Response(response: 200, description: 'Creat', content: new OA\JsonContent(ref: '#/components/schemas/GenericItemResponse')),
            new OA\Response(response: 422, description: 'Validacio incorrecta', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Get(
        path: '/ordenreunion/{id}',
        operationId: 'ordenreunionShow',
        summary: 'Obte el detall de ordenreunion',
        tags: ['Organitzacio'],
        security: [['sanctum' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        responses: [
            new OA\Response(response: 200, description: 'Detall', content: new OA\JsonContent(ref: '#/components/schemas/GenericItemResponse')),
            new OA\Response(response: 404, description: 'No trobat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Put(
        path: '/ordenreunion/{id}',
        operationId: 'ordenreunionUpdate',
        summary: 'Actualitza un registre de ordenreunion',
        tags: ['Organitzacio'],
        security: [['sanctum' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        requestBody: new OA\RequestBody(required: false, content: new OA\JsonContent(ref: '#/components/schemas/GenericUpsertRequest')),
        responses: [
            new OA\Response(response: 200, description: 'Actualitzat', content: new OA\JsonContent(ref: '#/components/schemas/GenericItemResponse')),
            new OA\Response(response: 404, description: 'No trobat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 422, description: 'Validacio incorrecta', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Delete(
        path: '/ordenreunion/{id}',
        operationId: 'ordenreunionDestroy',
        summary: 'Elimina un registre de ordenreunion',
        tags: ['Organitzacio'],
        security: [['sanctum' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        responses: [
            new OA\Response(response: 200, description: 'Eliminat', content: new OA\JsonContent(ref: '#/components/schemas/ApiSuccess')),
            new OA\Response(response: 404, description: 'No trobat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    public function ordenreunionResourceEndpoints(): void
    {
    }

    /**
     * Operacions REST de /colaboracion (protegides).
     */
    #[OA\Get(
        path: '/colaboracion',
        operationId: 'colaboracionIndex',
        summary: 'Llista de registres de colaboracion',
        tags: ['FCT'],
        security: [['sanctum' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Llistat', content: new OA\JsonContent(ref: '#/components/schemas/GenericCollectionResponse')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Post(
        path: '/colaboracion',
        operationId: 'colaboracionStore',
        summary: 'Crea un registre de colaboracion',
        tags: ['FCT'],
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(required: false, content: new OA\JsonContent(ref: '#/components/schemas/GenericUpsertRequest')),
        responses: [
            new OA\Response(response: 200, description: 'Creat', content: new OA\JsonContent(ref: '#/components/schemas/GenericItemResponse')),
            new OA\Response(response: 422, description: 'Validacio incorrecta', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Get(
        path: '/colaboracion/{id}',
        operationId: 'colaboracionShow',
        summary: 'Obte el detall de colaboracion',
        tags: ['FCT'],
        security: [['sanctum' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        responses: [
            new OA\Response(response: 200, description: 'Detall', content: new OA\JsonContent(ref: '#/components/schemas/GenericItemResponse')),
            new OA\Response(response: 404, description: 'No trobat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Put(
        path: '/colaboracion/{id}',
        operationId: 'colaboracionUpdate',
        summary: 'Actualitza un registre de colaboracion',
        tags: ['FCT'],
        security: [['sanctum' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        requestBody: new OA\RequestBody(required: false, content: new OA\JsonContent(ref: '#/components/schemas/GenericUpsertRequest')),
        responses: [
            new OA\Response(response: 200, description: 'Actualitzat', content: new OA\JsonContent(ref: '#/components/schemas/GenericItemResponse')),
            new OA\Response(response: 404, description: 'No trobat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 422, description: 'Validacio incorrecta', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Delete(
        path: '/colaboracion/{id}',
        operationId: 'colaboracionDestroy',
        summary: 'Elimina un registre de colaboracion',
        tags: ['FCT'],
        security: [['sanctum' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        responses: [
            new OA\Response(response: 200, description: 'Eliminat', content: new OA\JsonContent(ref: '#/components/schemas/ApiSuccess')),
            new OA\Response(response: 404, description: 'No trobat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    public function colaboracionResourceEndpoints(): void
    {
    }

    /**
     * Operacions REST de /centro (protegides).
     */
    #[OA\Get(
        path: '/centro',
        operationId: 'centroIndex',
        summary: 'Llista de registres de centro',
        tags: ['Organitzacio'],
        security: [['sanctum' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Llistat', content: new OA\JsonContent(ref: '#/components/schemas/GenericCollectionResponse')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Post(
        path: '/centro',
        operationId: 'centroStore',
        summary: 'Crea un registre de centro',
        tags: ['Organitzacio'],
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(required: false, content: new OA\JsonContent(ref: '#/components/schemas/GenericUpsertRequest')),
        responses: [
            new OA\Response(response: 200, description: 'Creat', content: new OA\JsonContent(ref: '#/components/schemas/GenericItemResponse')),
            new OA\Response(response: 422, description: 'Validacio incorrecta', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Get(
        path: '/centro/{id}',
        operationId: 'centroShow',
        summary: 'Obte el detall de centro',
        tags: ['Organitzacio'],
        security: [['sanctum' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        responses: [
            new OA\Response(response: 200, description: 'Detall', content: new OA\JsonContent(ref: '#/components/schemas/GenericItemResponse')),
            new OA\Response(response: 404, description: 'No trobat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Put(
        path: '/centro/{id}',
        operationId: 'centroUpdate',
        summary: 'Actualitza un registre de centro',
        tags: ['Organitzacio'],
        security: [['sanctum' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        requestBody: new OA\RequestBody(required: false, content: new OA\JsonContent(ref: '#/components/schemas/GenericUpsertRequest')),
        responses: [
            new OA\Response(response: 200, description: 'Actualitzat', content: new OA\JsonContent(ref: '#/components/schemas/GenericItemResponse')),
            new OA\Response(response: 404, description: 'No trobat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 422, description: 'Validacio incorrecta', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Delete(
        path: '/centro/{id}',
        operationId: 'centroDestroy',
        summary: 'Elimina un registre de centro',
        tags: ['Organitzacio'],
        security: [['sanctum' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        responses: [
            new OA\Response(response: 200, description: 'Eliminat', content: new OA\JsonContent(ref: '#/components/schemas/ApiSuccess')),
            new OA\Response(response: 404, description: 'No trobat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    public function centroResourceEndpoints(): void
    {
    }

    /**
     * Operacions REST de /grupotrabajo (protegides).
     */
    #[OA\Get(
        path: '/grupotrabajo',
        operationId: 'grupotrabajoIndex',
        summary: 'Llista de registres de grupotrabajo',
        tags: ['Organitzacio'],
        security: [['sanctum' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Llistat', content: new OA\JsonContent(ref: '#/components/schemas/GenericCollectionResponse')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Post(
        path: '/grupotrabajo',
        operationId: 'grupotrabajoStore',
        summary: 'Crea un registre de grupotrabajo',
        tags: ['Organitzacio'],
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(required: false, content: new OA\JsonContent(ref: '#/components/schemas/GenericUpsertRequest')),
        responses: [
            new OA\Response(response: 200, description: 'Creat', content: new OA\JsonContent(ref: '#/components/schemas/GenericItemResponse')),
            new OA\Response(response: 422, description: 'Validacio incorrecta', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Get(
        path: '/grupotrabajo/{id}',
        operationId: 'grupotrabajoShow',
        summary: 'Obte el detall de grupotrabajo',
        tags: ['Organitzacio'],
        security: [['sanctum' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        responses: [
            new OA\Response(response: 200, description: 'Detall', content: new OA\JsonContent(ref: '#/components/schemas/GenericItemResponse')),
            new OA\Response(response: 404, description: 'No trobat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Put(
        path: '/grupotrabajo/{id}',
        operationId: 'grupotrabajoUpdate',
        summary: 'Actualitza un registre de grupotrabajo',
        tags: ['Organitzacio'],
        security: [['sanctum' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        requestBody: new OA\RequestBody(required: false, content: new OA\JsonContent(ref: '#/components/schemas/GenericUpsertRequest')),
        responses: [
            new OA\Response(response: 200, description: 'Actualitzat', content: new OA\JsonContent(ref: '#/components/schemas/GenericItemResponse')),
            new OA\Response(response: 404, description: 'No trobat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 422, description: 'Validacio incorrecta', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Delete(
        path: '/grupotrabajo/{id}',
        operationId: 'grupotrabajoDestroy',
        summary: 'Elimina un registre de grupotrabajo',
        tags: ['Organitzacio'],
        security: [['sanctum' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        responses: [
            new OA\Response(response: 200, description: 'Eliminat', content: new OA\JsonContent(ref: '#/components/schemas/ApiSuccess')),
            new OA\Response(response: 404, description: 'No trobat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    public function grupotrabajoResourceEndpoints(): void
    {
    }

    /**
     * Operacions REST de /Empresa (protegides).
     */
    #[OA\Get(
        path: '/Empresa',
        operationId: 'empresaIndex',
        summary: 'Llista de registres de Empresa',
        tags: ['Organitzacio'],
        security: [['sanctum' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Llistat', content: new OA\JsonContent(ref: '#/components/schemas/GenericCollectionResponse')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Post(
        path: '/Empresa',
        operationId: 'empresaStore',
        summary: 'Crea un registre de Empresa',
        tags: ['Organitzacio'],
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(required: false, content: new OA\JsonContent(ref: '#/components/schemas/GenericUpsertRequest')),
        responses: [
            new OA\Response(response: 200, description: 'Creat', content: new OA\JsonContent(ref: '#/components/schemas/GenericItemResponse')),
            new OA\Response(response: 422, description: 'Validacio incorrecta', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Get(
        path: '/Empresa/{id}',
        operationId: 'empresaShow',
        summary: 'Obte el detall de Empresa',
        tags: ['Organitzacio'],
        security: [['sanctum' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        responses: [
            new OA\Response(response: 200, description: 'Detall', content: new OA\JsonContent(ref: '#/components/schemas/GenericItemResponse')),
            new OA\Response(response: 404, description: 'No trobat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Put(
        path: '/Empresa/{id}',
        operationId: 'empresaUpdate',
        summary: 'Actualitza un registre de Empresa',
        tags: ['Organitzacio'],
        security: [['sanctum' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        requestBody: new OA\RequestBody(required: false, content: new OA\JsonContent(ref: '#/components/schemas/GenericUpsertRequest')),
        responses: [
            new OA\Response(response: 200, description: 'Actualitzat', content: new OA\JsonContent(ref: '#/components/schemas/GenericItemResponse')),
            new OA\Response(response: 404, description: 'No trobat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 422, description: 'Validacio incorrecta', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Delete(
        path: '/Empresa/{id}',
        operationId: 'empresaDestroy',
        summary: 'Elimina un registre de Empresa',
        tags: ['Organitzacio'],
        security: [['sanctum' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        responses: [
            new OA\Response(response: 200, description: 'Eliminat', content: new OA\JsonContent(ref: '#/components/schemas/ApiSuccess')),
            new OA\Response(response: 404, description: 'No trobat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    public function empresaResourceEndpoints(): void
    {
    }

    /**
     * Operacions REST de /ordentrabajo (protegides).
     */
    #[OA\Get(
        path: '/ordentrabajo',
        operationId: 'ordentrabajoIndex',
        summary: 'Llista de registres de ordentrabajo',
        tags: ['Organitzacio'],
        security: [['sanctum' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Llistat', content: new OA\JsonContent(ref: '#/components/schemas/GenericCollectionResponse')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Post(
        path: '/ordentrabajo',
        operationId: 'ordentrabajoStore',
        summary: 'Crea un registre de ordentrabajo',
        tags: ['Organitzacio'],
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(required: false, content: new OA\JsonContent(ref: '#/components/schemas/GenericUpsertRequest')),
        responses: [
            new OA\Response(response: 200, description: 'Creat', content: new OA\JsonContent(ref: '#/components/schemas/GenericItemResponse')),
            new OA\Response(response: 422, description: 'Validacio incorrecta', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Get(
        path: '/ordentrabajo/{id}',
        operationId: 'ordentrabajoShow',
        summary: 'Obte el detall de ordentrabajo',
        tags: ['Organitzacio'],
        security: [['sanctum' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        responses: [
            new OA\Response(response: 200, description: 'Detall', content: new OA\JsonContent(ref: '#/components/schemas/GenericItemResponse')),
            new OA\Response(response: 404, description: 'No trobat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Put(
        path: '/ordentrabajo/{id}',
        operationId: 'ordentrabajoUpdate',
        summary: 'Actualitza un registre de ordentrabajo',
        tags: ['Organitzacio'],
        security: [['sanctum' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        requestBody: new OA\RequestBody(required: false, content: new OA\JsonContent(ref: '#/components/schemas/GenericUpsertRequest')),
        responses: [
            new OA\Response(response: 200, description: 'Actualitzat', content: new OA\JsonContent(ref: '#/components/schemas/GenericItemResponse')),
            new OA\Response(response: 404, description: 'No trobat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 422, description: 'Validacio incorrecta', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Delete(
        path: '/ordentrabajo/{id}',
        operationId: 'ordentrabajoDestroy',
        summary: 'Elimina un registre de ordentrabajo',
        tags: ['Organitzacio'],
        security: [['sanctum' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        responses: [
            new OA\Response(response: 200, description: 'Eliminat', content: new OA\JsonContent(ref: '#/components/schemas/ApiSuccess')),
            new OA\Response(response: 404, description: 'No trobat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    public function ordentrabajoResourceEndpoints(): void
    {
    }

    /**
     * Operacions REST de /incidencia (protegides).
     */
    #[OA\Get(
        path: '/incidencia',
        operationId: 'incidenciaIndex',
        summary: 'Llista de registres de incidencia',
        tags: ['Incidencies'],
        security: [['sanctum' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Llistat', content: new OA\JsonContent(ref: '#/components/schemas/GenericCollectionResponse')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Post(
        path: '/incidencia',
        operationId: 'incidenciaStore',
        summary: 'Crea un registre de incidencia',
        tags: ['Incidencies'],
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(required: false, content: new OA\JsonContent(ref: '#/components/schemas/GenericUpsertRequest')),
        responses: [
            new OA\Response(response: 200, description: 'Creat', content: new OA\JsonContent(ref: '#/components/schemas/GenericItemResponse')),
            new OA\Response(response: 422, description: 'Validacio incorrecta', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Get(
        path: '/incidencia/{id}',
        operationId: 'incidenciaShow',
        summary: 'Obte el detall de incidencia',
        tags: ['Incidencies'],
        security: [['sanctum' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        responses: [
            new OA\Response(response: 200, description: 'Detall', content: new OA\JsonContent(ref: '#/components/schemas/GenericItemResponse')),
            new OA\Response(response: 404, description: 'No trobat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Put(
        path: '/incidencia/{id}',
        operationId: 'incidenciaUpdate',
        summary: 'Actualitza un registre de incidencia',
        tags: ['Incidencies'],
        security: [['sanctum' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        requestBody: new OA\RequestBody(required: false, content: new OA\JsonContent(ref: '#/components/schemas/GenericUpsertRequest')),
        responses: [
            new OA\Response(response: 200, description: 'Actualitzat', content: new OA\JsonContent(ref: '#/components/schemas/GenericItemResponse')),
            new OA\Response(response: 404, description: 'No trobat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 422, description: 'Validacio incorrecta', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Delete(
        path: '/incidencia/{id}',
        operationId: 'incidenciaDestroy',
        summary: 'Elimina un registre de incidencia',
        tags: ['Incidencies'],
        security: [['sanctum' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        responses: [
            new OA\Response(response: 200, description: 'Eliminat', content: new OA\JsonContent(ref: '#/components/schemas/ApiSuccess')),
            new OA\Response(response: 404, description: 'No trobat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    public function incidenciaResourceEndpoints(): void
    {
    }

    /**
     * Operacions REST de /tipoincidencia (protegides).
     */
    #[OA\Get(
        path: '/tipoincidencia',
        operationId: 'tipoincidenciaIndex',
        summary: 'Llista de registres de tipoincidencia',
        tags: ['Incidencies'],
        security: [['sanctum' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Llistat', content: new OA\JsonContent(ref: '#/components/schemas/GenericCollectionResponse')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Post(
        path: '/tipoincidencia',
        operationId: 'tipoincidenciaStore',
        summary: 'Crea un registre de tipoincidencia',
        tags: ['Incidencies'],
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(required: false, content: new OA\JsonContent(ref: '#/components/schemas/GenericUpsertRequest')),
        responses: [
            new OA\Response(response: 200, description: 'Creat', content: new OA\JsonContent(ref: '#/components/schemas/GenericItemResponse')),
            new OA\Response(response: 422, description: 'Validacio incorrecta', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Get(
        path: '/tipoincidencia/{id}',
        operationId: 'tipoincidenciaShow',
        summary: 'Obte el detall de tipoincidencia',
        tags: ['Incidencies'],
        security: [['sanctum' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        responses: [
            new OA\Response(response: 200, description: 'Detall', content: new OA\JsonContent(ref: '#/components/schemas/GenericItemResponse')),
            new OA\Response(response: 404, description: 'No trobat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Put(
        path: '/tipoincidencia/{id}',
        operationId: 'tipoincidenciaUpdate',
        summary: 'Actualitza un registre de tipoincidencia',
        tags: ['Incidencies'],
        security: [['sanctum' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        requestBody: new OA\RequestBody(required: false, content: new OA\JsonContent(ref: '#/components/schemas/GenericUpsertRequest')),
        responses: [
            new OA\Response(response: 200, description: 'Actualitzat', content: new OA\JsonContent(ref: '#/components/schemas/GenericItemResponse')),
            new OA\Response(response: 404, description: 'No trobat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 422, description: 'Validacio incorrecta', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Delete(
        path: '/tipoincidencia/{id}',
        operationId: 'tipoincidenciaDestroy',
        summary: 'Elimina un registre de tipoincidencia',
        tags: ['Incidencies'],
        security: [['sanctum' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        responses: [
            new OA\Response(response: 200, description: 'Eliminat', content: new OA\JsonContent(ref: '#/components/schemas/ApiSuccess')),
            new OA\Response(response: 404, description: 'No trobat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    public function tipoincidenciaResourceEndpoints(): void
    {
    }

    /**
     * Operacions REST de /expediente (protegides).
     */
    #[OA\Get(
        path: '/expediente',
        operationId: 'expedienteIndex',
        summary: 'Llista de registres de expediente',
        tags: ['Academica'],
        security: [['sanctum' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Llistat', content: new OA\JsonContent(ref: '#/components/schemas/GenericCollectionResponse')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Post(
        path: '/expediente',
        operationId: 'expedienteStore',
        summary: 'Crea un registre de expediente',
        tags: ['Academica'],
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(required: false, content: new OA\JsonContent(ref: '#/components/schemas/GenericUpsertRequest')),
        responses: [
            new OA\Response(response: 200, description: 'Creat', content: new OA\JsonContent(ref: '#/components/schemas/GenericItemResponse')),
            new OA\Response(response: 422, description: 'Validacio incorrecta', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Get(
        path: '/expediente/{id}',
        operationId: 'expedienteShow',
        summary: 'Obte el detall de expediente',
        tags: ['Academica'],
        security: [['sanctum' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        responses: [
            new OA\Response(response: 200, description: 'Detall', content: new OA\JsonContent(ref: '#/components/schemas/GenericItemResponse')),
            new OA\Response(response: 404, description: 'No trobat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Put(
        path: '/expediente/{id}',
        operationId: 'expedienteUpdate',
        summary: 'Actualitza un registre de expediente',
        tags: ['Academica'],
        security: [['sanctum' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        requestBody: new OA\RequestBody(required: false, content: new OA\JsonContent(ref: '#/components/schemas/GenericUpsertRequest')),
        responses: [
            new OA\Response(response: 200, description: 'Actualitzat', content: new OA\JsonContent(ref: '#/components/schemas/GenericItemResponse')),
            new OA\Response(response: 404, description: 'No trobat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 422, description: 'Validacio incorrecta', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Delete(
        path: '/expediente/{id}',
        operationId: 'expedienteDestroy',
        summary: 'Elimina un registre de expediente',
        tags: ['Academica'],
        security: [['sanctum' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        responses: [
            new OA\Response(response: 200, description: 'Eliminat', content: new OA\JsonContent(ref: '#/components/schemas/ApiSuccess')),
            new OA\Response(response: 404, description: 'No trobat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    public function expedienteResourceEndpoints(): void
    {
    }

    /**
     * Operacions REST de /solicitud (protegides).
     */
    #[OA\Get(
        path: '/solicitud',
        operationId: 'solicitudIndex',
        summary: 'Llista de registres de solicitud',
        tags: ['Organitzacio'],
        security: [['sanctum' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Llistat', content: new OA\JsonContent(ref: '#/components/schemas/GenericCollectionResponse')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Post(
        path: '/solicitud',
        operationId: 'solicitudStore',
        summary: 'Crea un registre de solicitud',
        tags: ['Organitzacio'],
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(required: false, content: new OA\JsonContent(ref: '#/components/schemas/GenericUpsertRequest')),
        responses: [
            new OA\Response(response: 200, description: 'Creat', content: new OA\JsonContent(ref: '#/components/schemas/GenericItemResponse')),
            new OA\Response(response: 422, description: 'Validacio incorrecta', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Get(
        path: '/solicitud/{id}',
        operationId: 'solicitudShow',
        summary: 'Obte el detall de solicitud',
        tags: ['Organitzacio'],
        security: [['sanctum' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        responses: [
            new OA\Response(response: 200, description: 'Detall', content: new OA\JsonContent(ref: '#/components/schemas/GenericItemResponse')),
            new OA\Response(response: 404, description: 'No trobat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Put(
        path: '/solicitud/{id}',
        operationId: 'solicitudUpdate',
        summary: 'Actualitza un registre de solicitud',
        tags: ['Organitzacio'],
        security: [['sanctum' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        requestBody: new OA\RequestBody(required: false, content: new OA\JsonContent(ref: '#/components/schemas/GenericUpsertRequest')),
        responses: [
            new OA\Response(response: 200, description: 'Actualitzat', content: new OA\JsonContent(ref: '#/components/schemas/GenericItemResponse')),
            new OA\Response(response: 404, description: 'No trobat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 422, description: 'Validacio incorrecta', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Delete(
        path: '/solicitud/{id}',
        operationId: 'solicitudDestroy',
        summary: 'Elimina un registre de solicitud',
        tags: ['Organitzacio'],
        security: [['sanctum' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        responses: [
            new OA\Response(response: 200, description: 'Eliminat', content: new OA\JsonContent(ref: '#/components/schemas/ApiSuccess')),
            new OA\Response(response: 404, description: 'No trobat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    public function solicitudResourceEndpoints(): void
    {
    }

    /**
     * Operacions REST de /tipoExpediente (protegides).
     */
    #[OA\Get(
        path: '/tipoExpediente',
        operationId: 'tipoexpedienteIndex',
        summary: 'Llista de registres de tipoExpediente',
        tags: ['Academica'],
        security: [['sanctum' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Llistat', content: new OA\JsonContent(ref: '#/components/schemas/GenericCollectionResponse')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Post(
        path: '/tipoExpediente',
        operationId: 'tipoexpedienteStore',
        summary: 'Crea un registre de tipoExpediente',
        tags: ['Academica'],
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(required: false, content: new OA\JsonContent(ref: '#/components/schemas/GenericUpsertRequest')),
        responses: [
            new OA\Response(response: 200, description: 'Creat', content: new OA\JsonContent(ref: '#/components/schemas/GenericItemResponse')),
            new OA\Response(response: 422, description: 'Validacio incorrecta', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Get(
        path: '/tipoExpediente/{id}',
        operationId: 'tipoexpedienteShow',
        summary: 'Obte el detall de tipoExpediente',
        tags: ['Academica'],
        security: [['sanctum' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        responses: [
            new OA\Response(response: 200, description: 'Detall', content: new OA\JsonContent(ref: '#/components/schemas/GenericItemResponse')),
            new OA\Response(response: 404, description: 'No trobat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Put(
        path: '/tipoExpediente/{id}',
        operationId: 'tipoexpedienteUpdate',
        summary: 'Actualitza un registre de tipoExpediente',
        tags: ['Academica'],
        security: [['sanctum' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        requestBody: new OA\RequestBody(required: false, content: new OA\JsonContent(ref: '#/components/schemas/GenericUpsertRequest')),
        responses: [
            new OA\Response(response: 200, description: 'Actualitzat', content: new OA\JsonContent(ref: '#/components/schemas/GenericItemResponse')),
            new OA\Response(response: 404, description: 'No trobat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 422, description: 'Validacio incorrecta', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Delete(
        path: '/tipoExpediente/{id}',
        operationId: 'tipoexpedienteDestroy',
        summary: 'Elimina un registre de tipoExpediente',
        tags: ['Academica'],
        security: [['sanctum' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        responses: [
            new OA\Response(response: 200, description: 'Eliminat', content: new OA\JsonContent(ref: '#/components/schemas/ApiSuccess')),
            new OA\Response(response: 404, description: 'No trobat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    public function tipoexpedienteResourceEndpoints(): void
    {
    }

    /**
     * Operacions REST de /alumnogrupo (protegides).
     */
    #[OA\Get(
        path: '/alumnogrupo',
        operationId: 'alumnogrupoIndex',
        summary: 'Llista de registres de alumnogrupo',
        tags: ['Alumnat'],
        security: [['sanctum' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Llistat', content: new OA\JsonContent(ref: '#/components/schemas/GenericCollectionResponse')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Post(
        path: '/alumnogrupo',
        operationId: 'alumnogrupoStore',
        summary: 'Crea un registre de alumnogrupo',
        tags: ['Alumnat'],
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(required: false, content: new OA\JsonContent(ref: '#/components/schemas/GenericUpsertRequest')),
        responses: [
            new OA\Response(response: 200, description: 'Creat', content: new OA\JsonContent(ref: '#/components/schemas/GenericItemResponse')),
            new OA\Response(response: 422, description: 'Validacio incorrecta', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Get(
        path: '/alumnogrupo/{id}',
        operationId: 'alumnogrupoShow',
        summary: 'Obte el detall de alumnogrupo',
        tags: ['Alumnat'],
        security: [['sanctum' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        responses: [
            new OA\Response(response: 200, description: 'Detall', content: new OA\JsonContent(ref: '#/components/schemas/GenericItemResponse')),
            new OA\Response(response: 404, description: 'No trobat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Put(
        path: '/alumnogrupo/{id}',
        operationId: 'alumnogrupoUpdate',
        summary: 'Actualitza un registre de alumnogrupo',
        tags: ['Alumnat'],
        security: [['sanctum' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        requestBody: new OA\RequestBody(required: false, content: new OA\JsonContent(ref: '#/components/schemas/GenericUpsertRequest')),
        responses: [
            new OA\Response(response: 200, description: 'Actualitzat', content: new OA\JsonContent(ref: '#/components/schemas/GenericItemResponse')),
            new OA\Response(response: 404, description: 'No trobat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 422, description: 'Validacio incorrecta', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Delete(
        path: '/alumnogrupo/{id}',
        operationId: 'alumnogrupoDestroy',
        summary: 'Elimina un registre de alumnogrupo',
        tags: ['Alumnat'],
        security: [['sanctum' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        responses: [
            new OA\Response(response: 200, description: 'Eliminat', content: new OA\JsonContent(ref: '#/components/schemas/ApiSuccess')),
            new OA\Response(response: 404, description: 'No trobat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    public function alumnogrupoResourceEndpoints(): void
    {
    }

    /**
     * Operacions REST de /activity (protegides).
     */
    #[OA\Get(
        path: '/activity',
        operationId: 'activityIndex',
        summary: 'Llista de registres de activity',
        tags: ['Altres'],
        security: [['sanctum' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Llistat', content: new OA\JsonContent(ref: '#/components/schemas/GenericCollectionResponse')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Post(
        path: '/activity',
        operationId: 'activityStore',
        summary: 'Crea un registre de activity',
        tags: ['Altres'],
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(required: false, content: new OA\JsonContent(ref: '#/components/schemas/GenericUpsertRequest')),
        responses: [
            new OA\Response(response: 200, description: 'Creat', content: new OA\JsonContent(ref: '#/components/schemas/GenericItemResponse')),
            new OA\Response(response: 422, description: 'Validacio incorrecta', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Get(
        path: '/activity/{id}',
        operationId: 'activityShow',
        summary: 'Obte el detall de activity',
        tags: ['Altres'],
        security: [['sanctum' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        responses: [
            new OA\Response(response: 200, description: 'Detall', content: new OA\JsonContent(ref: '#/components/schemas/GenericItemResponse')),
            new OA\Response(response: 404, description: 'No trobat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Put(
        path: '/activity/{id}',
        operationId: 'activityUpdate',
        summary: 'Actualitza un registre de activity',
        tags: ['Altres'],
        security: [['sanctum' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        requestBody: new OA\RequestBody(required: false, content: new OA\JsonContent(ref: '#/components/schemas/GenericUpsertRequest')),
        responses: [
            new OA\Response(response: 200, description: 'Actualitzat', content: new OA\JsonContent(ref: '#/components/schemas/GenericItemResponse')),
            new OA\Response(response: 404, description: 'No trobat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 422, description: 'Validacio incorrecta', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Delete(
        path: '/activity/{id}',
        operationId: 'activityDestroy',
        summary: 'Elimina un registre de activity',
        tags: ['Altres'],
        security: [['sanctum' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        responses: [
            new OA\Response(response: 200, description: 'Eliminat', content: new OA\JsonContent(ref: '#/components/schemas/ApiSuccess')),
            new OA\Response(response: 404, description: 'No trobat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    public function activityResourceEndpoints(): void
    {
    }

    /**
     * Operacions REST de /curso (protegides).
     */
    #[OA\Get(
        path: '/curso',
        operationId: 'cursoIndex',
        summary: 'Llista de registres de curso',
        tags: ['Academica'],
        security: [['sanctum' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Llistat', content: new OA\JsonContent(ref: '#/components/schemas/GenericCollectionResponse')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Post(
        path: '/curso',
        operationId: 'cursoStore',
        summary: 'Crea un registre de curso',
        tags: ['Academica'],
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(required: false, content: new OA\JsonContent(ref: '#/components/schemas/GenericUpsertRequest')),
        responses: [
            new OA\Response(response: 200, description: 'Creat', content: new OA\JsonContent(ref: '#/components/schemas/GenericItemResponse')),
            new OA\Response(response: 422, description: 'Validacio incorrecta', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Get(
        path: '/curso/{id}',
        operationId: 'cursoShow',
        summary: 'Obte el detall de curso',
        tags: ['Academica'],
        security: [['sanctum' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        responses: [
            new OA\Response(response: 200, description: 'Detall', content: new OA\JsonContent(ref: '#/components/schemas/GenericItemResponse')),
            new OA\Response(response: 404, description: 'No trobat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Put(
        path: '/curso/{id}',
        operationId: 'cursoUpdate',
        summary: 'Actualitza un registre de curso',
        tags: ['Academica'],
        security: [['sanctum' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        requestBody: new OA\RequestBody(required: false, content: new OA\JsonContent(ref: '#/components/schemas/GenericUpsertRequest')),
        responses: [
            new OA\Response(response: 200, description: 'Actualitzat', content: new OA\JsonContent(ref: '#/components/schemas/GenericItemResponse')),
            new OA\Response(response: 404, description: 'No trobat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 422, description: 'Validacio incorrecta', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Delete(
        path: '/curso/{id}',
        operationId: 'cursoDestroy',
        summary: 'Elimina un registre de curso',
        tags: ['Academica'],
        security: [['sanctum' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        responses: [
            new OA\Response(response: 200, description: 'Eliminat', content: new OA\JsonContent(ref: '#/components/schemas/ApiSuccess')),
            new OA\Response(response: 404, description: 'No trobat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    public function cursoResourceEndpoints(): void
    {
    }

    /**
     * Operacions REST de /ciclo (protegides).
     */
    #[OA\Get(
        path: '/ciclo',
        operationId: 'cicloIndex',
        summary: 'Llista de registres de ciclo',
        tags: ['Academica'],
        security: [['sanctum' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Llistat', content: new OA\JsonContent(ref: '#/components/schemas/GenericCollectionResponse')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Post(
        path: '/ciclo',
        operationId: 'cicloStore',
        summary: 'Crea un registre de ciclo',
        tags: ['Academica'],
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(required: false, content: new OA\JsonContent(ref: '#/components/schemas/GenericUpsertRequest')),
        responses: [
            new OA\Response(response: 200, description: 'Creat', content: new OA\JsonContent(ref: '#/components/schemas/GenericItemResponse')),
            new OA\Response(response: 422, description: 'Validacio incorrecta', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Get(
        path: '/ciclo/{id}',
        operationId: 'cicloShow',
        summary: 'Obte el detall de ciclo',
        tags: ['Academica'],
        security: [['sanctum' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        responses: [
            new OA\Response(response: 200, description: 'Detall', content: new OA\JsonContent(ref: '#/components/schemas/GenericItemResponse')),
            new OA\Response(response: 404, description: 'No trobat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Put(
        path: '/ciclo/{id}',
        operationId: 'cicloUpdate',
        summary: 'Actualitza un registre de ciclo',
        tags: ['Academica'],
        security: [['sanctum' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        requestBody: new OA\RequestBody(required: false, content: new OA\JsonContent(ref: '#/components/schemas/GenericUpsertRequest')),
        responses: [
            new OA\Response(response: 200, description: 'Actualitzat', content: new OA\JsonContent(ref: '#/components/schemas/GenericItemResponse')),
            new OA\Response(response: 404, description: 'No trobat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 422, description: 'Validacio incorrecta', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Delete(
        path: '/ciclo/{id}',
        operationId: 'cicloDestroy',
        summary: 'Elimina un registre de ciclo',
        tags: ['Academica'],
        security: [['sanctum' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        responses: [
            new OA\Response(response: 200, description: 'Eliminat', content: new OA\JsonContent(ref: '#/components/schemas/ApiSuccess')),
            new OA\Response(response: 404, description: 'No trobat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    public function cicloResourceEndpoints(): void
    {
    }

    /**
     * Operacions REST de /task (protegides).
     */
    #[OA\Get(
        path: '/task',
        operationId: 'taskIndex',
        summary: 'Llista de registres de task',
        tags: ['Organitzacio'],
        security: [['sanctum' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Llistat', content: new OA\JsonContent(ref: '#/components/schemas/GenericCollectionResponse')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Post(
        path: '/task',
        operationId: 'taskStore',
        summary: 'Crea un registre de task',
        tags: ['Organitzacio'],
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(required: false, content: new OA\JsonContent(ref: '#/components/schemas/GenericUpsertRequest')),
        responses: [
            new OA\Response(response: 200, description: 'Creat', content: new OA\JsonContent(ref: '#/components/schemas/GenericItemResponse')),
            new OA\Response(response: 422, description: 'Validacio incorrecta', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Get(
        path: '/task/{id}',
        operationId: 'taskShow',
        summary: 'Obte el detall de task',
        tags: ['Organitzacio'],
        security: [['sanctum' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        responses: [
            new OA\Response(response: 200, description: 'Detall', content: new OA\JsonContent(ref: '#/components/schemas/GenericItemResponse')),
            new OA\Response(response: 404, description: 'No trobat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Put(
        path: '/task/{id}',
        operationId: 'taskUpdate',
        summary: 'Actualitza un registre de task',
        tags: ['Organitzacio'],
        security: [['sanctum' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        requestBody: new OA\RequestBody(required: false, content: new OA\JsonContent(ref: '#/components/schemas/GenericUpsertRequest')),
        responses: [
            new OA\Response(response: 200, description: 'Actualitzat', content: new OA\JsonContent(ref: '#/components/schemas/GenericItemResponse')),
            new OA\Response(response: 404, description: 'No trobat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 422, description: 'Validacio incorrecta', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Delete(
        path: '/task/{id}',
        operationId: 'taskDestroy',
        summary: 'Elimina un registre de task',
        tags: ['Organitzacio'],
        security: [['sanctum' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        responses: [
            new OA\Response(response: 200, description: 'Eliminat', content: new OA\JsonContent(ref: '#/components/schemas/ApiSuccess')),
            new OA\Response(response: 404, description: 'No trobat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    public function taskResourceEndpoints(): void
    {
    }

    /**
     * Operacions REST de /horario (protegides).
     */
    #[OA\Get(
        path: '/horario',
        operationId: 'horarioIndex',
        summary: 'Llista de registres de horario',
        tags: ['Horaris'],
        security: [['sanctum' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Llistat', content: new OA\JsonContent(ref: '#/components/schemas/HorarioCollectionResponse')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Post(
        path: '/horario',
        operationId: 'horarioStore',
        summary: 'Crea un registre de horario',
        tags: ['Horaris'],
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(required: false, content: new OA\JsonContent(ref: '#/components/schemas/HorarioUpsertRequest')),
        responses: [
            new OA\Response(response: 200, description: 'Creat', content: new OA\JsonContent(ref: '#/components/schemas/HorarioItemResponse')),
            new OA\Response(response: 422, description: 'Validacio incorrecta', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Get(
        path: '/horario/{id}',
        operationId: 'horarioShow',
        summary: 'Obte el detall de horario',
        tags: ['Horaris'],
        security: [['sanctum' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        responses: [
            new OA\Response(response: 200, description: 'Detall', content: new OA\JsonContent(ref: '#/components/schemas/HorarioItemResponse')),
            new OA\Response(response: 404, description: 'No trobat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Put(
        path: '/horario/{id}',
        operationId: 'horarioUpdate',
        summary: 'Actualitza un registre de horario',
        tags: ['Horaris'],
        security: [['sanctum' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        requestBody: new OA\RequestBody(required: false, content: new OA\JsonContent(ref: '#/components/schemas/HorarioUpsertRequest')),
        responses: [
            new OA\Response(response: 200, description: 'Actualitzat', content: new OA\JsonContent(ref: '#/components/schemas/HorarioItemResponse')),
            new OA\Response(response: 404, description: 'No trobat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 422, description: 'Validacio incorrecta', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Delete(
        path: '/horario/{id}',
        operationId: 'horarioDestroy',
        summary: 'Elimina un registre de horario',
        tags: ['Horaris'],
        security: [['sanctum' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        responses: [
            new OA\Response(response: 200, description: 'Eliminat', content: new OA\JsonContent(ref: '#/components/schemas/ApiSuccess')),
            new OA\Response(response: 404, description: 'No trobat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    public function horarioResourceEndpoints(): void
    {
    }

    /**
     * Operacions REST de /hora (protegides).
     */
    #[OA\Get(
        path: '/hora',
        operationId: 'horaIndex',
        summary: 'Llista de registres de hora',
        tags: ['Horaris'],
        security: [['sanctum' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Llistat', content: new OA\JsonContent(ref: '#/components/schemas/GenericCollectionResponse')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Post(
        path: '/hora',
        operationId: 'horaStore',
        summary: 'Crea un registre de hora',
        tags: ['Horaris'],
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(required: false, content: new OA\JsonContent(ref: '#/components/schemas/GenericUpsertRequest')),
        responses: [
            new OA\Response(response: 200, description: 'Creat', content: new OA\JsonContent(ref: '#/components/schemas/GenericItemResponse')),
            new OA\Response(response: 422, description: 'Validacio incorrecta', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Get(
        path: '/hora/{id}',
        operationId: 'horaShow',
        summary: 'Obte el detall de hora',
        tags: ['Horaris'],
        security: [['sanctum' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        responses: [
            new OA\Response(response: 200, description: 'Detall', content: new OA\JsonContent(ref: '#/components/schemas/GenericItemResponse')),
            new OA\Response(response: 404, description: 'No trobat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Put(
        path: '/hora/{id}',
        operationId: 'horaUpdate',
        summary: 'Actualitza un registre de hora',
        tags: ['Horaris'],
        security: [['sanctum' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        requestBody: new OA\RequestBody(required: false, content: new OA\JsonContent(ref: '#/components/schemas/GenericUpsertRequest')),
        responses: [
            new OA\Response(response: 200, description: 'Actualitzat', content: new OA\JsonContent(ref: '#/components/schemas/GenericItemResponse')),
            new OA\Response(response: 404, description: 'No trobat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 422, description: 'Validacio incorrecta', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Delete(
        path: '/hora/{id}',
        operationId: 'horaDestroy',
        summary: 'Elimina un registre de hora',
        tags: ['Horaris'],
        security: [['sanctum' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        responses: [
            new OA\Response(response: 200, description: 'Eliminat', content: new OA\JsonContent(ref: '#/components/schemas/ApiSuccess')),
            new OA\Response(response: 404, description: 'No trobat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    public function horaResourceEndpoints(): void
    {
    }

    /**
     * Operacions REST de /alumnoresultado (protegides).
     */
    #[OA\Get(
        path: '/alumnoresultado',
        operationId: 'alumnoresultadoIndex',
        summary: 'Llista de registres de alumnoresultado',
        tags: ['Alumnat'],
        security: [['sanctum' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Llistat', content: new OA\JsonContent(ref: '#/components/schemas/GenericCollectionResponse')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Post(
        path: '/alumnoresultado',
        operationId: 'alumnoresultadoStore',
        summary: 'Crea un registre de alumnoresultado',
        tags: ['Alumnat'],
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(required: false, content: new OA\JsonContent(ref: '#/components/schemas/GenericUpsertRequest')),
        responses: [
            new OA\Response(response: 200, description: 'Creat', content: new OA\JsonContent(ref: '#/components/schemas/GenericItemResponse')),
            new OA\Response(response: 422, description: 'Validacio incorrecta', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Get(
        path: '/alumnoresultado/{id}',
        operationId: 'alumnoresultadoShow',
        summary: 'Obte el detall de alumnoresultado',
        tags: ['Alumnat'],
        security: [['sanctum' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        responses: [
            new OA\Response(response: 200, description: 'Detall', content: new OA\JsonContent(ref: '#/components/schemas/GenericItemResponse')),
            new OA\Response(response: 404, description: 'No trobat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Put(
        path: '/alumnoresultado/{id}',
        operationId: 'alumnoresultadoUpdate',
        summary: 'Actualitza un registre de alumnoresultado',
        tags: ['Alumnat'],
        security: [['sanctum' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        requestBody: new OA\RequestBody(required: false, content: new OA\JsonContent(ref: '#/components/schemas/GenericUpsertRequest')),
        responses: [
            new OA\Response(response: 200, description: 'Actualitzat', content: new OA\JsonContent(ref: '#/components/schemas/GenericItemResponse')),
            new OA\Response(response: 404, description: 'No trobat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 422, description: 'Validacio incorrecta', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Delete(
        path: '/alumnoresultado/{id}',
        operationId: 'alumnoresultadoDestroy',
        summary: 'Elimina un registre de alumnoresultado',
        tags: ['Alumnat'],
        security: [['sanctum' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        responses: [
            new OA\Response(response: 200, description: 'Eliminat', content: new OA\JsonContent(ref: '#/components/schemas/ApiSuccess')),
            new OA\Response(response: 404, description: 'No trobat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    public function alumnoresultadoResourceEndpoints(): void
    {
    }

    /**
     * Operacions REST de /lote (protegides).
     */
    #[OA\Get(
        path: '/lote',
        operationId: 'loteIndex',
        summary: 'Llista de registres de lote',
        tags: ['Materials'],
        security: [['sanctum' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Llistat', content: new OA\JsonContent(ref: '#/components/schemas/GenericCollectionResponse')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Post(
        path: '/lote',
        operationId: 'loteStore',
        summary: 'Crea un registre de lote',
        tags: ['Materials'],
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(required: false, content: new OA\JsonContent(ref: '#/components/schemas/GenericUpsertRequest')),
        responses: [
            new OA\Response(response: 200, description: 'Creat', content: new OA\JsonContent(ref: '#/components/schemas/GenericItemResponse')),
            new OA\Response(response: 422, description: 'Validacio incorrecta', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Get(
        path: '/lote/{id}',
        operationId: 'loteShow',
        summary: 'Obte el detall de lote',
        tags: ['Materials'],
        security: [['sanctum' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        responses: [
            new OA\Response(response: 200, description: 'Detall', content: new OA\JsonContent(ref: '#/components/schemas/GenericItemResponse')),
            new OA\Response(response: 404, description: 'No trobat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Put(
        path: '/lote/{id}',
        operationId: 'loteUpdate',
        summary: 'Actualitza un registre de lote',
        tags: ['Materials'],
        security: [['sanctum' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        requestBody: new OA\RequestBody(required: false, content: new OA\JsonContent(ref: '#/components/schemas/GenericUpsertRequest')),
        responses: [
            new OA\Response(response: 200, description: 'Actualitzat', content: new OA\JsonContent(ref: '#/components/schemas/GenericItemResponse')),
            new OA\Response(response: 404, description: 'No trobat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 422, description: 'Validacio incorrecta', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Delete(
        path: '/lote/{id}',
        operationId: 'loteDestroy',
        summary: 'Elimina un registre de lote',
        tags: ['Materials'],
        security: [['sanctum' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        responses: [
            new OA\Response(response: 200, description: 'Eliminat', content: new OA\JsonContent(ref: '#/components/schemas/ApiSuccess')),
            new OA\Response(response: 404, description: 'No trobat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    public function loteResourceEndpoints(): void
    {
    }

    /**
     * Operacions REST de /articuloLote (protegides).
     */
    #[OA\Get(
        path: '/articuloLote',
        operationId: 'articuloloteIndex',
        summary: 'Llista de registres de articuloLote',
        tags: ['Materials'],
        security: [['sanctum' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Llistat', content: new OA\JsonContent(ref: '#/components/schemas/GenericCollectionResponse')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Post(
        path: '/articuloLote',
        operationId: 'articuloloteStore',
        summary: 'Crea un registre de articuloLote',
        tags: ['Materials'],
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(required: false, content: new OA\JsonContent(ref: '#/components/schemas/GenericUpsertRequest')),
        responses: [
            new OA\Response(response: 200, description: 'Creat', content: new OA\JsonContent(ref: '#/components/schemas/GenericItemResponse')),
            new OA\Response(response: 422, description: 'Validacio incorrecta', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Get(
        path: '/articuloLote/{id}',
        operationId: 'articuloloteShow',
        summary: 'Obte el detall de articuloLote',
        tags: ['Materials'],
        security: [['sanctum' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        responses: [
            new OA\Response(response: 200, description: 'Detall', content: new OA\JsonContent(ref: '#/components/schemas/GenericItemResponse')),
            new OA\Response(response: 404, description: 'No trobat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Put(
        path: '/articuloLote/{id}',
        operationId: 'articuloloteUpdate',
        summary: 'Actualitza un registre de articuloLote',
        tags: ['Materials'],
        security: [['sanctum' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        requestBody: new OA\RequestBody(required: false, content: new OA\JsonContent(ref: '#/components/schemas/GenericUpsertRequest')),
        responses: [
            new OA\Response(response: 200, description: 'Actualitzat', content: new OA\JsonContent(ref: '#/components/schemas/GenericItemResponse')),
            new OA\Response(response: 404, description: 'No trobat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 422, description: 'Validacio incorrecta', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Delete(
        path: '/articuloLote/{id}',
        operationId: 'articuloloteDestroy',
        summary: 'Elimina un registre de articuloLote',
        tags: ['Materials'],
        security: [['sanctum' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        responses: [
            new OA\Response(response: 200, description: 'Eliminat', content: new OA\JsonContent(ref: '#/components/schemas/ApiSuccess')),
            new OA\Response(response: 404, description: 'No trobat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    public function articuloloteResourceEndpoints(): void
    {
    }

    /**
     * Operacions REST de /articulo (protegides).
     */
    #[OA\Get(
        path: '/articulo',
        operationId: 'articuloIndex',
        summary: 'Llista de registres de articulo',
        tags: ['Materials'],
        security: [['sanctum' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Llistat', content: new OA\JsonContent(ref: '#/components/schemas/GenericCollectionResponse')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Post(
        path: '/articulo',
        operationId: 'articuloStore',
        summary: 'Crea un registre de articulo',
        tags: ['Materials'],
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(required: false, content: new OA\JsonContent(ref: '#/components/schemas/GenericUpsertRequest')),
        responses: [
            new OA\Response(response: 200, description: 'Creat', content: new OA\JsonContent(ref: '#/components/schemas/GenericItemResponse')),
            new OA\Response(response: 422, description: 'Validacio incorrecta', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Get(
        path: '/articulo/{id}',
        operationId: 'articuloShow',
        summary: 'Obte el detall de articulo',
        tags: ['Materials'],
        security: [['sanctum' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        responses: [
            new OA\Response(response: 200, description: 'Detall', content: new OA\JsonContent(ref: '#/components/schemas/GenericItemResponse')),
            new OA\Response(response: 404, description: 'No trobat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Put(
        path: '/articulo/{id}',
        operationId: 'articuloUpdate',
        summary: 'Actualitza un registre de articulo',
        tags: ['Materials'],
        security: [['sanctum' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        requestBody: new OA\RequestBody(required: false, content: new OA\JsonContent(ref: '#/components/schemas/GenericUpsertRequest')),
        responses: [
            new OA\Response(response: 200, description: 'Actualitzat', content: new OA\JsonContent(ref: '#/components/schemas/GenericItemResponse')),
            new OA\Response(response: 404, description: 'No trobat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 422, description: 'Validacio incorrecta', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Delete(
        path: '/articulo/{id}',
        operationId: 'articuloDestroy',
        summary: 'Elimina un registre de articulo',
        tags: ['Materials'],
        security: [['sanctum' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        responses: [
            new OA\Response(response: 200, description: 'Eliminat', content: new OA\JsonContent(ref: '#/components/schemas/ApiSuccess')),
            new OA\Response(response: 404, description: 'No trobat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    public function articuloResourceEndpoints(): void
    {
    }

    /**
     * Operacions REST de /cotxe (protegides).
     */
    #[OA\Get(
        path: '/cotxe',
        operationId: 'cotxeIndex',
        summary: 'Llista de registres de cotxe',
        tags: ['Vehicles'],
        security: [['sanctum' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Llistat', content: new OA\JsonContent(ref: '#/components/schemas/GenericCollectionResponse')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Post(
        path: '/cotxe',
        operationId: 'cotxeStore',
        summary: 'Crea un registre de cotxe',
        tags: ['Vehicles'],
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(required: false, content: new OA\JsonContent(ref: '#/components/schemas/GenericUpsertRequest')),
        responses: [
            new OA\Response(response: 200, description: 'Creat', content: new OA\JsonContent(ref: '#/components/schemas/GenericItemResponse')),
            new OA\Response(response: 422, description: 'Validacio incorrecta', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Get(
        path: '/cotxe/{id}',
        operationId: 'cotxeShow',
        summary: 'Obte el detall de cotxe',
        tags: ['Vehicles'],
        security: [['sanctum' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        responses: [
            new OA\Response(response: 200, description: 'Detall', content: new OA\JsonContent(ref: '#/components/schemas/GenericItemResponse')),
            new OA\Response(response: 404, description: 'No trobat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Put(
        path: '/cotxe/{id}',
        operationId: 'cotxeUpdate',
        summary: 'Actualitza un registre de cotxe',
        tags: ['Vehicles'],
        security: [['sanctum' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        requestBody: new OA\RequestBody(required: false, content: new OA\JsonContent(ref: '#/components/schemas/GenericUpsertRequest')),
        responses: [
            new OA\Response(response: 200, description: 'Actualitzat', content: new OA\JsonContent(ref: '#/components/schemas/GenericItemResponse')),
            new OA\Response(response: 404, description: 'No trobat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 422, description: 'Validacio incorrecta', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Delete(
        path: '/cotxe/{id}',
        operationId: 'cotxeDestroy',
        summary: 'Elimina un registre de cotxe',
        tags: ['Vehicles'],
        security: [['sanctum' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        responses: [
            new OA\Response(response: 200, description: 'Eliminat', content: new OA\JsonContent(ref: '#/components/schemas/ApiSuccess')),
            new OA\Response(response: 404, description: 'No trobat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    public function cotxeResourceEndpoints(): void
    {
    }

    /**
     * Operacions REST de /tipoactividad (protegides).
     */
    #[OA\Get(
        path: '/tipoactividad',
        operationId: 'tipoactividadIndex',
        summary: 'Llista de registres de tipoactividad',
        tags: ['Activitats'],
        security: [['sanctum' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Llistat', content: new OA\JsonContent(ref: '#/components/schemas/GenericCollectionResponse')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Post(
        path: '/tipoactividad',
        operationId: 'tipoactividadStore',
        summary: 'Crea un registre de tipoactividad',
        tags: ['Activitats'],
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(required: false, content: new OA\JsonContent(ref: '#/components/schemas/GenericUpsertRequest')),
        responses: [
            new OA\Response(response: 200, description: 'Creat', content: new OA\JsonContent(ref: '#/components/schemas/GenericItemResponse')),
            new OA\Response(response: 422, description: 'Validacio incorrecta', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Get(
        path: '/tipoactividad/{id}',
        operationId: 'tipoactividadShow',
        summary: 'Obte el detall de tipoactividad',
        tags: ['Activitats'],
        security: [['sanctum' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        responses: [
            new OA\Response(response: 200, description: 'Detall', content: new OA\JsonContent(ref: '#/components/schemas/GenericItemResponse')),
            new OA\Response(response: 404, description: 'No trobat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Put(
        path: '/tipoactividad/{id}',
        operationId: 'tipoactividadUpdate',
        summary: 'Actualitza un registre de tipoactividad',
        tags: ['Activitats'],
        security: [['sanctum' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        requestBody: new OA\RequestBody(required: false, content: new OA\JsonContent(ref: '#/components/schemas/GenericUpsertRequest')),
        responses: [
            new OA\Response(response: 200, description: 'Actualitzat', content: new OA\JsonContent(ref: '#/components/schemas/GenericItemResponse')),
            new OA\Response(response: 404, description: 'No trobat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 422, description: 'Validacio incorrecta', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    #[OA\Delete(
        path: '/tipoactividad/{id}',
        operationId: 'tipoactividadDestroy',
        summary: 'Elimina un registre de tipoactividad',
        tags: ['Activitats'],
        security: [['sanctum' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        responses: [
            new OA\Response(response: 200, description: 'Eliminat', content: new OA\JsonContent(ref: '#/components/schemas/ApiSuccess')),
            new OA\Response(response: 404, description: 'No trobat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    public function tipoactividadResourceEndpoints(): void
    {
    }
}
