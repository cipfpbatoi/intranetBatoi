<?php

namespace Intranet\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
        return [
            'id' => 'required|numeric|max:99|unique:tipoincidencias,id',
            'nombre' => 'required|max:40',
            'nom' => 'required|max:40',
            'idProfesor' => 'required|exists:profesores,dni',
            'tipus' => 'required'
        ];
    }
}
