<?php

namespace Intranet\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Intranet\Presentation\Crud\TipoActividadCrudSchema;

class TipoActividadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return TipoActividadCrudSchema::RULES;
    }
}
