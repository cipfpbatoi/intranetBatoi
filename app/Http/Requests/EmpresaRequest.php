<?php

namespace Intranet\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Intranet\Presentation\Crud\EmpresaCrudSchema;

class EmpresaRequest extends FormRequest
{
    /**
     * Determina si l'usuari està autoritzat a fer la petició.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Regles de validació del formulari d'empresa.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $empresa = $this->route('empresa');
        $empresaId = is_object($empresa) && isset($empresa->id) ? $empresa->id : $empresa;

        return EmpresaCrudSchema::requestRules($empresaId);
    }

    /**
     * Normalitza dades abans de validar/guardar.
     */
    protected function prepareForValidation(): void
    {
        $checkboxes = ['europa', 'sao', 'dual', 'delitos', 'menores'];
        $normalized = [];

        foreach ($checkboxes as $field) {
            $normalized[$field] = $this->boolean($field);
        }

        if ($this->filled('cif')) {
            $normalized['cif'] = strtoupper((string) $this->input('cif'));
        }

        $this->merge($normalized);
    }
}

