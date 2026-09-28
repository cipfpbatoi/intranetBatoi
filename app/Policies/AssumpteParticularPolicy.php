<?php

declare(strict_types=1);

namespace Intranet\Policies;

use Intranet\Entities\AssumpteParticular;
use Intranet\Policies\Concerns\InteractsWithProfesorOwnership;

/**
 * Policy d'autorització per a les peticions d'assumptes particulars.
 */
class AssumpteParticularPolicy
{
    use InteractsWithProfesorOwnership;

    /**
     * Qualsevol identitat de professor pot iniciar una petició pròpia.
     *
     * @param mixed $user
     */
    public function create($user): bool
    {
        return $this->hasProfesorIdentity($user);
    }

    /**
     * El propietari i Direcció poden consultar la petició.
     *
     * @param mixed $user
     */
    public function view($user, AssumpteParticular $peticio): bool
    {
        return $this->ownsOrIsDirectionOrAdmin($user, (string) $peticio->idProfesor);
    }

    /**
     * El professor només pot modificar una petició pròpia pendent.
     *
     * @param mixed $user
     */
    public function update($user, AssumpteParticular $peticio): bool
    {
        return $peticio->estaPendent()
            && $this->hasProfesorIdentity($user)
            && (string) $user->dni === (string) $peticio->idProfesor;
    }

    /**
     * El professor només pot cancel·lar una petició pròpia pendent.
     *
     * @param mixed $user
     */
    public function cancel($user, AssumpteParticular $peticio): bool
    {
        return $this->update($user, $peticio);
    }

    /**
     * Només Direcció o Administració poden resoldre peticions pendents.
     *
     * @param mixed $user
     */
    public function resolve($user, AssumpteParticular $peticio): bool
    {
        return $peticio->estaPendent() && $this->isDirectionOrAdmin($user);
    }

    /**
     * Qualsevol membre de Direcció pot autoritzar amb la rúbrica de la directora configurada.
     *
     * @param mixed $user
     */
    public function approve($user, AssumpteParticular $peticio): bool
    {
        return $this->resolve($user, $peticio)
            && filled(config('avisos.director'))
            && esRol($user->rol, config('roles.rol.direccion'));
    }

    /**
     * Només Direcció pot registrar autoritzacions històriques.
     *
     * @param mixed $user
     */
    public function regularize($user): bool
    {
        return $this->hasProfesorIdentity($user)
            && esRol($user->rol, config('roles.rol.direccion'));
    }
}
