<?php

namespace Intranet\Policies;

use Intranet\Entities\Lote;
use Intranet\Policies\Concerns\InteractsWithProfesorOwnership;

/**
 * Policy d'autorització per a lots d'inventari.
 */
class LotePolicy
{
    use InteractsWithProfesorOwnership;

    /**
     * Determina si l'usuari pot crear lots.
     *
     * @param mixed $user
     */
    public function create($user): bool
    {
        return $this->isDirectionOrAdmin($user);
    }

    /**
     * Determina si l'usuari pot actualitzar lots.
     *
     * @param mixed $user
     */
    public function update($user, Lote $lote): bool
    {
        return $this->isDirectionOrAdmin($user);
    }

    /**
     * Determina si l'usuari pot eliminar lots.
     *
     * @param mixed $user
     */
    public function delete($user, Lote $lote): bool
    {
        return $this->isDirectionOrAdmin($user);
    }
}
