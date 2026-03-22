<?php

namespace Intranet\Policies;

use Intranet\Entities\Modulo_ciclo;
use Intranet\Policies\Concerns\InteractsWithProfesorOwnership;

/**
 * Policy d'autorització per a l'enllaç mòdul-cicle.
 */
class ModuloCicloPolicy
{
    use InteractsWithProfesorOwnership;

    /**
     * Determina si l'usuari pot crear enllaços mòdul-cicle.
     *
     * @param mixed $user
     */
    public function create($user): bool
    {
        return $this->hasRole($user, 'roles.rol.administrador');
    }

    /**
     * Determina si l'usuari pot actualitzar enllaços mòdul-cicle.
     *
     * @param mixed $user
     */
    public function update($user, Modulo_ciclo $moduloCiclo): bool
    {
        return $this->hasRole($user, 'roles.rol.administrador');
    }

    /**
     * Determina si l'usuari pot eliminar enllaços mòdul-cicle.
     *
     * @param mixed $user
     */
    public function delete($user, Modulo_ciclo $moduloCiclo): bool
    {
        return $this->hasRole($user, 'roles.rol.administrador');
    }
}
