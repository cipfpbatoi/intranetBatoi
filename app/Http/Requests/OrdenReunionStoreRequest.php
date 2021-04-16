<?php

namespace Intranet\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrdenReunionStoreRequest extends FormRequest
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
            'orden' => 'required|integer',
            'descripcion' => 'required|max:120',
        ];
    }
}
