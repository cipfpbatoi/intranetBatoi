<?php

namespace Intranet\Policies;

use Intranet\Entities\Comision;

/**
 * Policy d'autorització per a comissions de servei.
 */
class ComisionPolicy
{
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
        return $this->hasProfesorIdentity($user);
    }

    /**
     * @param mixed $user
     */
    public function manageFct($user, Comision $comision): bool
    {
        return $this->hasProfesorIdentity($user);
    }

    /**
     * @param mixed $user
     */
    private function hasProfesorIdentity($user): bool
    {
        return is_object($user) && isset($user->dni) && (string) $user->dni !== '';
    }
}
