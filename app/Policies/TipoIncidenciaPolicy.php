<?php

namespace Intranet\Policies;

use Intranet\Entities\TipoIncidencia;
use Intranet\Policies\Concerns\InteractsWithProfesorOwnership;

/**
 * Policy d'autorització per a tipus d'incidència.
 */
class TipoIncidenciaPolicy
{
    use InteractsWithProfesorOwnership;

    /**
     * Determina si l'usuari pot crear tipus d'incidència.
     *
     * @param mixed $user
     */
    public function create($user): bool
    {
        return $this->hasRole($user, 'roles.rol.administrador');
    }

    /**
     * Determina si l'usuari pot actualitzar tipus d'incidència.
     *
     * @param mixed $user
     */
    public function update($user, TipoIncidencia $tipoIncidencia): bool
    {
        return $this->hasRole($user, 'roles.rol.administrador');
    }

    /**
     * Determina si l'usuari pot eliminar tipus d'incidència.
     *
     * @param mixed $user
     */
    public function delete($user, TipoIncidencia $tipoIncidencia): bool
    {
        return $this->hasRole($user, 'roles.rol.administrador');
    }
}
