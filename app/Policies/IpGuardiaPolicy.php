<?php

namespace Intranet\Policies;

use Intranet\Entities\IpGuardia;
use Intranet\Policies\Concerns\InteractsWithProfesorOwnership;

/**
 * Policy d'autorització per a IPs de guàrdia.
 */
class IpGuardiaPolicy
{
    use InteractsWithProfesorOwnership;

    /**
     * Determina si l'usuari pot crear IPs.
     *
     * @param mixed $user
     */
    public function create($user): bool
    {
        return $this->hasRole($user, 'roles.rol.administrador');
    }

    /**
     * Determina si l'usuari pot actualitzar IPs.
     *
     * @param mixed $user
     */
    public function update($user, IpGuardia $ipGuardia): bool
    {
        return $this->hasRole($user, 'roles.rol.administrador');
    }

    /**
     * Determina si l'usuari pot eliminar IPs.
     *
     * @param mixed $user
     */
    public function delete($user, IpGuardia $ipGuardia): bool
    {
        return $this->hasRole($user, 'roles.rol.administrador');
    }
}
