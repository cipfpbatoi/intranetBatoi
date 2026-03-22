<?php

namespace Intranet\Policies;

use Intranet\Entities\Curso;
use Intranet\Policies\Concerns\InteractsWithProfesorOwnership;

/**
 * Policy d'autorització per a cursos.
 */
class CursoPolicy
{
    use InteractsWithProfesorOwnership;

    /**
     * Determina si l'usuari pot accedir als llistats de cursos.
     *
     * @param mixed $user
     */
    public function viewAny($user): bool
    {
        return $this->hasProfesorIdentity($user);
    }

    /**
     * Determina si l'usuari pot crear cursos.
     *
     * @param mixed $user
     */
    public function create($user): bool
    {
        return $this->hasProfesorIdentity($user);
    }

    /**
     * Determina si l'usuari pot actualitzar cursos.
     *
     * @param mixed $user
     */
    public function update($user, Curso $curso): bool
    {
        return $this->hasProfesorIdentity($user);
    }

    /**
     * Determina si l'usuari pot eliminar cursos.
     *
     * @param mixed $user
     */
    public function delete($user, Curso $curso): bool
    {
        return $this->hasProfesorIdentity($user);
    }
}
