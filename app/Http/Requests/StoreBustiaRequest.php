<?php 
namespace Intranet\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBustiaRequest extends FormRequest
{
    public function authorize(): bool { return true; } // si cal, posa policies
    public function rules(): array {
        return [
            'mensaje'   => 'required|string|min:15|max:5000',
            'categoria' => 'nullable|string|max:50',
            'anonimo'   => 'sometimes|boolean',
            'adjunto'   => 'nullable|file|mimes:pdf,jpg,jpeg,png,heic,webp|max:5120',
        ];
    }
    public function messages(): array {
        return [
            'mensaje.required' => 'Explica’ns què ha passat (mínim 15 caràcters).',
        ];
    }
}