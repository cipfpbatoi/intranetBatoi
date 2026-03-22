<?php

namespace Intranet\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SettingRequest extends FormRequest
{
    /**
     * Determina si l'usuari autenticat pot modificar settings.
     */
    public function authorize()
    {
        $user = auth('profesor')->user();

        if (!is_object($user) || !isset($user->rol)) {
            return false;
        }

        return ((int) $user->rol) % (int) config('roles.rol.administrador') === 0;
    }

    /**
     * Retorna les regles de validació del formulari de settings.
     *
     * @return array<string, string>
     */
    public function rules()
    {
        return [
            'collection' => 'required',
            'key' => 'required',
            'value' => 'required',
        ];
    }
}
