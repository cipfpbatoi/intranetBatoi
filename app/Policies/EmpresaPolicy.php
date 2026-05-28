<?php

namespace Intranet\Policies;

use Intranet\Entities\Empresa;

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
}
