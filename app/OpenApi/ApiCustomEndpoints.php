<?php

namespace Intranet\OpenApi;

use OpenApi\Attributes as OA;

/**
 * Documentacio OpenAPI per a endpoints custom (no REST resource) definits en routes/api.php.
 */
class ApiCustomEndpoints
{

    /**
     * Endpoint custom: GET /alumnofct/{grupo}/grupo
     */
    #[OA\Get(
        path: '/alumnofct/{grupo}/grupo',
        operationId: 'alumnofct_grupo_grupo_get',
        summary: 'Llista alumnes FCT d un grup',
        tags: ['FCT (Public)'],

        parameters: [
            new OA\Parameter(name: 'grupo', in: 'path', required: true, schema: new OA\Schema(type: 'string')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'OK', content: new OA\JsonContent(ref: '#/components/schemas/ApiSuccess')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 422, description: 'Validacio incorrecta', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
        ]
    )]
    public function alumnofct_grupo_grupo_get(): void
    {
    }

    /**
     * Endpoint custom: GET /convenio
     */
    #[OA\Get(
        path: '/convenio',
        operationId: 'convenio_get',
        summary: 'Llista convenis disponibles',
        tags: ['FCT (Public)'],


        responses: [
            new OA\Response(response: 200, description: 'OK', content: new OA\JsonContent(ref: '#/components/schemas/ApiSuccess')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 422, description: 'Validacio incorrecta', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
        ]
    )]
    public function convenio_get(): void
    {
    }

    /**
     * Endpoint custom: GET /miIp
     */
    #[OA\Get(
        path: '/miIp',
        operationId: 'miIp_get',
        summary: 'IP client detectada pel servidor',
        tags: ['Sistema (Public)'],


        responses: [
            new OA\Response(response: 200, description: 'IP client', content: new OA\JsonContent(ref: '#/components/schemas/MiIpResponse')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 422, description: 'Validacio incorrecta', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
        ]
    )]
    public function miIp_get(): void
    {
    }

    /**
     * Endpoint custom: GET /actividad/{actividad}/getFiles
     */
    #[OA\Get(
        path: '/actividad/{actividad}/getFiles',
        operationId: 'actividad_actividad_getFiles_get',
        summary: 'Obte fitxers associats a una activitat',
        tags: ['Activitats (Public)'],

        parameters: [
            new OA\Parameter(name: 'actividad', in: 'path', required: true, schema: new OA\Schema(type: 'string')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'OK', content: new OA\JsonContent(ref: '#/components/schemas/ApiSuccess')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 422, description: 'Validacio incorrecta', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
        ]
    )]
    public function actividad_actividad_getFiles_get(): void
    {
    }

    /**
     * Endpoint custom: GET /server-time
     */
    #[OA\Get(
        path: '/server-time',
        operationId: 'server_time_get',
        summary: 'Data i hora actual del servidor',
        tags: ['Guardies (Public)'],


        responses: [
            new OA\Response(response: 200, description: 'Data/hora servidor', content: new OA\JsonContent(ref: '#/components/schemas/ServerTimeResponse')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 422, description: 'Validacio incorrecta', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
        ]
    )]
    public function server_time_get(): void
    {
    }

    /**
     * Endpoint custom: GET /porta/obrir
     */
    #[OA\Get(
        path: '/porta/obrir',
        operationId: 'porta_obrir_get',
        summary: 'Obri la porta en mode prova',
        tags: ['Vehicles (Public)'],


        responses: [
            new OA\Response(response: 200, description: 'OK', content: new OA\JsonContent(ref: '#/components/schemas/ApiSuccess')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 422, description: 'Validacio incorrecta', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
        ]
    )]
    public function porta_obrir_get(): void
    {
    }

    /**
     * Endpoint custom: POST /porta/obrir-automatica
     */
    #[OA\Post(
        path: '/porta/obrir-automatica',
        operationId: 'porta_obrir_automatica_post',
        summary: 'Executa obertura automatica de porta',
        tags: ['Vehicles (Public)'],


        requestBody: new OA\RequestBody(required: false, content: new OA\JsonContent(type: 'object')),
        responses: [
            new OA\Response(response: 200, description: 'OK', content: new OA\JsonContent(ref: '#/components/schemas/ApiSuccess')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 422, description: 'Validacio incorrecta', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
        ]
    )]
    public function porta_obrir_automatica_post(): void
    {
    }

    /**
     * Endpoint custom: POST /eventPortaSortida
     */
    #[OA\Post(
        path: '/eventPortaSortida',
        operationId: 'eventPortaSortida_post',
        summary: 'Registra event de porta d eixida',
        tags: ['Vehicles (Public)'],


        requestBody: new OA\RequestBody(required: false, content: new OA\JsonContent(type: 'object')),
        responses: [
            new OA\Response(response: 200, description: 'OK', content: new OA\JsonContent(ref: '#/components/schemas/ApiSuccess')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 422, description: 'Validacio incorrecta', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
        ]
    )]
    public function eventPortaSortida_post(): void
    {
    }

    /**
     * Endpoint custom: POST /eventPorta
     */
    #[OA\Post(
        path: '/eventPorta',
        operationId: 'eventPorta_post',
        summary: 'Registra event de porta d entrada',
        tags: ['Vehicles (Public)'],


        requestBody: new OA\RequestBody(required: false, content: new OA\JsonContent(type: 'object')),
        responses: [
            new OA\Response(response: 200, description: 'OK', content: new OA\JsonContent(ref: '#/components/schemas/ApiSuccess')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 422, description: 'Validacio incorrecta', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
        ]
    )]
    public function eventPorta_post(): void
    {
    }

    /**
     * Endpoint custom: GET /presencia/resumen-rango
     */
    #[OA\Get(
        path: '/presencia/resumen-rango',
        operationId: 'presencia_resumen_rango_get',
        summary: 'Resum de presencia per professor en un rang de dates',
        tags: ['Guardies (Public)'],
        parameters: [
            new OA\Parameter(name: 'desde', in: 'query', required: true, schema: new OA\Schema(type: 'string', format: 'date')),
            new OA\Parameter(name: 'hasta', in: 'query', required: true, schema: new OA\Schema(type: 'string', format: 'date')),
            new OA\Parameter(name: 'dni', in: 'query', required: false, schema: new OA\Schema(type: 'string')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Resum de presencia', content: new OA\JsonContent(ref: '#/components/schemas/PresenciaResumenRangoResponse')),
            new OA\Response(response: 422, description: 'Falten parametres o format invalid', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 500, description: 'Error intern', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    public function presencia_resumen_rango_get(): void
    {
    }

    /**
     * Endpoint custom: GET /grupo/list/{id}
     */
    #[OA\Get(
        path: '/grupo/list/{id}',
        operationId: 'grupo_list_id_get',
        summary: 'Llistat d alumnes del grup',
        tags: ['Organitzacio'],
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Alumnes del grup', content: new OA\JsonContent(ref: '#/components/schemas/GrupoListResponse')),
            new OA\Response(response: 404, description: 'Grup no trobat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 422, description: 'Validacio incorrecta', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
            new OA\Response(response: 500, description: 'Error intern', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    public function grupo_list_id_get(): void
    {
    }

    /**
     * Endpoint custom: GET /alumnofct/{grupo}/dual
     */
    #[OA\Get(
        path: '/alumnofct/{grupo}/dual',
        operationId: 'alumnofct_grupo_dual_get',
        summary: 'Llista alumnes FCT dual d un grup',
        tags: ['FCT'],
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'grupo', in: 'path', required: true, schema: new OA\Schema(type: 'string')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'OK', content: new OA\JsonContent(ref: '#/components/schemas/ApiSuccess')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 422, description: 'Validacio incorrecta', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
            new OA\Response(response: 500, description: 'Error intern', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    public function alumnofct_grupo_dual_get(): void
    {
    }

    /**
     * Endpoint custom: GET /fct/{id}/alFct
     */
    #[OA\Get(
        path: '/fct/{id}/alFct',
        operationId: 'fct_id_alFct_get',
        summary: 'Obte seguiment FCT per identificador',
        tags: ['FCT'],
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'OK', content: new OA\JsonContent(ref: '#/components/schemas/ApiSuccess')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 422, description: 'Validacio incorrecta', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
            new OA\Response(response: 500, description: 'Error intern', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    public function fct_id_alFct_get(): void
    {
    }

    /**
     * Endpoint custom: POST /fct/{id}/alFct
     */
    #[OA\Post(
        path: '/fct/{id}/alFct',
        operationId: 'fct_id_alFct_post',
        summary: 'Guarda seguiment FCT per identificador',
        tags: ['FCT'],
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string')),
        ],
        requestBody: new OA\RequestBody(required: false, content: new OA\JsonContent(type: 'object')),
        responses: [
            new OA\Response(response: 200, description: 'OK', content: new OA\JsonContent(ref: '#/components/schemas/ApiSuccess')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 422, description: 'Validacio incorrecta', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
            new OA\Response(response: 500, description: 'Error intern', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    public function fct_id_alFct_post(): void
    {
    }

    /**
     * Endpoint custom: PUT /comision/{dni}/prePay
     */
    #[OA\Put(
        path: '/comision/{dni}/prePay',
        operationId: 'comision_dni_prePay_put',
        summary: 'Marca pre-pagament d una comissio',
        tags: ['Comissions'],
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'dni', in: 'path', required: true, schema: new OA\Schema(type: 'string')),
        ],
        requestBody: new OA\RequestBody(required: false, content: new OA\JsonContent(type: 'object')),
        responses: [
            new OA\Response(response: 200, description: 'OK', content: new OA\JsonContent(ref: '#/components/schemas/ApiSuccess')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 422, description: 'Validacio incorrecta', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
            new OA\Response(response: 500, description: 'Error intern', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    public function comision_dni_prePay_put(): void
    {
    }

    /**
     * Endpoint custom: GET /autorizar/comision
     */
    #[OA\Get(
        path: '/autorizar/comision',
        operationId: 'autorizar_comision_get',
        summary: 'Obte comissions pendents d autoritzacio',
        tags: ['Comissions'],
        security: [['sanctum' => []]],

        responses: [
            new OA\Response(response: 200, description: 'OK', content: new OA\JsonContent(ref: '#/components/schemas/ApiSuccess')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 422, description: 'Validacio incorrecta', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
        ]
    )]
    public function autorizar_comision_get(): void
    {
    }

    /**
     * Endpoint custom: GET /notification/{id}
     */
    #[OA\Get(
        path: '/notification/{id}',
        operationId: 'notification_id_get',
        summary: 'Marca una notificacio com llegida',
        tags: ['Professorat'],
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'OK', content: new OA\JsonContent(ref: '#/components/schemas/ApiSuccess')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 422, description: 'Validacio incorrecta', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
        ]
    )]
    public function notification_id_get(): void
    {
    }

    /**
     * Endpoint custom: GET /profesor/{dni}/rol
     */
    #[OA\Get(
        path: '/profesor/{dni}/rol',
        operationId: 'profesor_dni_rol_get',
        summary: 'Obte el rol d un professor',
        tags: ['Professorat'],
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'dni', in: 'path', required: true, schema: new OA\Schema(type: 'string')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'OK', content: new OA\JsonContent(ref: '#/components/schemas/ApiSuccess')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 422, description: 'Validacio incorrecta', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
        ]
    )]
    public function profesor_dni_rol_get(): void
    {
    }

    /**
     * Endpoint custom: GET /profesor/rol/{rol}
     */
    #[OA\Get(
        path: '/profesor/rol/{rol}',
        operationId: 'profesor_rol_rol_get',
        summary: 'Llista professors per rol',
        tags: ['Professorat'],
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'rol', in: 'path', required: true, schema: new OA\Schema(type: 'string')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'OK', content: new OA\JsonContent(ref: '#/components/schemas/ApiSuccess')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 422, description: 'Validacio incorrecta', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
        ]
    )]
    public function profesor_rol_rol_get(): void
    {
    }

    /**
     * Endpoint custom: GET /doficha
     */
    #[OA\Get(
        path: '/doficha',
        operationId: 'doficha_get',
        summary: 'Executa fitxatge del professor autenticat',
        tags: ['Professorat'],
        security: [['sanctum' => []]],

        responses: [
            new OA\Response(response: 200, description: 'OK', content: new OA\JsonContent(ref: '#/components/schemas/ApiSuccess')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 422, description: 'Validacio incorrecta', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
        ]
    )]
    public function doficha_get(): void
    {
    }

    /**
     * Endpoint custom: GET /ipGuardias
     */
    #[OA\Get(
        path: '/ipGuardias',
        operationId: 'ipGuardias_get',
        summary: 'Llista IPs autoritzades de guardia',
        tags: ['Professorat'],
        security: [['sanctum' => []]],

        responses: [
            new OA\Response(response: 200, description: 'OK', content: new OA\JsonContent(ref: '#/components/schemas/ApiSuccess')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 422, description: 'Validacio incorrecta', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
        ]
    )]
    public function ipGuardias_get(): void
    {
    }

    /**
     * Endpoint custom: GET /verficha
     */
    #[OA\Get(
        path: '/verficha',
        operationId: 'verficha_get',
        summary: 'Consulta fitxatges per rang',
        tags: ['Professorat'],
        security: [['sanctum' => []]],

        responses: [
            new OA\Response(response: 200, description: 'OK', content: new OA\JsonContent(ref: '#/components/schemas/ApiSuccess')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 422, description: 'Validacio incorrecta', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
        ]
    )]
    public function verficha_get(): void
    {
    }

    /**
     * Endpoint custom: GET /itaca/{dia}/{idProfesor}
     */
    #[OA\Get(
        path: '/itaca/{dia}/{idProfesor}',
        operationId: 'itaca_dia_idProfesor_get',
        summary: 'Hores potencials ITACA per dia i professor',
        tags: ['Professorat'],
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'dia', in: 'path', required: true, schema: new OA\Schema(type: 'string', format: 'date')),
            new OA\Parameter(name: 'idProfesor', in: 'path', required: true, schema: new OA\Schema(type: 'string')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Hores potencials', content: new OA\JsonContent(ref: '#/components/schemas/ItacaPotencialResponse')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 422, description: 'Validacio incorrecta', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
            new OA\Response(response: 500, description: 'Error intern', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    public function itaca_dia_idProfesor_get(): void
    {
    }

    /**
     * Endpoint custom: POST /itaca
     */
    #[OA\Post(
        path: '/itaca',
        operationId: 'itaca_post',
        summary: 'Guarda o actualitza faltes ITACA',
        tags: ['Professorat'],
        security: [['sanctum' => []]],

        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/ItacaGuardarRequest')),
        responses: [
            new OA\Response(response: 200, description: 'Resultat per sessio', content: new OA\JsonContent(ref: '#/components/schemas/ItacaGuardarResponse')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 422, description: 'Validacio incorrecta', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
            new OA\Response(response: 500, description: 'Error intern', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    public function itaca_post(): void
    {
    }

    /**
     * Endpoint custom: GET /aula
     */
    #[OA\Get(
        path: '/aula',
        operationId: 'aula_get',
        summary: 'Consulta dades d aula',
        tags: ['Reserves'],
        security: [['sanctum' => []]],

        responses: [
            new OA\Response(response: 200, description: 'OK', content: new OA\JsonContent(ref: '#/components/schemas/ApiSuccess')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 422, description: 'Validacio incorrecta', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
        ]
    )]
    public function aula_get(): void
    {
    }

    /**
     * Endpoint custom: GET /faltaProfesor/horas/{condicion}
     */
    #[OA\Get(
        path: '/faltaProfesor/horas/{condicion}',
        operationId: 'faltaProfesor_horas_condicion_get',
        summary: 'Llista hores de falta per condicio',
        tags: ['Incidencies'],
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'condicion', in: 'path', required: true, schema: new OA\Schema(type: 'string')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'OK', content: new OA\JsonContent(ref: '#/components/schemas/ApiSuccess')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 422, description: 'Validacio incorrecta', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
        ]
    )]
    public function faltaProfesor_horas_condicion_get(): void
    {
    }

    /**
     * Endpoint custom: PUT /material/cambiarUbicacion/
     */
    #[OA\Put(
        path: '/material/cambiarUbicacion/',
        operationId: 'material_cambiarUbicacion_put',
        summary: 'Canvia la ubicacio d un material',
        tags: ['Materials'],
        security: [['sanctum' => []]],

        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/MaterialCambiarUbicacionRequest')),
        responses: [
            new OA\Response(response: 200, description: 'OK', content: new OA\JsonContent(ref: '#/components/schemas/ApiSuccess')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 422, description: 'Validacio incorrecta', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
        ]
    )]
    public function material_cambiarUbicacion_put(): void
    {
    }

    /**
     * Endpoint custom: PUT /material/cambiarEstado/
     */
    #[OA\Put(
        path: '/material/cambiarEstado/',
        operationId: 'material_cambiarEstado_put',
        summary: 'Canvia l estat d un material',
        tags: ['Materials'],
        security: [['sanctum' => []]],

        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/MaterialCambiarEstadoRequest')),
        responses: [
            new OA\Response(response: 200, description: 'OK', content: new OA\JsonContent(ref: '#/components/schemas/ApiSuccess')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 422, description: 'Validacio incorrecta', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
        ]
    )]
    public function material_cambiarEstado_put(): void
    {
    }

    /**
     * Endpoint custom: PUT /material/cambiarUnidad/
     */
    #[OA\Put(
        path: '/material/cambiarUnidad/',
        operationId: 'material_cambiarUnidad_put',
        summary: 'Canvia les unitats d un material',
        tags: ['Materials'],
        security: [['sanctum' => []]],

        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/MaterialCambiarUnidadRequest')),
        responses: [
            new OA\Response(response: 200, description: 'OK', content: new OA\JsonContent(ref: '#/components/schemas/ApiSuccess')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 422, description: 'Validacio incorrecta', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
        ]
    )]
    public function material_cambiarUnidad_put(): void
    {
    }

    /**
     * Endpoint custom: PUT /material/cambiarInventario
     */
    #[OA\Put(
        path: '/material/cambiarInventario',
        operationId: 'material_cambiarInventario_put',
        summary: 'Canvia la configuracio d inventari d un material',
        tags: ['Materials'],
        security: [['sanctum' => []]],

        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/MaterialCambiarInventarioRequest')),
        responses: [
            new OA\Response(response: 200, description: 'OK', content: new OA\JsonContent(ref: '#/components/schemas/ApiSuccess')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 422, description: 'Validacio incorrecta', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
        ]
    )]
    public function material_cambiarInventario_put(): void
    {
    }

    /**
     * Endpoint custom: GET /material/espacio/{espacio}
     */
    #[OA\Get(
        path: '/material/espacio/{espacio}',
        operationId: 'material_espacio_espacio_get',
        summary: 'Materials d un espai',
        tags: ['Materials'],
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'espacio', in: 'path', required: true, schema: new OA\Schema(type: 'string')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Materials de l espai', content: new OA\JsonContent(ref: '#/components/schemas/MaterialEspacioResponse')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 422, description: 'Validacio incorrecta', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
        ]
    )]
    public function material_espacio_espacio_get(): void
    {
    }

    /**
     * Endpoint custom: GET /inventario
     */
    #[OA\Get(
        path: '/inventario',
        operationId: 'inventario_get',
        summary: 'Llistat d inventariable visible per l usuari',
        tags: ['Materials'],
        security: [['sanctum' => []]],

        responses: [
            new OA\Response(response: 200, description: 'Llistat inventari', content: new OA\JsonContent(ref: '#/components/schemas/MaterialCollectionResponse')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 422, description: 'Validacio incorrecta', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
            new OA\Response(response: 500, description: 'Error intern', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    public function inventario_get(): void
    {
    }

    /**
     * Endpoint custom: GET /inventario/{espai}
     */
    #[OA\Get(
        path: '/inventario/{espai}',
        operationId: 'inventario_espai_get',
        summary: 'Llistat d inventariable filtrat per espai',
        tags: ['Materials'],
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'espai', in: 'path', required: true, schema: new OA\Schema(type: 'string')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Llistat inventari per espai', content: new OA\JsonContent(ref: '#/components/schemas/MaterialCollectionResponse')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 422, description: 'Validacio incorrecta', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
            new OA\Response(response: 500, description: 'Error intern', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    public function inventario_espai_get(): void
    {
    }

    /**
     * Endpoint custom: GET /guardia/range
     */
    #[OA\Get(
        path: '/guardia/range',
        operationId: 'guardia_range_get',
        summary: 'Guardies entre dos dates',
        tags: ['Guardies'],
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'desde', in: 'query', required: true, schema: new OA\Schema(type: 'string', format: 'date')),
            new OA\Parameter(name: 'hasta', in: 'query', required: true, schema: new OA\Schema(type: 'string', format: 'date')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Guardies del rang', content: new OA\JsonContent(ref: '#/components/schemas/GuardiaCollectionResponse')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 422, description: 'Falten parametres desde/hasta', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 500, description: 'Error intern', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    public function guardia_range_get(): void
    {
    }

    /**
     * Endpoint custom: GET /alumnoGrupoModulo/{dni}/{modulo}
     */
    #[OA\Get(
        path: '/alumnoGrupoModulo/{dni}/{modulo}',
        operationId: 'alumnoGrupoModulo_dni_modulo_get',
        summary: 'Obte notes o dades d alumne per modul',
        tags: ['Alumnat'],
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'dni', in: 'path', required: true, schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'modulo', in: 'path', required: true, schema: new OA\Schema(type: 'string')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'OK', content: new OA\JsonContent(ref: '#/components/schemas/ApiSuccess')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 422, description: 'Validacio incorrecta', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
            new OA\Response(response: 500, description: 'Error intern', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    public function alumnoGrupoModulo_dni_modulo_get(): void
    {
    }

    /**
     * Endpoint custom: GET /horario/{idProfesor}/guardia
     */
    #[OA\Get(
        path: '/horario/{idProfesor}/guardia',
        operationId: 'horario_idProfesor_guardia_get',
        summary: 'Guardies d un professor',
        tags: ['Horaris'],
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'idProfesor', in: 'path', required: true, schema: new OA\Schema(type: 'string')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Llistat de guardies', content: new OA\JsonContent(ref: '#/components/schemas/HorarioCollectionResponse')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 422, description: 'Validacio incorrecta', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
        ]
    )]
    public function horario_idProfesor_guardia_get(): void
    {
    }

    /**
     * Endpoint custom: GET /horariosDia/{fecha}
     */
    #[OA\Get(
        path: '/horariosDia/{fecha}',
        operationId: 'horariosDia_fecha_get',
        summary: 'Franja horaria diaria per professor',
        tags: ['Horaris'],
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'fecha', in: 'path', required: true, schema: new OA\Schema(type: 'string', format: 'date')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Mapa DNI a franja', content: new OA\JsonContent(ref: '#/components/schemas/HorariosDiaResponse')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 422, description: 'Validacio incorrecta', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
        ]
    )]
    public function horariosDia_fecha_get(): void
    {
    }

    /**
     * Endpoint custom: PUT /asistencia/cambiar
     */
    #[OA\Put(
        path: '/asistencia/cambiar',
        operationId: 'asistencia_cambiar_put',
        summary: 'Canvia estat d assistencia',
        tags: ['Guardies'],
        security: [['sanctum' => []]],

        requestBody: new OA\RequestBody(required: false, content: new OA\JsonContent(type: 'object')),
        responses: [
            new OA\Response(response: 200, description: 'OK', content: new OA\JsonContent(ref: '#/components/schemas/ApiSuccess')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 422, description: 'Validacio incorrecta', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
        ]
    )]
    public function asistencia_cambiar_put(): void
    {
    }

    /**
     * Endpoint custom: PUT /reunion/{idReunion}/alumno/{idAlumno}
     */
    #[OA\Put(
        path: '/reunion/{idReunion}/alumno/{idAlumno}',
        operationId: 'reunion_idReunion_alumno_idAlumno_put',
        summary: 'Actualitza dades d alumne en reunio',
        tags: ['Activitats'],
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'idReunion', in: 'path', required: true, schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'idAlumno', in: 'path', required: true, schema: new OA\Schema(type: 'string')),
        ],
        requestBody: new OA\RequestBody(required: false, content: new OA\JsonContent(type: 'object')),
        responses: [
            new OA\Response(response: 200, description: 'OK', content: new OA\JsonContent(ref: '#/components/schemas/ApiSuccess')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 422, description: 'Validacio incorrecta', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
        ]
    )]
    public function reunion_idReunion_alumno_idAlumno_put(): void
    {
    }

    /**
     * Endpoint custom: GET /tiporeunion/{id}
     */
    #[OA\Get(
        path: '/tiporeunion/{id}',
        operationId: 'tiporeunion_id_get',
        summary: 'Obte tipus de reunio',
        tags: ['Horaris'],
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'OK', content: new OA\JsonContent(ref: '#/components/schemas/ApiSuccess')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 422, description: 'Validacio incorrecta', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
        ]
    )]
    public function tiporeunion_id_get(): void
    {
    }

    /**
     * Endpoint custom: GET /modulo/{id}
     */
    #[OA\Get(
        path: '/modulo/{id}',
        operationId: 'modulo_id_get',
        summary: 'Obte modul per identificador',
        tags: ['Horaris'],
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'OK', content: new OA\JsonContent(ref: '#/components/schemas/ApiSuccess')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 422, description: 'Validacio incorrecta', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
        ]
    )]
    public function modulo_id_get(): void
    {
    }

    /**
     * Endpoint custom: GET /horarioChange/{dni}
     */
    #[OA\Get(
        path: '/horarioChange/{dni}',
        operationId: 'horarioChange_dni_get',
        summary: 'Obte proposta de canvi horari',
        tags: ['Horaris'],
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'dni', in: 'path', required: true, schema: new OA\Schema(type: 'string')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'OK', content: new OA\JsonContent(ref: '#/components/schemas/ApiSuccess')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 422, description: 'Validacio incorrecta', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
        ]
    )]
    public function horarioChange_dni_get(): void
    {
    }

    /**
     * Endpoint custom: POST /horarioChange/{dni}
     */
    #[OA\Post(
        path: '/horarioChange/{dni}',
        operationId: 'horarioChange_dni_post',
        summary: 'Registra un canvi d horari per professor',
        tags: ['Horaris'],
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'dni', in: 'path', required: true, schema: new OA\Schema(type: 'string')),
        ],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/HorarioChangeRequest')),
        responses: [
            new OA\Response(response: 200, description: 'OK', content: new OA\JsonContent(ref: '#/components/schemas/ApiSuccess')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 422, description: 'Validacio incorrecta', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
        ]
    )]
    public function horarioChange_dni_post(): void
    {
    }

    /**
     * Endpoint custom: POST /centro/fusionar
     */
    #[OA\Post(
        path: '/centro/fusionar',
        operationId: 'centro_fusionar_post',
        summary: 'Fusiona centres en un centre principal',
        tags: ['Organitzacio'],
        security: [['sanctum' => []]],

        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/CentroFusionarRequest')),
        responses: [
            new OA\Response(response: 200, description: 'Resultat de la fusio', content: new OA\JsonContent(ref: '#/components/schemas/ApiSuccess')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 422, description: 'Validacio incorrecta', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
            new OA\Response(response: 500, description: 'Error intern', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    public function centro_fusionar_post(): void
    {
    }

    /**
     * Endpoint custom: GET /colaboracion/instructores/{id}
     */
    #[OA\Get(
        path: '/colaboracion/instructores/{id}',
        operationId: 'colaboracion_instructores_id_get',
        summary: 'Llista instructors d una colaboracio',
        tags: ['FCT'],
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'OK', content: new OA\JsonContent(ref: '#/components/schemas/ApiSuccess')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 422, description: 'Validacio incorrecta', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
        ]
    )]
    public function colaboracion_instructores_id_get(): void
    {
    }

    /**
     * Endpoint custom: GET /colaboracion/{colaboracion}/resolve
     */
    #[OA\Get(
        path: '/colaboracion/{colaboracion}/resolve',
        operationId: 'colaboracion_colaboracion_resolve_get',
        summary: 'Resol una colaboracio',
        tags: ['FCT'],
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'colaboracion', in: 'path', required: true, schema: new OA\Schema(type: 'string')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'OK', content: new OA\JsonContent(ref: '#/components/schemas/ApiSuccess')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 422, description: 'Validacio incorrecta', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
        ]
    )]
    public function colaboracion_colaboracion_resolve_get(): void
    {
    }

    /**
     * Endpoint custom: GET /colaboracion/{colaboracion}/refuse
     */
    #[OA\Get(
        path: '/colaboracion/{colaboracion}/refuse',
        operationId: 'colaboracion_colaboracion_refuse_get',
        summary: 'Rebutja una colaboracio',
        tags: ['FCT'],
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'colaboracion', in: 'path', required: true, schema: new OA\Schema(type: 'string')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'OK', content: new OA\JsonContent(ref: '#/components/schemas/ApiSuccess')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 422, description: 'Validacio incorrecta', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
        ]
    )]
    public function colaboracion_colaboracion_refuse_get(): void
    {
    }

    /**
     * Endpoint custom: GET /colaboracion/{colaboracion}/unauthorize
     */
    #[OA\Get(
        path: '/colaboracion/{colaboracion}/unauthorize',
        operationId: 'colaboracion_colaboracion_unauthorize_get',
        summary: 'Desautoritza una colaboracio',
        tags: ['FCT'],
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'colaboracion', in: 'path', required: true, schema: new OA\Schema(type: 'string')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'OK', content: new OA\JsonContent(ref: '#/components/schemas/ApiSuccess')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 422, description: 'Validacio incorrecta', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
        ]
    )]
    public function colaboracion_colaboracion_unauthorize_get(): void
    {
    }

    /**
     * Endpoint custom: GET /colaboracion/{colaboracion}/switch
     */
    #[OA\Get(
        path: '/colaboracion/{colaboracion}/switch',
        operationId: 'colaboracion_colaboracion_switch_get',
        summary: 'Canvia estat d una colaboracio',
        tags: ['FCT'],
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'colaboracion', in: 'path', required: true, schema: new OA\Schema(type: 'string')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'OK', content: new OA\JsonContent(ref: '#/components/schemas/ApiSuccess')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 422, description: 'Validacio incorrecta', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
        ]
    )]
    public function colaboracion_colaboracion_switch_get(): void
    {
    }

    /**
     * Endpoint custom: POST /colaboracion/{colaboracion}/telefonico
     */
    #[OA\Post(
        path: '/colaboracion/{colaboracion}/telefonico',
        operationId: 'colaboracion_colaboracion_telefonico_post',
        summary: 'Registra contacte telefonic de colaboracio',
        tags: ['FCT'],
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'colaboracion', in: 'path', required: true, schema: new OA\Schema(type: 'string')),
        ],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/ColaboracionTelefonicoRequest')),
        responses: [
            new OA\Response(response: 200, description: 'OK', content: new OA\JsonContent(ref: '#/components/schemas/ApiSuccess')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 422, description: 'Validacio incorrecta', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
        ]
    )]
    public function colaboracion_colaboracion_telefonico_post(): void
    {
    }

    /**
     * Endpoint custom: POST /colaboracion/{colaboracion}/book
     */
    #[OA\Post(
        path: '/colaboracion/{colaboracion}/book',
        operationId: 'colaboracion_colaboracion_book_post',
        summary: 'Programa una visita o cita de colaboracio',
        tags: ['FCT'],
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'colaboracion', in: 'path', required: true, schema: new OA\Schema(type: 'string')),
        ],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/ColaboracionBookRequest')),
        responses: [
            new OA\Response(response: 200, description: 'OK', content: new OA\JsonContent(ref: '#/components/schemas/ApiSuccess')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 422, description: 'Validacio incorrecta', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
        ]
    )]
    public function colaboracion_colaboracion_book_post(): void
    {
    }

    /**
     * Endpoint custom: GET /documentacionFCT/{documento}
     */
    #[OA\Get(
        path: '/documentacionFCT/{documento}',
        operationId: 'documentacionFCT_documento_get',
        summary: 'Executa generacio de document FCT',
        tags: ['FCT'],
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'documento', in: 'path', required: true, schema: new OA\Schema(type: 'string')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'OK', content: new OA\JsonContent(ref: '#/components/schemas/ApiSuccess')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 422, description: 'Validacio incorrecta', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
        ]
    )]
    public function documentacionFCT_documento_get(): void
    {
    }

    /**
     * Endpoint custom: GET /signatura
     */
    #[OA\Get(
        path: '/signatura',
        operationId: 'signatura_get',
        summary: 'Obte signatura',
        tags: ['FCT'],
        security: [['sanctum' => []]],

        responses: [
            new OA\Response(response: 200, description: 'OK', content: new OA\JsonContent(ref: '#/components/schemas/ApiSuccess')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 422, description: 'Validacio incorrecta', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
        ]
    )]
    public function signatura_get(): void
    {
    }

    /**
     * Endpoint custom: GET /signatura/director
     */
    #[OA\Get(
        path: '/signatura/director',
        operationId: 'signatura_director_get',
        summary: 'Obte signatura de direccio',
        tags: ['FCT'],
        security: [['sanctum' => []]],

        responses: [
            new OA\Response(response: 200, description: 'OK', content: new OA\JsonContent(ref: '#/components/schemas/ApiSuccess')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 422, description: 'Validacio incorrecta', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
        ]
    )]
    public function signatura_director_get(): void
    {
    }

    /**
     * Endpoint custom: GET /signatura/a1
     */
    #[OA\Get(
        path: '/signatura/a1',
        operationId: 'signatura_a1_get',
        summary: 'Obte signatura A1',
        tags: ['FCT'],
        security: [['sanctum' => []]],

        responses: [
            new OA\Response(response: 200, description: 'OK', content: new OA\JsonContent(ref: '#/components/schemas/ApiSuccess')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 422, description: 'Validacio incorrecta', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
        ]
    )]
    public function signatura_a1_get(): void
    {
    }

    /**
     * Endpoint custom: GET /matricula/{token}
     */
    #[OA\Get(
        path: '/matricula/{token}',
        operationId: 'matricula_token_get',
        summary: 'Obte dades de matricula per token',
        tags: ['FCT'],
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'token', in: 'path', required: true, schema: new OA\Schema(type: 'string')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'OK', content: new OA\JsonContent(ref: '#/components/schemas/ApiSuccess')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 422, description: 'Validacio incorrecta', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
        ]
    )]
    public function matricula_token_get(): void
    {
    }

    /**
     * Endpoint custom: GET /test/matricula/{token}
     */
    #[OA\Get(
        path: '/test/matricula/{token}',
        operationId: 'test_matricula_token_get',
        summary: 'Obte dades de matricula de prova',
        tags: ['Altres'],
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'token', in: 'path', required: true, schema: new OA\Schema(type: 'string')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'OK', content: new OA\JsonContent(ref: '#/components/schemas/ApiSuccess')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 422, description: 'Validacio incorrecta', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
        ]
    )]
    public function test_matricula_token_get(): void
    {
    }

    /**
     * Endpoint custom: POST /alumno/{dni}/foto
     */
    #[OA\Post(
        path: '/alumno/{dni}/foto',
        operationId: 'alumno_dni_foto_post',
        summary: 'Actualitza foto d alumne',
        tags: ['Alumnat'],
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'dni', in: 'path', required: true, schema: new OA\Schema(type: 'string')),
        ],
        requestBody: new OA\RequestBody(required: false, content: new OA\JsonContent(type: 'object')),
        responses: [
            new OA\Response(response: 200, description: 'OK', content: new OA\JsonContent(ref: '#/components/schemas/ApiSuccess')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 422, description: 'Validacio incorrecta', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
        ]
    )]
    public function alumno_dni_foto_post(): void
    {
    }

    /**
     * Endpoint custom: POST /alumno/{dni}/dades
     */
    #[OA\Post(
        path: '/alumno/{dni}/dades',
        operationId: 'alumno_dni_dades_post',
        summary: 'Actualitza dades d alumne',
        tags: ['Alumnat'],
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'dni', in: 'path', required: true, schema: new OA\Schema(type: 'string')),
        ],
        requestBody: new OA\RequestBody(required: false, content: new OA\JsonContent(type: 'object')),
        responses: [
            new OA\Response(response: 200, description: 'OK', content: new OA\JsonContent(ref: '#/components/schemas/ApiSuccess')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 422, description: 'Validacio incorrecta', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
        ]
    )]
    public function alumno_dni_dades_post(): void
    {
    }

    /**
     * Endpoint custom: POST /matricula/send
     */
    #[OA\Post(
        path: '/matricula/send',
        operationId: 'matricula_send_post',
        summary: 'Envia notificacio de matricula',
        tags: ['FCT'],
        security: [['sanctum' => []]],

        requestBody: new OA\RequestBody(required: false, content: new OA\JsonContent(type: 'object')),
        responses: [
            new OA\Response(response: 200, description: 'OK', content: new OA\JsonContent(ref: '#/components/schemas/ApiSuccess')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 422, description: 'Validacio incorrecta', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
        ]
    )]
    public function matricula_send_post(): void
    {
    }

    /**
     * Endpoint custom: GET /lote/{id}/articulos
     */
    #[OA\Get(
        path: '/lote/{id}/articulos',
        operationId: 'lote_id_articulos_get',
        summary: 'Llista articles d un lot',
        tags: ['Materials'],
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'OK', content: new OA\JsonContent(ref: '#/components/schemas/ApiSuccess')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 422, description: 'Validacio incorrecta', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
        ]
    )]
    public function lote_id_articulos_get(): void
    {
    }

    /**
     * Endpoint custom: PUT /lote/{id}/articulos
     */
    #[OA\Put(
        path: '/lote/{id}/articulos',
        operationId: 'lote_id_articulos_put',
        summary: 'Actualitza articles d un lot',
        tags: ['Materials'],
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string')),
        ],
        requestBody: new OA\RequestBody(required: false, content: new OA\JsonContent(type: 'object')),
        responses: [
            new OA\Response(response: 200, description: 'OK', content: new OA\JsonContent(ref: '#/components/schemas/ApiSuccess')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 422, description: 'Validacio incorrecta', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
        ]
    )]
    public function lote_id_articulos_put(): void
    {
    }

    /**
     * Endpoint custom: GET /articuloLote/{id}/materiales
     */
    #[OA\Get(
        path: '/articuloLote/{id}/materiales',
        operationId: 'articuloLote_id_materiales_get',
        summary: 'Llista materials d un article de lot',
        tags: ['Materials'],
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'OK', content: new OA\JsonContent(ref: '#/components/schemas/ApiSuccess')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 422, description: 'Validacio incorrecta', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
        ]
    )]
    public function articuloLote_id_materiales_get(): void
    {
    }

    /**
     * Endpoint custom: POST /attachFile
     */
    #[OA\Post(
        path: '/attachFile',
        operationId: 'attachFile_post',
        summary: 'Adjunta un fitxer',
        tags: ['Activitats'],
        security: [['sanctum' => []]],

        requestBody: new OA\RequestBody(required: false, content: new OA\JsonContent(type: 'object')),
        responses: [
            new OA\Response(response: 200, description: 'OK', content: new OA\JsonContent(ref: '#/components/schemas/ApiSuccess')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 422, description: 'Validacio incorrecta', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
        ]
    )]
    public function attachFile_post(): void
    {
    }

    /**
     * Endpoint custom: GET /getAttached/{modelo}/{id}
     */
    #[OA\Get(
        path: '/getAttached/{modelo}/{id}',
        operationId: 'getAttached_modelo_id_get',
        summary: 'Llista fitxers adjunts d un model',
        tags: ['Activitats'],
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'modelo', in: 'path', required: true, schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'OK', content: new OA\JsonContent(ref: '#/components/schemas/ApiSuccess')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 422, description: 'Validacio incorrecta', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
        ]
    )]
    public function getAttached_modelo_id_get(): void
    {
    }

    /**
     * Endpoint custom: GET /getNameAttached/{modelo}/{id}/{filename}
     */
    #[OA\Get(
        path: '/getNameAttached/{modelo}/{id}/{filename}',
        operationId: 'getNameAttached_modelo_id_filename_get',
        summary: 'Descarrega un fitxer adjunt',
        tags: ['Activitats'],
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'modelo', in: 'path', required: true, schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'filename', in: 'path', required: true, schema: new OA\Schema(type: 'string')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'OK', content: new OA\JsonContent(ref: '#/components/schemas/ApiSuccess')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 422, description: 'Validacio incorrecta', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
        ]
    )]
    public function getNameAttached_modelo_id_filename_get(): void
    {
    }

    /**
     * Endpoint custom: GET /removeAttached/{modelo}/{id}/{file}
     */
    #[OA\Get(
        path: '/removeAttached/{modelo}/{id}/{file}',
        operationId: 'removeAttached_modelo_id_file_get',
        summary: 'Elimina un fitxer adjunt',
        tags: ['Activitats'],
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'modelo', in: 'path', required: true, schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'file', in: 'path', required: true, schema: new OA\Schema(type: 'string')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'OK', content: new OA\JsonContent(ref: '#/components/schemas/ApiSuccess')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 422, description: 'Validacio incorrecta', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
        ]
    )]
    public function removeAttached_modelo_id_file_get(): void
    {
    }

    /**
     * Endpoint custom: GET /activity/{id}/move/{fct}
     */
    #[OA\Get(
        path: '/activity/{id}/move/{fct}',
        operationId: 'activity_id_move_fct_get',
        summary: 'Mou activitat entre FCT',
        tags: ['Altres'],
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'fct', in: 'path', required: true, schema: new OA\Schema(type: 'string')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'OK', content: new OA\JsonContent(ref: '#/components/schemas/ApiSuccess')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 422, description: 'Validacio incorrecta', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
        ]
    )]
    public function activity_id_move_fct_get(): void
    {
    }

    /**
     * Endpoint custom: GET /tutoriagrupo/{id}
     */
    #[OA\Get(
        path: '/tutoriagrupo/{id}',
        operationId: 'tutoriagrupo_id_get',
        summary: 'Obte tutoria de grup',
        tags: ['Horaris'],
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'OK', content: new OA\JsonContent(ref: '#/components/schemas/ApiSuccess')),
            new OA\Response(response: 401, description: 'No autoritzat', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 422, description: 'Validacio incorrecta', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
        ]
    )]
    public function tutoriagrupo_id_get(): void
    {
    }
}
