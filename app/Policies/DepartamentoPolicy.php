<?php

namespace Intranet\Policies;

use Intranet\Entities\Departamento;
use Intranet\Policies\Concerns\InteractsWithProfesorOwnership;

/**
 * Policy d'autorització per a departaments.
 */
class DepartamentoPolicy
{
    use InteractsWithProfesorOwnership;

    /**
     * Determina si l'usuari pot crear departaments.
     *
     * @param mixed $user
     */
    public function create($user): bool
    {
        return $this->hasRole($user, 'roles.rol.administrador');
    }

    /**
     * Determina si l'usuari pot actualitzar departaments.
     *
     * @param mixed $user
     */
    public function update($user, Departamento $departamento): bool
    {
        return $this->hasRole($user, 'roles.rol.administrador');
    }

    /**
     * Determina si l'usuari pot eliminar departaments.
     *
     * @param mixed $user
     */
    public function delete($user, Departamento $departamento): bool
    {
        return $this->hasRole($user, 'roles.rol.administrador');
    }
}
