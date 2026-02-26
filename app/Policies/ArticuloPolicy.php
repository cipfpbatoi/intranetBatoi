<?php

namespace Intranet\Policies;

use Intranet\Entities\Articulo;
use Intranet\Policies\Concerns\InteractsWithProfesorOwnership;

/**
 * Policy d'autorització per a catàleg d'articles.
 */
class ArticuloPolicy
{
    use InteractsWithProfesorOwnership;

    /**
     * Determina si l'usuari pot veure articles.
     *
     * @param mixed $user
     */
    public function view($user, Articulo $articulo): bool
    {
        return $this->hasProfesorIdentity($user);
    }

    /**
     * Determina si l'usuari pot crear articles.
     *
     * @param mixed $user
     */
    public function create($user): bool
    {
        return $this->hasProfesorIdentity($user);
    }

    /**
     * Determina si l'usuari pot actualitzar articles.
     *
     * @param mixed $user
     */
    public function update($user, Articulo $articulo): bool
    {
        return $this->hasProfesorIdentity($user);
    }

    /**
     * Determina si l'usuari pot eliminar articles.
     *
     * @param mixed $user
     */
    public function delete($user, Articulo $articulo): bool
    {
        return $this->hasProfesorIdentity($user);
    }
}
