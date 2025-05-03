<?php

namespace Intranet\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CotxeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $cotxeId = $this->route('id'); // o $this->cotxe, depèn del nom a la ruta

        return [
            'matricula' => [
                'required',
                'string',
                'max:8',
                Rule::unique('cotxes')
                    ->ignore($cotxeId)
                    ->where(function ($query) {
                    return $query->where('idProfesor', authUser()->dni) ;
                }),
            ],
            'marca' => 'required|string|max:80',
        ];
    }
}
