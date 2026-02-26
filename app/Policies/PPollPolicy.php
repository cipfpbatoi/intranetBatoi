<?php

namespace Intranet\Policies;

use Intranet\Entities\Poll\PPoll;
use Intranet\Policies\Concerns\InteractsWithProfesorOwnership;

/**
 * Policy d'autorització per a plantilles de polls.
 */
class PPollPolicy
{
    use InteractsWithProfesorOwnership;

    /**
     * Determina si l'usuari pot veure la plantilla.
     *
     * @param mixed $user
     */
    public function view($user, PPoll $ppoll): bool
    {
        return $this->hasRole($user, 'roles.rol.qualitat');
    }

    /**
     * Determina si l'usuari pot crear plantilles.
     *
     * @param mixed $user
     */
    public function create($user): bool
    {
        return $this->hasRole($user, 'roles.rol.qualitat');
    }

    /**
     * Determina si l'usuari pot actualitzar plantilles.
     *
     * @param mixed $user
     */
    public function update($user, PPoll $ppoll): bool
    {
        return $this->hasRole($user, 'roles.rol.qualitat');
    }

    /**
     * Determina si l'usuari pot eliminar plantilles.
     *
     * @param mixed $user
     */
    public function delete($user, PPoll $ppoll): bool
    {
        return $this->hasRole($user, 'roles.rol.qualitat');
    }
}
