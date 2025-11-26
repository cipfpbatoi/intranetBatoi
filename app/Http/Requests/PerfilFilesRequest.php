<?php

namespace Intranet\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PerfilFilesRequest extends FormRequest
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
            'foto'      => 'nullable|image|mimes:jpg,jpeg,png|max:10240',
            'signatura' => 'nullable|image|mimes:jpg,jpeg,png|max:10240',
            'peu'       => 'nullable|image|mimes:jpg,jpeg,png|max:10240',
        ];
    }

    public function messages()
    {
        return [
            'foto.mimes' => 'La foto ha de ser en format JPG o PNG. '
                . 'Si la fas amb un iPhone, guarda-la com a JPEG o fes una captura de pantalla abans de pujar-la.',
            'signatura.mimes' => 'La signatura ha de ser en format JPG o PNG.',
            'peu.mimes' => 'El peu ha de ser en format JPG o PNG.',
        ];
    }
}
