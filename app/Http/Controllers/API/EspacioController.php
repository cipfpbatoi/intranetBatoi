<?php

namespace Intranet\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Intranet\Entities\Espacio;

/**
 * API d'espais amb autorització explícita per a les mutacions.
 */
class EspacioController extends ApiResourceController
{
    protected $model = 'Espacio';

    /**
     * Crea espais només per a l'usuari de Direcció autenticat a l'API.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $user = $request->user('sanctum') ?? $request->user('api');
        Gate::forUser($user)->authorize('create', Espacio::class);

        return parent::store($request);
    }
}
