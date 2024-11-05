<?php

namespace Intranet\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProyectoRequest extends FormRequest
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
            'titol'=> 'required|max:255',
            //'grup'=> 'required',
            'objectius'=> 'required',
            'resultats'=> 'required',
            'aplicacions'=> 'required',
            'recursos'=> 'required',
            'descripcio'=> 'required',
        ];
    }
}
