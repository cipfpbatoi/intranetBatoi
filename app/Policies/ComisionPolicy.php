<?php

namespace Intranet\Policies;

use Intranet\Entities\Comision;
use Intranet\Policies\Concerns\InteractsWithProfesorOwnership;

/**
 * Policy d'autorització per a comissions de servei.
 */
class ComisionPolicy
{
    use InteractsWithProfesorOwnership;

    /**
     * @param mixed $user
     */
    public function create($user): bool
    {
        return $this->hasProfesorIdentity($user);
    }

    /**
     * @param mixed $user
     */
    public function update($user, Comision $comision): bool
    {
        return $this->hasProfesorIdentity($user);
    }

    /**
     * @param mixed $user
     */
    public function view($user, Comision $comision): bool
    {
        return $this->isOwner($user, $comision);
    }

    /**
     * @param mixed $user
     */
    public function manageFct($user, Comision $comision): bool
    {
        return $this->isOwner($user, $comision);
    }

    /**
     * Regla de propietat de la comissió.
     *
     * @param mixed $user
     */
    private function isOwner($user, Comision $comision): bool
    {
        return $this->hasProfesorIdentity($user)
            && (string) $comision->idProfesor === (string) $user->dni;
    }

}
