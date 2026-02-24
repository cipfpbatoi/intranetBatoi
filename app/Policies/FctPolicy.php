<?php

namespace Intranet\Policies;

use Intranet\Entities\Fct;

/**
 * Policy d'autorització per a les operacions de FCT.
 */
class FctPolicy
{
    /**
     * Determina si l'usuari pot crear una FCT.
     *
     * @param mixed $user
     */
    public function create($user): bool
    {
        return $this->canMutate($user);
    }

    /**
     * Determina si l'usuari pot actualitzar una FCT.
     *
     * @param mixed $user
     */
    public function update($user, Fct $fct): bool
    {
        return $this->canMutate($user);
    }

    /**
     * Determina si l'usuari pot eliminar una FCT.
     *
     * @param mixed $user
     */
    public function delete($user, Fct $fct): bool
    {
        return $this->canMutate($user);
    }

    /**
     * Regla comuna de permisos per a mutacions de FCT.
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
            || $rol % (int) config('roles.rol.practicas') === 0
            || $rol % (int) config('roles.rol.jefe_practicas') === 0;
    }
}
