<?php

namespace Intranet\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Intranet\Presentation\Crud\CotxeCrudSchema;

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
        return CotxeCrudSchema::requestRules($cotxeId, (string) authUser()->dni);
    }
}
