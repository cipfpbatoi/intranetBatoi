<?php

namespace Intranet\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TipoActividadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'cliteral' => ['required', 'string', 'max:50'],
            'vliteral' => ['required', 'string', 'max:50'],
            'departamento_id' => ['nullable', 'integer', Rule::exists('departamentos', 'id')],
        ];
    }
}
