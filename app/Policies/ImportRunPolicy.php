<?php

namespace Intranet\Policies;

use Intranet\Entities\ImportRun;

class ImportRunPolicy
{
    public function manage($user): bool
    {
        if (!is_object($user) || !isset($user->rol)) {
            return false;
        }

        return ((int) $user->rol) % (int) config('roles.rol.administrador') === 0;
    }

    public function viewAny($user): bool
    {
        return $this->manage($user);
    }

    public function view($user, ImportRun $importRun): bool
    {
        return $this->manage($user);
    }
}
