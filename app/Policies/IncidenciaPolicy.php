<?php

namespace Intranet\Policies;

use Intranet\Entities\Incidencia;

class IncidenciaPolicy
{
    public function create($user): bool
    {
        return $this->hasProfesorIdentity($user);
    }

    public function view($user, Incidencia $incidencia): bool
    {
        return $this->ownsOrIsResponsible($user, $incidencia);
    }

    public function update($user, Incidencia $incidencia): bool
    {
        return $this->ownsOrIsResponsible($user, $incidencia);
    }

    public function delete($user, Incidencia $incidencia): bool
    {
        return $this->ownsOrIsResponsible($user, $incidencia);
    }

    private function hasProfesorIdentity($user): bool
    {
        return is_object($user) && isset($user->dni) && (string) $user->dni !== '';
    }

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
