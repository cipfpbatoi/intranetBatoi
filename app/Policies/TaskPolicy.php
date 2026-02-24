<?php

namespace Intranet\Policies;

use Intranet\Entities\Task;

/**
 * Policy d'autorització per a tasques.
 */
class TaskPolicy
{
    /**
     * Determina si l'usuari pot crear tasques (rol administrador).
     *
     * @param mixed $user
     */
    public function create($user): bool
    {
        return $this->isAdministrador($user);
    }

    /**
     * Determina si l'usuari pot actualitzar tasques (rol administrador).
     *
     * @param mixed $user
     */
    public function update($user, Task $task): bool
    {
        return $this->isAdministrador($user);
    }

    /**
     * Determina si l'usuari pot marcar/desmarcar una tasca pròpia.
     *
     * @param mixed $user
     */
    public function check($user, Task $task): bool
    {
        return is_object($user) && isset($user->dni) && (string) $user->dni !== '';
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
