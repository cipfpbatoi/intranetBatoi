<?php

namespace Intranet\Policies;

use Intranet\Entities\Menu;
use Intranet\Policies\Concerns\InteractsWithProfesorOwnership;

/**
 * Policy d'autorització per a opcions de menú.
 */
class MenuPolicy
{
    use InteractsWithProfesorOwnership;

    /**
     * Determina si l'usuari pot crear menús.
     *
     * @param mixed $user
     */
    public function create($user): bool
    {
        return $this->hasRole($user, 'roles.rol.administrador');
    }

    /**
     * Determina si l'usuari pot actualitzar menús.
     *
     * @param mixed $user
     */
    public function update($user, Menu $menu): bool
    {
        return $this->hasRole($user, 'roles.rol.administrador');
    }

    /**
     * Determina si l'usuari pot eliminar menús.
     *
     * @param mixed $user
     */
    public function delete($user, Menu $menu): bool
    {
        return $this->hasRole($user, 'roles.rol.administrador');
    }
}
