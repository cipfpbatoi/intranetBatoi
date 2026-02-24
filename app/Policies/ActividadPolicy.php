<?php

namespace Intranet\Policies;

use Intranet\Entities\Actividad;
use Intranet\Policies\Concerns\InteractsWithProfesorOwnership;

/**
 * Policy d'autorització per al flux d'activitats.
 */
class ActividadPolicy
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
    public function view($user, Actividad $actividad): bool
    {
        return $this->hasProfesorIdentity($user);
    }

    /**
     * @param mixed $user
     */
    public function update($user, Actividad $actividad): bool
    {
        return $this->hasProfesorIdentity($user);
    }

    /**
     * @param mixed $user
     */
    public function manageParticipants($user, Actividad $actividad): bool
    {
        return $this->hasProfesorIdentity($user);
    }

    /**
     * @param mixed $user
     */
    public function notify($user, Actividad $actividad): bool
    {
        return $this->hasProfesorIdentity($user);
    }

}
