<?php

namespace Intranet\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ComisionRequest extends FormRequest
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
        if (AuthUser()->dni == config('contacto.director')){
            return [
                'servicio' => 'required',
                'kilometraje' => 'required|integer',
                'desde' => 'required|date|after:today',
                'hasta' => 'required|date|after:desde',
                'alojamiento' => 'required|numeric',
                'comida' => 'required|numeric',
                'gastos' => 'required|numeric',
                'medio' => 'required|max:30',
                'marca' => 'required|max:30',
                'matricula' => 'required|max:10'
            ];
        } else {
            return [
                'servicio' => 'required',
                'kilometraje' => 'required|integer',
                'desde' => 'required|date|after:tomorrow',
                'hasta' => 'required|date|after:desde',
                'alojamiento' => 'required|numeric',
                'comida' => 'required|numeric',
                'gastos' => 'required|numeric',
                'medio' => 'required|max:30',
                'marca' => 'required|max:30',
                'matricula' => 'required|max:10'
            ];
        }

    }
}
