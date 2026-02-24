<?php

namespace Intranet\Policies;

use Intranet\Entities\Setting;

/**
 * Policy d'autorització per a la gestió de settings.
 */
class SettingPolicy
{
    /**
     * Determina si l'usuari pot crear settings (rol administrador).
     *
     * @param mixed $user
     */
    public function create($user): bool
    {
        return $this->isAdministrador($user);
    }

    /**
     * Determina si l'usuari pot actualitzar settings (rol administrador).
     *
     * @param mixed $user
     */
    public function update($user, Setting $setting): bool
    {
        return $this->isAdministrador($user);
    }

    /**
     * Determina si l'usuari pot eliminar settings (rol administrador).
     *
     * @param mixed $user
     */
    public function delete($user, Setting $setting): bool
    {
        return $this->isAdministrador($user);
    }

    /**
     * @param mixed $user
     */
    private function isAdministrador($user): bool
    {
        if (!is_object($user) || !isset($user->rol)) {
            return false;
        }

        return ((int) $user->rol) % (int) config('roles.rol.administrador') === 0;
    }
}
