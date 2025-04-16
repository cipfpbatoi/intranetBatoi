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
        $dia = authUser()->dni == config('avisos.director')?'today':'tomorrow';
        return [
            'servicio' => 'required',
            'kilometraje' => 'required|integer',
            'desde' => "required|date|after:$dia",
            'hasta' => 'required|date|after:desde',
            'alojamiento' => 'required|numeric',
            'comida' => 'required|numeric',
            'gastos' => 'required|numeric',
            'medio' => 'required|numeric',
            'marca' => 'required_if:medio,0|required_if:medio,1|max:30',
            'matricula' => 'required_if:medio,0|required_if:medio,1|max:10'
        ];
    }
}
