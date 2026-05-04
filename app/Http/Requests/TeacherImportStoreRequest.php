<?php

namespace Intranet\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validació d'entrada per a la importació individual de professorat.
 *
 * El control de format XML es fa en `ImportService`, perquè els navegadors i
 * proxies poden enviar el MIME d'un `.xml` amb variants no reconegudes per
 * `mimes:xml`.
 */
class TeacherImportStoreRequest extends FormRequest
{
    /**
     * Determina si l'usuari pot executar importacions individuals.
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
            'idProfesor' => 'required|string|max:15',
            'fichero' => 'required|file',
            'horari' => 'nullable|boolean',
            'lost' => 'nullable|boolean',
            'mode' => 'nullable|in:full,create_only',
        ];
    }
}
