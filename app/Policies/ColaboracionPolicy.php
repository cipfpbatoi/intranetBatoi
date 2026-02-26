<?php

namespace Intranet\Policies;

use Intranet\Entities\Colaboracion;

class ColaboracionPolicy
{
    public function create($user): bool
    {
        return $this->isTutor($user);
    }

    public function update($user, Colaboracion $colaboracion): bool
    {
        return $this->isTutor($user);
    }

    private function isTutor($user): bool
    {
        if (!is_object($user) || !isset($user->rol)) {
            return false;
        }

        return ((int) $user->rol) % (int) config('roles.rol.tutor') === 0;
    }
}

