<?php

declare(strict_types=1);

namespace Intranet\Policies;

use Intranet\Entities\Convalidacio;
use Intranet\Entities\SollicitudConvalidacio;

/**
 * Policy d'autorització per a la gestió de convalidacions.
 */
class ConvalidacioPolicy
{
    /**
     * Comprova si l'usuari té identitat (professor o alumne).
     *
     * @param mixed $user
     */
    private function hasIdentity($user): bool
    {
        return is_object($user) && isset($user->dni) && (string) $user->dni !== '';
    }

    /**
     * Comprova si l'usuari és alumne.
     *
     * @param mixed $user
     */
    private function isAlumno($user): bool
    {
        if (!is_object($user) || !isset($user->rol)) {
            return false;
        }

        return esRol((int) $user->rol, (int) config('roles.rol.alumno'));
    }

    /**
     * Comprova si l'usuari és direcció o administració.
     *
     * @param mixed $user
     */
    private function isDirectionOrAdmin($user): bool
    {
        if (!is_object($user) || !isset($user->rol)) {
            return false;
        }

        return esRol((int) $user->rol, (int) config('roles.rol.direccion'))
            || esRol((int) $user->rol, (int) config('roles.rol.administrador'));
    }

    /**
     * Qualsevol identitat pot veure la llista de convalidacions.
     * El filtratge per alumne es farà al controller.
     *
     * @param mixed $user
     */
    public function viewAny($user): bool
    {
        return $this->hasIdentity($user);
    }

    /**
     * L'alumne pot crear sol·licituds per mòduls del seu cicle actual.
     *
     * @param mixed $user
     */
    public function create($user): bool
    {
        return $this->isAlumno($user);
    }

    /**
     * L'alumne només pot veure les seues pròpies sol·licituds.
     * Direcció i administració poden veure totes.
     *
     * @param mixed $user
     */
    public function view($user, SollicitudConvalidacio $sollicitud): bool
    {
        if ($this->isDirectionOrAdmin($user)) {
            return true;
        }

        return $this->isAlumno($user) && (string) $user->dni === (string) $sollicitud->alumno_id;
    }

    /**
     * L'alumne només pot actualitzar sol·licituds pendents de les seues.
     *
     * @param mixed $user
     */
    public function update($user, SollicitudConvalidacio $sollicitud): bool
    {
        return $sollicitud->estaPendent()
            && $this->isAlumno($user)
            && (string) $user->dni === (string) $sollicitud->alumno_id;
    }

    /**
     * Només Direcció o Administració poden resoldre sol·licituds pendents.
     *
     * @param mixed $user
     */
    public function resolve($user, SollicitudConvalidacio $sollicitud): bool
    {
        return $sollicitud->estaPendent() && $this->isDirectionOrAdmin($user);
    }

    /**
     * L'alumne pot descarregar documents de les seues pròpies sol·licituds.
     * Direcció i administració poden descarregar qualsevol document.
     *
     * @param mixed $user
     */
    public function downloadDocument($user, SollicitudConvalidacio $sollicitud, Convalidacio $convalidacio = null): bool
    {
        if ($this->isDirectionOrAdmin($user)) {
            return true;
        }

        return $this->isAlumno($user) && (string) $user->dni === (string) $sollicitud->alumno_id;
    }
}
