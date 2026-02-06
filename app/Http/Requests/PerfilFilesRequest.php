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
        $fotoRule = 'nullable|image|mimes:jpg,jpeg,png|max:10240';
        $foto = $this->file('foto');
        if ($foto) {
            $ext = strtolower($foto->getClientOriginalExtension());
            if (in_array($ext, ['heic', 'heif'], true)) {
                $fotoRule = 'nullable|file|mimes:heic,heif|max:10240';
            }
        }

        return [
            'foto'      => $fotoRule,
            'signatura' => 'nullable|image|mimes:jpg,jpeg,png|max:10240',
            'peu'       => 'nullable|image|mimes:jpg,jpeg,png|max:10240',
        ];
    }

    public function messages()
    {
        return [
            'foto.mimes' => 'La foto ha de ser en format JPG, PNG o HEIF/HEIC. '
                . 'Si la fas amb un iPhone i dona error, guarda-la com a JPEG o fes una captura de pantalla abans de pujar-la.',
            'signatura.mimes' => 'La signatura ha de ser en format JPG o PNG.',
            'peu.mimes' => 'El peu ha de ser en format JPG o PNG.',
        ];
    }
}
