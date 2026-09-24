<?php

namespace Intranet\Policies;

use Intranet\Entities\Reunion;
use Intranet\Entities\Grupo;
use Intranet\Policies\Concerns\InteractsWithProfesorOwnership;

/**
 * Policy d'autorització per a la gestió de reunions.
 */
class ReunionPolicy
{
    use InteractsWithProfesorOwnership;

    /**
     * Determina si l'usuari pot consultar el llistat de reunions.
     *
     * @param mixed $user
     */
    public function viewAny($user): bool
    {
        return $this->hasProfesorIdentity($user);
    }

    /**
     * Determina si l'usuari pot consultar la reunió i els seus documents.
     *
     * @param mixed $user
     */
    public function view($user, Reunion $reunion): bool
    {
        if (!$this->hasProfesorIdentity($user)) {
            return false;
        }

        if ($this->isOwner($user, $reunion) || $this->isDirectionOrAdmin($user)) {
            return true;
        }

        return $reunion->profesores()
            ->where('profesores.dni', (string) $user->dni)
            ->exists();
    }

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
        return $this->canMutate($user, $reunion);
    }

    /**
     * Determina si l'usuari pot eliminar una reunió oberta.
     *
     * @param mixed $user
     */
    public function delete($user, Reunion $reunion): bool
    {
        return $this->canMutate($user, $reunion);
    }

    /**
     * Determina si l'usuari pot modificar participants de la reunió.
     *
     * @param mixed $user
     */
    public function manageParticipants($user, Reunion $reunion): bool
    {
        return $this->canMutate($user, $reunion);
    }

    /**
     * Determina si l'usuari pot gestionar l'orde de reunió.
     *
     * @param mixed $user
     */
    public function manageOrder($user, Reunion $reunion): bool
    {
        return $this->canMutate($user, $reunion);
    }

    /**
     * Determina si l'usuari pot notificar o enviar correu de la reunió.
     *
     * @param mixed $user
     */
    public function notify($user, Reunion $reunion): bool
    {
        return $this->canMutate($user, $reunion);
    }

    /**
     * Determina si l'usuari pot arxivar la reunió.
     *
     * @param mixed $user
     */
    public function archive($user, Reunion $reunion): bool
    {
        return $this->canMutate($user, $reunion);
    }

    /**
     * Determina si l'usuari pot desarxivar una reunió arxivada.
     *
     * @param mixed $user
     */
    public function unarchive($user, Reunion $reunion): bool
    {
        return $this->isOwner($user, $reunion) && (bool) $reunion->archivada;
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
    private function isOwner($user, Reunion $reunion): bool
    {
        if (!$this->hasProfesorIdentity($user)) {
            return false;
        }

        return (string) $reunion->idProfesor === (string) $user->dni
            || $this->isTutorDelGrupo($user, $reunion);
    }

    /**
     * Determina si l'usuari pot modificar una reunió encara oberta.
     *
     * @param mixed $user
     */
    private function canMutate($user, Reunion $reunion): bool
    {
        return $this->isOwner($user, $reunion) && !(bool) $reunion->archivada;
    }

    /**
     * Determina si l'usuari és tutor actual del grup docent de l'acta.
     *
     * @param mixed $user
     */
    private function isTutorDelGrupo($user, Reunion $reunion): bool
    {
        if (!$reunion->idGrupo) {
            return false;
        }

        return Grupo::qTutor((string) $user->dni)
            ->where('codigo', (string) $reunion->idGrupo)
            ->exists();
    }
}
