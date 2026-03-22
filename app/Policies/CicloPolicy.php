<?php

namespace Intranet\Policies;

use Intranet\Entities\Ciclo;
use Intranet\Policies\Concerns\InteractsWithProfesorOwnership;

/**
 * Policy d'autorització per a cicles.
 */
class CicloPolicy
{
    use InteractsWithProfesorOwnership;

    /**
     * Determina si l'usuari pot crear cicles.
     *
     * @param mixed $user
     */
    public function create($user): bool
    {
        return $this->hasRole($user, 'roles.rol.administrador');
    }

    /**
     * Determina si l'usuari pot actualitzar cicles.
     *
     * @param mixed $user
     */
    public function update($user, Ciclo $ciclo): bool
    {
        return $this->hasRole($user, 'roles.rol.administrador');
    }

    /**
     * Determina si l'usuari pot eliminar cicles.
     *
     * @param mixed $user
     */
    public function delete($user, Ciclo $ciclo): bool
    {
        return $this->hasRole($user, 'roles.rol.administrador');
    }
}
