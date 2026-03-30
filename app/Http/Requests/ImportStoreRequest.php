<?php

namespace Intranet\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validació d'entrada per a la importació general.
 *
 * El control de format XML es fa en `ImportService`, perquè els navegadors i
 * proxies poden enviar el MIME d'un `.xml` amb variants no reconegudes per
 * `mimes:xml`.
 */
class ImportStoreRequest extends FormRequest
{
    /**
     * Determina si l'usuari pot executar importacions.
     */
    public function authorize()
    {
        return userIsAllow(config('roles.rol.administrador'));
    }

    /**
     * Regles bàsiques d'entrada del formulari.
     *
     * @return array<string, string>
     */
    public function rules()
    {
        return [
            'fichero' => 'required|file',
            'primera' => 'nullable|in:on,off,1,0,true,false',
            'mode' => 'nullable|in:full,create_only',
        ];
    }
}
