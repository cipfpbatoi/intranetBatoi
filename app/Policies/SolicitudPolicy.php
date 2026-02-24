<?php

namespace Intranet\Policies;

use Intranet\Entities\Solicitud;
use Intranet\Policies\Concerns\InteractsWithProfesorOwnership;

/**
 * Policy d'autorització per a sol·licituds.
 */
class SolicitudPolicy
{
    use InteractsWithProfesorOwnership;

    /**
     * Determina si l'usuari pot crear sol·licituds.
     *
     * @param mixed $user
     */
    public function create($user): bool
    {
        return $this->hasProfesorIdentity($user);
    }

    /**
     * Determina si l'usuari pot veure sol·licituds.
     *
     * @param mixed $user
     */
    public function view($user, Solicitud $solicitud): bool
    {
        return $this->ownsOrIsDirectionOrAdmin($user, (string) $solicitud->idProfesor);
    }

    /**
     * Determina si l'usuari pot actualitzar sol·licituds.
     *
     * @param mixed $user
     */
    public function update($user, Solicitud $solicitud): bool
    {
        if ($this->ownsOrIsDirectionOrAdmin($user, (string) $solicitud->idProfesor)) {
            return true;
        }

        return $this->hasProfesorIdentity($user)
            && (string) $user->dni === (string) ($solicitud->idOrientador ?? '');
    }

    /**
     * Determina si l'usuari pot activar una sol·licitud d'orientació.
     *
     * @param mixed $user
     */
    public function activate($user, Solicitud $solicitud): bool
    {
        return $this->hasProfesorIdentity($user)
            && (string) $user->dni === (string) ($solicitud->idOrientador ?? '');
    }

    /**
     * Determina si l'usuari pot resoldre una sol·licitud d'orientació.
     *
     * @param mixed $user
     */
    public function resolve($user, Solicitud $solicitud): bool
    {
        return $this->activate($user, $solicitud);
    }

    /**
     * Determina si l'usuari pot eliminar sol·licituds.
     *
     * @param mixed $user
     */
    public function delete($user, Solicitud $solicitud): bool
    {
        return $this->ownsOrIsDirectionOrAdmin($user, (string) $solicitud->idProfesor);
    }
}
