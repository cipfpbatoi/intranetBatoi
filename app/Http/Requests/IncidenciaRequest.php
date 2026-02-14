<?php

namespace Intranet\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IncidenciaRequest extends FormRequest
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
        $imagenRule = 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120';
        $file = $this->file('imagen');
        if ($file) {
            $ext = strtolower($file->getClientOriginalExtension());
            if (in_array($ext, ['heic', 'heif'], true)) {
                $imagenRule = 'nullable|file|mimes:heic,heif|max:5120';
            }
        }

        return [
                'descripcion' => 'required',
                'tipo' => 'required',
                'idProfesor' => 'required',
                'prioridad' => 'required',
                'observaciones' => 'max:255',
                'solucion' => 'max:255',
                'fecha' => 'date',
                'imagen' => $imagenRule,
            ];
    }
}
