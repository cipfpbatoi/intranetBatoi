<?php

namespace Intranet\Policies\Concerns;

/**
 * Utilitats de policy per a regles basades en propietari professor i rols elevats.
 */
trait InteractsWithProfesorOwnership
{
    /**
     * Comprova que l'usuari tinga identitat de professor.
     *
     * @param mixed $user
     */
    private function hasProfesorIdentity($user): bool
    {
        return is_object($user) && isset($user->dni) && (string) $user->dni !== '';
    }

    /**
     * Comprova si l'usuari té un rol concret (bitmask de rols).
     *
     * @param mixed $user
     */
    private function hasRole($user, string $roleConfigKey): bool
    {
        if (!is_object($user) || !isset($user->rol)) {
            return false;
        }

        $role = (int) config($roleConfigKey);
        if ($role <= 0) {
            return false;
        }

        return ((int) $user->rol) % $role === 0;
    }

    /**
     * Comprova si l'usuari és direcció o administració.
     *
     * @param mixed $user
     */
    private function isDirectionOrAdmin($user): bool
    {
        return $this->hasRole($user, 'roles.rol.direccion')
            || $this->hasRole($user, 'roles.rol.administrador');
    }

    /**
     * Regla genèrica: propietari professor o rols elevats.
     *
     * @param mixed $user
     */
    private function ownsOrIsDirectionOrAdmin($user, string $ownerDni): bool
    {
        if (!$this->hasProfesorIdentity($user)) {
            return false;
        }

        return (string) $user->dni === $ownerDni || $this->isDirectionOrAdmin($user);
    }
}
