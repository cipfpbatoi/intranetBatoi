<?php

namespace Intranet\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TeacherImportStoreRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'idProfesor' => 'required|string|max:15',
            'fichero' => 'required|file|mimes:xml',
            'horari' => 'nullable|boolean',
            'lost' => 'nullable|boolean',
            'mode' => 'nullable|in:full,create_only',
        ];
    }
}
