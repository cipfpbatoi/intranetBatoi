<?php

namespace Intranet\Policies;

use Intranet\Entities\Empresa;

/**
 * Policy d'autorització per a la gestió d'empreses.
 */
class EmpresaPolicy
{
    /**
     * Determina si l'usuari pot accedir als llistats d'empreses.
     *
     * @param mixed $user
     */
    public function viewAny($user): bool
    {
        return $this->canMutate($user);
    }

    public function create($user): bool
    {
        return $this->canMutate($user);
    }

    public function update($user, Empresa $empresa): bool
    {
        return $this->canMutate($user);
    }

    /**
     * Determina si l'usuari pot eliminar una empresa.
     *
     * L'esborrat queda restringit a cap de pràctiques, que és qui veu
     * l'acció des de la fitxa d'empresa.
     *
     * @param mixed $user
     */
    public function delete($user, Empresa $empresa): bool
    {
        return $this->canDelete($user);
    }

    /**
     * Regla comuna de permisos per a consultes i edició.
     *
     * @param mixed $user
     */
    private function canMutate($user): bool
    {
        if (!is_object($user) || !isset($user->rol)) {
            return false;
        }

        $rol = (int) $user->rol;

        return $rol % (int) config('roles.rol.tutor') === 0
            || $rol % (int) config('roles.rol.jefe_practicas') === 0
            || $rol % (int) config('roles.rol.direccion') === 0;
    }

    /**
     * Regla específica per a l'esborrat d'empreses.
     *
     * @param mixed $user
     */
    private function canDelete($user): bool
    {
        if (!is_object($user) || !isset($user->rol)) {
            return false;
        }

        return ((int) $user->rol) % (int) config('roles.rol.jefe_practicas') === 0;
    }
}
