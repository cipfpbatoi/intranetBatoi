<?php

namespace Intranet\Http\Controllers\API;

use Illuminate\Http\Request;

/**
 * @deprecated Endpoint legacy de dual.
 *
 * Només es manté per lectura/compatibilitat temporal.
 * La creació de nous registres està deshabilitada.
 */
class DualController extends ApiResourceController
{

    protected $model = 'AlumnoFct';
    protected $resource = 'Intranet\Http\Resources\DualResource';

    public function store(Request $request)
    {
        return response()->json([
            'success' => false,
            'message' => 'Dual està deprecated: no es permet crear nous registres.',
        ], 410);
    }

}
