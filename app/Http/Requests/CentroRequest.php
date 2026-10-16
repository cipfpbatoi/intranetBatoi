<?php

namespace Intranet\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CentroRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    /**
     * Retorna les regles compartides pels formularis web i l'API de centres.
     *
     * @return array<string, string>
     */
    public static function validationRules(bool $requireEmpresa = true): array
    {
        $rules = [
            'nombre' => 'required',
            'direccion' => 'required',
            'localidad' => 'required',
            'latitud' => 'nullable|numeric|between:-90,90',
            'longitud' => 'nullable|numeric|between:-180,180',
        ];

        if ($requireEmpresa) {
            $rules['idEmpresa'] = 'required';
        }

        return $rules;
    }

    /**
     * Retorna les regles de validació del formulari de centre.
     *
     * @return array<string, string>
     */
    public function rules(): array
    {
        return self::validationRules();
    }
}
