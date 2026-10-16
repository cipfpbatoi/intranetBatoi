<?php

namespace Intranet\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Valida la creació web d'una reunió sense acceptar camps de propietat.
 */
class ReunionStoreRequest extends FormRequest
{
    /**
     * Qualsevol professor que arriba a la ruta protegida pot validar la petició.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Valida només les dades funcionals; el convocant prové de l'autenticació.
     *
     * @return array<string, string>
     */
    public function rules(): array
    {
        return [
            'tipo' => 'required|integer',
            'grupo' => 'nullable|string|max:20',
            'idGrupo' => 'nullable|string|max:10',
            'curso' => 'required|string|max:20',
            'numero' => 'nullable|integer',
            'fecha' => 'required|date',
            'descripcion' => 'required|string|max:120',
            'objetivos' => 'nullable|string',
            'idEspacio' => 'required|string|max:10',
        ];
    }
}
