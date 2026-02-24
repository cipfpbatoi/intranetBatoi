<?php

namespace Intranet\Policies;

use Intranet\Entities\Incidencia;
use Intranet\Policies\Concerns\InteractsWithProfesorOwnership;

/**
 * Policy d'autorització per a incidències.
 */
class IncidenciaPolicy
{
    use InteractsWithProfesorOwnership;

    /**
     * Determina si l'usuari pot crear incidències.
     *
     * @param mixed $user
     */
    public function create($user): bool
    {
        return $this->hasProfesorIdentity($user);
    }

    /**
     * Determina si l'usuari pot veure una incidència.
     *
     * @param mixed $user
     */
    public function view($user, Incidencia $incidencia): bool
    {
        return $this->ownsOrIsResponsible($user, $incidencia);
    }

    /**
     * Determina si l'usuari pot actualitzar una incidència.
     *
     * @param mixed $user
     */
    public function update($user, Incidencia $incidencia): bool
    {
        return $this->ownsOrIsResponsible($user, $incidencia);
    }

    /**
     * Determina si l'usuari pot eliminar una incidència.
     *
     * @param mixed $user
     */
    public function delete($user, Incidencia $incidencia): bool
    {
        return $this->ownsOrIsResponsible($user, $incidencia);
    }

    /**
     * Regla: creador o responsable.
     *
     * @param mixed $user
     */
    private function ownsOrIsResponsible($user, Incidencia $incidencia): bool
    {
        if (!$this->hasProfesorIdentity($user)) {
            return false;
        }

        $dni = (string) $user->dni;

        return (string) $incidencia->idProfesor === $dni
            || (string) ($incidencia->responsable ?? '') === $dni;
    }
}
