<?php

namespace Intranet\Policies;

use Intranet\Entities\MaterialBaja;
use Intranet\Policies\Concerns\InteractsWithProfesorOwnership;

/**
 * Policy d'autorització per a gestió de baixes de material.
 */
class MaterialBajaPolicy
{
    use InteractsWithProfesorOwnership;

    /**
     * Determina si l'usuari pot actualitzar una baixa de material.
     *
     * @param mixed $user
     */
    public function update($user, MaterialBaja $materialBaja): bool
    {
        return $this->isDirectionOrAdmin($user);
    }

    /**
     * Determina si l'usuari pot eliminar una baixa de material.
     *
     * @param mixed $user
     */
    public function delete($user, MaterialBaja $materialBaja): bool
    {
        return $this->isDirectionOrAdmin($user);
    }

    /**
     * Determina si l'usuari pot recuperar material des de baixa.
     *
     * @param mixed $user
     */
    public function recover($user, MaterialBaja $materialBaja): bool
    {
        return $this->isDirectionOrAdmin($user);
    }
}
