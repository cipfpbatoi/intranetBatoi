<?php

namespace Intranet\Policies;

use Intranet\Entities\TipoActividad;
use Intranet\Policies\Concerns\InteractsWithProfesorOwnership;

/**
 * Policy d'autorització per a tipus d'activitat.
 */
class TipoActividadPolicy
{
    use InteractsWithProfesorOwnership;

    /**
     * Determina si l'usuari pot crear tipus d'activitat.
     *
     * @param mixed $user
     */
    public function create($user): bool
    {
        return $this->isDirectionOrHeadOfDepartment($user);
    }

    /**
     * Determina si l'usuari pot actualitzar un tipus d'activitat.
     *
     * @param mixed $user
     */
    public function update($user, TipoActividad $tipoActividad): bool
    {
        if ($this->hasRole($user, 'roles.rol.direccion') || $this->hasRole($user, 'roles.rol.administrador')) {
            return true;
        }

        return $this->hasRole($user, 'roles.rol.jefe_dpto')
            && is_object($user)
            && isset($user->departamento)
            && (string) $user->departamento === (string) $tipoActividad->departamento_id;
    }

    /**
     * Determina si l'usuari pot eliminar un tipus d'activitat.
     *
     * @param mixed $user
     */
    public function delete($user, TipoActividad $tipoActividad): bool
    {
        return $this->update($user, $tipoActividad);
    }

    /**
     * Comprova si l'usuari té rol de direcció/admin o cap de departament.
     *
     * @param mixed $user
     */
    private function isDirectionOrHeadOfDepartment($user): bool
    {
        return $this->hasRole($user, 'roles.rol.direccion')
            || $this->hasRole($user, 'roles.rol.administrador')
            || $this->hasRole($user, 'roles.rol.jefe_dpto');
    }
}
