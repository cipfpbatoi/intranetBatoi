<?php

namespace Intranet\Http\Controllers\API;

use Illuminate\Http\Request;
use Intranet\Http\Requests;
use Intranet\Http\Controllers\Controller;

class OrdenReunionController extends ApiResourceController
{

    protected $model = 'OrdenReunion';

    /**
     * Regles de validació per a creació.
     *
     * - `descripcion`: obliga a text breu.
     * - `resumen`: el límit màxim és de 65.535 caràcters (columna TEXT).
     */
    protected function storeRules(): array
    {
        return [
            'descripcion' => 'required|string|max:120',
            'resumen' => 'nullable|string|max:65535',
            'idReunion' => 'required|integer',
            'orden' => 'required|integer|between:1,127',
        ];
    }

    /**
     * Regles de validació per a actualització.
     *
     * Es permet partial update, però el resum queda acotat.
     */
    protected function updateRules(): array
    {
        return [
            'descripcion' => 'sometimes|string|max:120',
            'resumen' => 'sometimes|nullable|string|max:65535',
            'idReunion' => 'sometimes|integer',
            'orden' => 'sometimes|integer|between:1,127',
        ];
    }

}
