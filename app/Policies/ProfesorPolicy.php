<?php

namespace Intranet\Policies;

use Intranet\Entities\Profesor;

class ProfesorPolicy
{
    public function manageQualityFinal($user, Profesor $profesor): bool
    {
        if (!is_object($user) || !isset($user->rol) || !isset($user->dni)) {
            return false;
        }

        return ((int) $user->rol) % (int) config('roles.rol.jefe_practicas') === 0;
    }
}
