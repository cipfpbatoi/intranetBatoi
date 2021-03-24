<?php

namespace Intranet\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CursoRequest extends FormRequest
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
                'titulo' => 'required',
                'fecha_inicio' => 'required|date',
                'fecha_fin' => 'required|date',
                'horas' => 'required|integer|max:255',
                'aforo' => 'numeric',
                'comentarios' => 'required'
        ];
    }
}
