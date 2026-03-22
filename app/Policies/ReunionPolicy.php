<?php

namespace Intranet\Policies;

use Intranet\Entities\Reunion;
use Intranet\Policies\Concerns\InteractsWithProfesorOwnership;

/**
 * Policy d'autorització per a la gestió de reunions.
 */
class ReunionPolicy
{
    use InteractsWithProfesorOwnership;

    /**
     * Determina si l'usuari pot crear reunions.
     *
     * @param mixed $user
     */
    public function create($user): bool
    {
        return $this->hasProfesorIdentity($user);
    }

    /**
     * Determina si l'usuari pot veure/editar la reunió.
     *
     * @param mixed $user
     */
    public function update($user, Reunion $reunion): bool
    {
        return $this->isOwner($user, $reunion);
    }

    /**
     * Determina si l'usuari pot modificar participants de la reunió.
     *
     * @param mixed $user
     */
    public function manageParticipants($user, Reunion $reunion): bool
    {
        return $this->isOwner($user, $reunion);
    }

    /**
     * Determina si l'usuari pot gestionar l'orde de reunió.
     *
     * @param mixed $user
     */
    public function manageOrder($user, Reunion $reunion): bool
    {
        return $this->isOwner($user, $reunion);
    }

    /**
     * Determina si l'usuari pot notificar o enviar correu de la reunió.
     *
     * @param mixed $user
     */
    public function notify($user, Reunion $reunion): bool
    {
        return $this->isOwner($user, $reunion);
    }

    /**
     * Determina si l'usuari pot gestionar l'informe trimestral de departament.
     *
     * @param mixed $user
     */
    public function manageDepartmentReport($user): bool
    {
        return $this->hasRole($user, 'roles.rol.jefe_dpto');
    }

    /**
     * @param mixed $user
     */
    /**
     * @param mixed $user
     */
    private function isOwner($user, Reunion $reunion): bool
    {
        return $this->hasProfesorIdentity($user)
            && (string) $reunion->idProfesor === (string) $user->dni;
    }
}
