<?php

namespace Intranet\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Valida les dades dels formularis de centres.
 */
class CentroRequest extends FormRequest
{
    /**
     * Autoritza la petició de centre.
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Retorna les regles compartides pels formularis web i l'API de centres.
     *
     * @return array<string, string>
     */
    public static function sharedRules(bool $requireEmpresa = true): array
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
        return self::sharedRules();
    }
}
