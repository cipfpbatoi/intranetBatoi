<?php

namespace Intranet\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EmpresaCentroRequest extends FormRequest
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
            'cif' => 'required|alpha_num|unique:empresas,id',
            //'concierto' => 'required|unique:empresas,concierto',
            'concierto' => 'required',
            'email' => 'required|email',
            'telefono' => 'required|max:20',
        ];
    }
}
