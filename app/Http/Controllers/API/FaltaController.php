<?php

namespace Intranet\Http\Controllers\API;

use Intranet\Application\Falta\FaltaService;
use Intranet\Entities\Falta;
use Illuminate\Http\Request;

/**
 * API per a faltes de professorat.
 */
class FaltaController extends ApiResourceController
{

    protected $model = 'Falta';

    /**
     * Actualitza una falta respectant la restricció de faltes ja comunicades.
     */
    public function update(Request $request, $id)
    {
        $falta = Falta::find($id);

        if (!$falta) {
            return $this->sendNotFound("Not found: Falta #{$id}");
        }

        $user = $request->user('sanctum') ?? $request->user('api') ?? authUser();
        $isDireccion = $user && esRol($user->rol, config('roles.rol.direccion'));
        $canEditSubmittedData = $isDireccion
            && (string) $falta->idProfesor !== (string) ($user->dni ?? '');

        app(FaltaService::class)->update($id, $request, (bool) $canEditSubmittedData);

        return $this->sendResponse(['updated' => true], 'OK');
    }
}
