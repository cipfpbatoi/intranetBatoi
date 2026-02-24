<?php

namespace Intranet\Policies;

use Intranet\Entities\Fct;

/**
 * Policy d'autorització per a les operacions de FCT.
 */
class FctPolicy
{
    /**
     * Determina si l'usuari pot accedir al panell general de FCT.
     *
     * @param mixed $user
     */
    public function viewAny($user): bool
    {
        return $this->canMutate($user);
    }

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
     * Determina si l'usuari pot gestionar avaluacions FCT (apte/no apte/projecte/inserció).
     *
     * @param mixed $user
     */
    public function manageAval($user): bool
    {
        return $this->canMutate($user);
    }

    /**
     * Determina si l'usuari pot demanar actes d'avaluació.
     *
     * @param mixed $user
     */
    public function requestActa($user): bool
    {
        return $this->canMutate($user);
    }

    /**
     * Determina si l'usuari pot enviar annexos A56 a secretaria.
     *
     * @param mixed $user
     */
    public function sendA56($user): bool
    {
        return $this->canMutate($user);
    }

    /**
     * Determina si l'usuari pot consultar estadístiques d'avaluació FCT.
     *
     * @param mixed $user
     */
    public function viewStats($user): bool
    {
        return $this->canMutate($user);
    }

    /**
     * Determina si l'usuari pot validar/rebutjar actes pendents de FCT.
     *
     * @param mixed $user
     */
    public function managePendingActa($user): bool
    {
        if (!is_object($user) || !isset($user->rol)) {
            return false;
        }

        $rol = (int) $user->rol;

        return $rol % (int) config('roles.rol.direccion') === 0
            || $rol % (int) config('roles.rol.administrador') === 0
            || $rol % (int) config('roles.rol.jefe_practicas') === 0;
    }

    /**
     * Determina si l'usuari pot gestionar el panell de control de dual.
     *
     * @param mixed $user
     */
    public function manageFctControl($user): bool
    {
        if (!is_object($user) || !isset($user->rol)) {
            return false;
        }

        $rol = (int) $user->rol;

        return $rol % (int) config('roles.rol.jefe_practicas') === 0
            || $rol % (int) config('roles.rol.direccion') === 0
            || $rol % (int) config('roles.rol.administrador') === 0;
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
