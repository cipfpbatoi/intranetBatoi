<?php

namespace Intranet\Policies;

use Intranet\Entities\Profesor;
use Intranet\Policies\Concerns\InteractsWithProfesorOwnership;

/**
 * Policy d'autorització per a professorat.
 */
class ProfesorPolicy
{
    use InteractsWithProfesorOwnership;

    /**
     * Determina si l'usuari pot actualitzar el perfil d'un professor.
     *
     * @param mixed $user
     */
    public function update($user, Profesor $profesor): bool
    {
        return $this->hasRole($user, 'roles.rol.direccion')
            || $this->hasRole($user, 'roles.rol.administrador');
    }

    /**
     * Determina si l'usuari pot gestionar la qualitat final (cap de pràctiques).
     *
     * @param mixed $user
     */
    public function manageQualityFinal($user, Profesor $profesor): bool
    {
        if (!is_object($user) || !isset($user->dni)) {
            return false;
        }

        return $this->hasRole($user, 'roles.rol.jefe_practicas');
    }

    /**
     * Determina si l'usuari pot gestionar incidències de fitxatge/presència.
     *
     * @param mixed $user
     */
    public function manageAttendance($user): bool
    {
        return $this->hasRole($user, 'roles.rol.direccion')
            || $this->hasRole($user, 'roles.rol.administrador');
    }
}
