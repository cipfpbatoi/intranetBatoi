<?php

namespace Intranet\Policies;

use Intranet\Entities\TutoriaGrupo;
use Intranet\Policies\Concerns\InteractsWithProfesorOwnership;

/**
 * Policy d'autorització per a tutories de grup.
 */
class TutoriaGrupoPolicy
{
    use InteractsWithProfesorOwnership;

    /**
     * Determina si l'usuari pot crear registres de tutoria-grup.
     *
     * @param mixed $user
     */
    public function create($user): bool
    {
        return $this->hasProfesorIdentity($user);
    }

    /**
     * Determina si l'usuari pot veure registres de tutoria-grup.
     *
     * @param mixed $user
     */
    public function view($user, TutoriaGrupo $tutoriaGrupo): bool
    {
        return $this->hasProfesorIdentity($user);
    }

    /**
     * Determina si l'usuari pot actualitzar registres de tutoria-grup.
     *
     * @param mixed $user
     */
    public function update($user, TutoriaGrupo $tutoriaGrupo): bool
    {
        return $this->hasProfesorIdentity($user);
    }

    /**
     * Determina si l'usuari pot eliminar registres de tutoria-grup.
     *
     * @param mixed $user
     */
    public function delete($user, TutoriaGrupo $tutoriaGrupo): bool
    {
        return $this->hasProfesorIdentity($user);
    }
}
