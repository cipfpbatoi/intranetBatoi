<?php

namespace Intranet\Policies;

use Intranet\Entities\GrupoTrabajo;
use Intranet\Entities\Miembro;
use Intranet\Policies\Concerns\InteractsWithProfesorOwnership;

/**
 * Policy d'autorització per a grups de treball.
 */
class GrupoTrabajoPolicy
{
    use InteractsWithProfesorOwnership;

    /**
     * Determina si l'usuari pot crear grups de treball.
     *
     * @param mixed $user
     */
    public function create($user): bool
    {
        return $this->hasProfesorIdentity($user);
    }

    /**
     * Determina si l'usuari pot actualitzar un grup de treball.
     *
     * @param mixed $user
     */
    public function update($user, GrupoTrabajo $grupoTrabajo): bool
    {
        return $this->isOwner($user, $grupoTrabajo);
    }

    /**
     * Determina si l'usuari pot eliminar un grup de treball.
     *
     * @param mixed $user
     */
    public function delete($user, GrupoTrabajo $grupoTrabajo): bool
    {
        return $this->isOwner($user, $grupoTrabajo);
    }

    /**
     * Determina si l'usuari pot gestionar membres/coordinador del grup.
     *
     * @param mixed $user
     */
    public function manageMembers($user, GrupoTrabajo $grupoTrabajo): bool
    {
        return $this->isOwner($user, $grupoTrabajo);
    }

    /**
     * Determina si l'usuari pot veure els membres del grup.
     *
     * @param mixed $user
     */
    public function viewMembers($user, GrupoTrabajo $grupoTrabajo): bool
    {
        return $this->isOwner($user, $grupoTrabajo)
            || $this->isMember($user, $grupoTrabajo);
    }

    /**
     * @param mixed $user
     */
    private function isOwner($user, GrupoTrabajo $grupoTrabajo): bool
    {
        return $this->hasProfesorIdentity($user)
            && (string) $grupoTrabajo->Creador() === (string) $user->dni;
    }

    /**
     * @param mixed $user
     */
    private function isMember($user, GrupoTrabajo $grupoTrabajo): bool
    {
        if (!$this->hasProfesorIdentity($user)) {
            return false;
        }

        return Miembro::query()
            ->where('idGrupoTrabajo', $grupoTrabajo->id)
            ->where('idProfesor', (string) $user->dni)
            ->exists();
    }
}
