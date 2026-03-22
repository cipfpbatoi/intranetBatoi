<?php

namespace Intranet\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LoteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $currentRegistre = $this->route('id') ?? $this->route('lote');

        if (is_object($currentRegistre) && isset($currentRegistre->registre)) {
            $currentRegistre = $currentRegistre->registre;
        }

        return [
            'registre' => [
                'required',
                'alpha_dash',
                Rule::unique('lotes', 'registre')->ignore($currentRegistre, 'registre'),
            ],
        ];
    }
}
