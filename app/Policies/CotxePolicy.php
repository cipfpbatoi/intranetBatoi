<?php

namespace Intranet\Policies;

use Intranet\Entities\Cotxe;
use Intranet\Policies\Concerns\InteractsWithProfesorOwnership;

/**
 * Policy d'autorització per a vehicles de professorat.
 */
class CotxePolicy
{
    use InteractsWithProfesorOwnership;

    /**
     * Determina si l'usuari pot crear vehicles.
     *
     * @param mixed $user
     */
    public function create($user): bool
    {
        return $this->hasProfesorIdentity($user);
    }

    /**
     * Determina si l'usuari pot veure vehicles.
     *
     * @param mixed $user
     */
    public function view($user, Cotxe $cotxe): bool
    {
        return $this->ownsOrIsDirectionOrAdmin($user, (string) ($cotxe->idProfesor ?? ''));
    }

    /**
     * Determina si l'usuari pot actualitzar vehicles.
     *
     * @param mixed $user
     */
    public function update($user, Cotxe $cotxe): bool
    {
        return $this->ownsOrIsDirectionOrAdmin($user, (string) ($cotxe->idProfesor ?? ''));
    }

    /**
     * Determina si l'usuari pot eliminar vehicles.
     *
     * @param mixed $user
     */
    public function delete($user, Cotxe $cotxe): bool
    {
        return $this->ownsOrIsDirectionOrAdmin($user, (string) ($cotxe->idProfesor ?? ''));
    }
}
