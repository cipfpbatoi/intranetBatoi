<?php

namespace Intranet\Policies;

use Intranet\Entities\Poll\Option;
use Intranet\Policies\Concerns\InteractsWithProfesorOwnership;

/**
 * Policy d'autorització per a opcions de polls.
 */
class OptionPolicy
{
    use InteractsWithProfesorOwnership;

    /**
     * Determina si l'usuari pot crear opcions.
     *
     * @param mixed $user
     */
    public function create($user): bool
    {
        return $this->hasRole($user, 'roles.rol.qualitat');
    }

    /**
     * Determina si l'usuari pot eliminar opcions.
     *
     * @param mixed $user
     */
    public function delete($user, Option $option): bool
    {
        return $this->hasRole($user, 'roles.rol.qualitat');
    }
}
