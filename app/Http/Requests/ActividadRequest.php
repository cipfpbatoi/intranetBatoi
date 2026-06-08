<?php

namespace Intranet\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Valida l'alta i edició d'activitats complementàries i extraescolars.
 */
class ActividadRequest extends FormRequest
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
            'name' => 'required|between:1,75',
            'desde' => 'required|date',
            'hasta' => 'required|date|after:desde',
            'tipus_activitat' => ['required', Rule::in(['complementaria', 'extraescolar'])],
            'ubicacio_activitat' => ['required', Rule::in([
                'centre',
                'exterior_transport',
                'exterior_sense_transport',
            ])],
        ];
    }
}
