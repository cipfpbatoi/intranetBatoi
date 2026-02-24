<?php

namespace Intranet\Policies;

use Intranet\Entities\Falta;
use Intranet\Policies\Concerns\InteractsWithProfesorOwnership;

/**
 * Policy d'autorització per a la gestió de faltes.
 */
class FaltaPolicy
{
    use InteractsWithProfesorOwnership;

    /**
     * Determina si l'usuari pot crear una falta.
     *
     * @param mixed $user
     */
    public function create($user): bool
    {
        return $this->hasProfesorIdentity($user);
    }

    /**
     * Determina si l'usuari pot veure una falta.
     *
     * @param mixed $user
     */
    public function view($user, Falta $falta): bool
    {
        return $this->ownsOrIsDirectionOrAdmin($user, (string) $falta->idProfesor);
    }

    /**
     * Determina si l'usuari pot actualitzar una falta.
     *
     * @param mixed $user
     */
    public function update($user, Falta $falta): bool
    {
        return $this->ownsOrIsDirectionOrAdmin($user, (string) $falta->idProfesor);
    }

    /**
     * Determina si l'usuari pot eliminar una falta.
     *
     * @param mixed $user
     */
    public function delete($user, Falta $falta): bool
    {
        return $this->ownsOrIsDirectionOrAdmin($user, (string) $falta->idProfesor);
    }
}
