<?php

namespace Intranet\Policies;

use Intranet\Entities\Falta;
use Intranet\Policies\Concerns\InteractsWithProfesorOwnership;

/**
 * Autoritza la gestió de faltes, la continuació de baixes i l'esborrat abans d'imprimir.
 */
class FaltaPolicy
{
    use InteractsWithProfesorOwnership;

    /** Restringix la conversió a baixa llarga al personal de Direcció. */
    public function longLeave($user, Falta $falta): bool
    {
        return $this->hasRole($user, 'roles.rol.direccion');
    }

    /** Només Direcció pot continuar una baixa amb un altre motiu. */
    public function continueWithReason($user, Falta $falta): bool
    {
        return $this->hasRole($user, 'roles.rol.direccion');
    }

    /**
     * Determina si l'usuari pot crear una falta.
     *
     * @param mixed $user
     */
    public function create($user): bool
    {
        return $this->hasProfesorIdentity($user);
    }

    /**
     * Determina si l'usuari pot veure una falta.
     *
     * @param mixed $user
     */
    public function view($user, Falta $falta): bool
    {
        return $this->ownsOrIsDirectionOrAdmin($user, (string) $falta->idProfesor);
    }

    /**
     * Determina si l'usuari pot actualitzar una falta.
     *
     * @param mixed $user
     */
    public function update($user, Falta $falta): bool
    {
        return $this->ownsOrIsDirectionOrAdmin($user, (string) $falta->idProfesor);
    }

    /**
     * Determina si l'usuari pot eliminar una falta.
     *
     * @param mixed $user
     */
    public function delete($user, Falta $falta): bool
    {
        return $this->ownsOrIsDirectionOrAdmin($user, (string) $falta->idProfesor);
    }

    /** Només Direcció pot esborrar qualsevol estat abans d'imprimir l'informe mensual. */
    public function deleteBeforePrint($user, Falta $falta): bool
    {
        return $this->hasRole($user, 'roles.rol.direccion')
            && (int) $falta->estado !== 4
            && $falta->idDocumento === null;
    }

    /** Només Direcció pot anul·lar una falta autoritzada no tancada. */
    public function annul($user, Falta $falta): bool
    {
        return $this->hasRole($user, 'roles.rol.direccion')
            && (int) $falta->estado === 3
            && $falta->idDocumento === null;
    }
}
