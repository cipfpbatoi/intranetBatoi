<?php

namespace Intranet\Policies;

use Intranet\Entities\Signatura;
use Intranet\Policies\Concerns\InteractsWithProfesorOwnership;

/**
 * Policy d'autorització per a signatures de FCT.
 */
class SignaturaPolicy
{
    use InteractsWithProfesorOwnership;

    /**
     * Determina si l'usuari pot accedir al panell de signatures de direcció.
     *
     * @param mixed $user
     */
    public function manageDirectionPanel($user): bool
    {
        return $this->hasRole($user, 'roles.rol.direccion')
            || $this->hasRole($user, 'roles.rol.administrador');
    }

    /**
     * Determina si l'usuari pot gestionar fluxos globals de signatures.
     *
     * @param mixed $user
     */
    public function manage($user): bool
    {
        return $this->hasProfesorIdentity($user);
    }

    /**
     * Determina si l'usuari pot crear signatures.
     *
     * @param mixed $user
     */
    public function create($user): bool
    {
        return $this->hasProfesorIdentity($user);
    }

    /**
     * Determina si l'usuari pot veure signatures.
     *
     * @param mixed $user
     */
    public function view($user, Signatura $signatura): bool
    {
        return $this->ownsOrIsDirectionOrAdmin($user, (string) $signatura->idProfesor);
    }

    /**
     * Determina si l'usuari pot actualitzar signatures.
     *
     * @param mixed $user
     */
    public function update($user, Signatura $signatura): bool
    {
        return $this->ownsOrIsDirectionOrAdmin($user, (string) $signatura->idProfesor);
    }

    /**
     * Determina si l'usuari pot eliminar signatures.
     *
     * @param mixed $user
     */
    public function delete($user, Signatura $signatura): bool
    {
        return $this->ownsOrIsDirectionOrAdmin($user, (string) $signatura->idProfesor);
    }
}
