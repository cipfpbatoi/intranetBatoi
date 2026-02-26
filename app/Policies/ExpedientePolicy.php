<?php

namespace Intranet\Policies;

use Intranet\Entities\Expediente;
use Intranet\Policies\Concerns\InteractsWithProfesorOwnership;

/**
 * Policy d'autorització per a expedients.
 */
class ExpedientePolicy
{
    use InteractsWithProfesorOwnership;

    /**
     * Determina si l'usuari pot crear expedients.
     *
     * @param mixed $user
     */
    public function create($user): bool
    {
        return $this->hasProfesorIdentity($user);
    }

    /**
     * Determina si l'usuari pot veure expedients.
     *
     * @param mixed $user
     */
    public function view($user, Expediente $expediente): bool
    {
        return $this->ownsOrIsDirectionOrAdmin($user, (string) $expediente->idProfesor);
    }

    /**
     * Determina si l'usuari pot actualitzar expedients.
     *
     * @param mixed $user
     */
    public function update($user, Expediente $expediente): bool
    {
        return $this->ownsOrIsDirectionOrAdmin($user, (string) $expediente->idProfesor);
    }

    /**
     * Determina si l'usuari pot eliminar expedients.
     *
     * @param mixed $user
     */
    public function delete($user, Expediente $expediente): bool
    {
        return $this->ownsOrIsDirectionOrAdmin($user, (string) $expediente->idProfesor);
    }
}
