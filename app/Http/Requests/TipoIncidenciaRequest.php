<?php

namespace Intranet\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Intranet\Presentation\Crud\TipoIncidenciaCrudSchema;

class TipoIncidenciaRequest extends FormRequest
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
        $currentId = $this->route('ciclo')
            ?? $this->route('tipoincidencia')
            ?? $this->route('tipoincidencium');

        return TipoIncidenciaCrudSchema::requestRules($currentId);
    }
}
