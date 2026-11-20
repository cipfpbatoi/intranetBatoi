<?php

declare(strict_types=1);

namespace Intranet\Http\Requests\Auth\Sso;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Intranet\Entities\Profesor;

/**
 * Valida i autoritza l'inici del SSO de professorat cap a NM.
 */
class NmSsoRequest extends FormRequest
{
    /**
     * Restringix el flux a professorat actiu, també en rols combinats.
     */
    public function authorize(): bool
    {
        $profesor = Auth::guard('profesor')->user();
        if (!$profesor instanceof Profesor) {
            return false;
        }

        $fechaBaja = $profesor->getRawOriginal('fecha_baja');
        $estaActivo = (int) $profesor->activo === 1
            && ($fechaBaja === null || $fechaBaja === '' || $fechaBaja === '0000-00-00');

        return $estaActivo
            && esRol((int) $profesor->rol, (int) config('roles.rol.profesor'));
    }

    /**
     * Exigix un state opac i prou llarg sense transformar-lo.
     *
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'state' => [
                'required',
                'string',
                'min:32',
                'max:512',
                'regex:/\A[A-Za-z0-9._~-]+\z/',
            ],
        ];
    }

    /**
     * Retorna els missatges de validació del contracte SSO.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'state.required' => 'Falta el paràmetre de seguretat state.',
            'state.min' => 'El paràmetre state no té una longitud segura.',
            'state.max' => 'El paràmetre state és massa llarg.',
            'state.regex' => 'El paràmetre state conté caràcters no admesos.',
        ];
    }
}
