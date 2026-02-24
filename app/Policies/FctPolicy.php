<?php

namespace Intranet\Policies;

use Intranet\Entities\Fct;

class FctPolicy
{
    public function create($user): bool
    {
        return $this->canMutate($user);
    }

    public function update($user, Fct $fct): bool
    {
        return $this->canMutate($user);
    }

    private function canMutate($user): bool
    {
        if (!is_object($user) || !isset($user->rol)) {
            return false;
        }

        $rol = (int) $user->rol;

        return $rol % (int) config('roles.rol.tutor') === 0
            || $rol % (int) config('roles.rol.practicas') === 0
            || $rol % (int) config('roles.rol.jefe_practicas') === 0;
    }
}

