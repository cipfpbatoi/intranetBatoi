<?php

namespace Intranet\Policies;

use Intranet\Entities\Resultado;
use Intranet\Policies\Concerns\InteractsWithProfesorOwnership;

/**
 * Policy d'autorització per a resultats acadèmics.
 */
class ResultadoPolicy
{
    use InteractsWithProfesorOwnership;

    /**
     * Determina si l'usuari pot crear resultats.
     *
     * @param mixed $user
     */
    public function create($user): bool
    {
        return $this->hasProfesorIdentity($user);
    }

    /**
     * Determina si l'usuari pot veure resultats.
     *
     * @param mixed $user
     */
    public function view($user, Resultado $resultado): bool
    {
        return $this->ownsOrIsDirectionOrAdmin($user, (string) $resultado->idProfesor);
    }

    /**
     * Determina si l'usuari pot actualitzar resultats.
     *
     * @param mixed $user
     */
    public function update($user, Resultado $resultado): bool
    {
        return $this->ownsOrIsDirectionOrAdmin($user, (string) $resultado->idProfesor);
    }

    /**
     * Determina si l'usuari pot eliminar resultats.
     *
     * @param mixed $user
     */
    public function delete($user, Resultado $resultado): bool
    {
        return $this->ownsOrIsDirectionOrAdmin($user, (string) $resultado->idProfesor);
    }
}
