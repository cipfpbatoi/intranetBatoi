<?php

namespace Intranet\Policies;

use Intranet\Entities\Espacio;
use Intranet\Policies\Concerns\InteractsWithProfesorOwnership;

/**
 * Policy d'autorització per a espais.
 */
class EspacioPolicy
{
    use InteractsWithProfesorOwnership;

    /**
     * Determina si l'usuari pot crear espais.
     *
     * @param mixed $user
     */
    public function create($user): bool
    {
        return $this->isDirectionOrAdmin($user);
    }

    /**
     * Determina si l'usuari pot actualitzar espais.
     *
     * @param mixed $user
     */
    public function update($user, Espacio $espacio): bool
    {
        return $this->isDirectionOrAdmin($user);
    }

    /**
     * Determina si l'usuari pot eliminar espais.
     *
     * @param mixed $user
     */
    public function delete($user, Espacio $espacio): bool
    {
        return $this->isDirectionOrAdmin($user);
    }

    /**
     * Determina si l'usuari pot imprimir codis de barres de l'espai.
     *
     * @param mixed $user
     */
    public function printBarcode($user, Espacio $espacio): bool
    {
        return $this->isDirectionOrAdmin($user);
    }
}
