<?php

declare(strict_types=1);

namespace Intranet\Policies;

use Intranet\Entities\Alumno;
use Intranet\Entities\Convalidacio;
use Intranet\Entities\Profesor;
use Intranet\Entities\SollicitudConvalidacio;

/** Autorització de propietat i gestió de convalidacions. */
class ConvalidacioPolicy
{
    /** L'alumne pot consultar una capçalera pròpia. */
    public function view($user, SollicitudConvalidacio $sollicitud): bool
    {
        return $this->canManageRequests($user)
            || ($user instanceof Alumno && (string) $user->nia === (string) $sollicitud->alumno_id);
    }

    /** L'alumne pot consultar o descarregar una petició pròpia. */
    public function viewPeticio($user, Convalidacio $peticio): bool
    {
        return $this->canManageRequests($user)
            || ($user instanceof Alumno && (string) $user->nia === (string) $peticio->sollicitud->alumno_id);
    }

    /** Perfils operatius autoritzats poden consultar el panell de sol·licituds. */
    public function viewRequests($user): bool
    {
        return $this->canManageRequests($user);
    }

    /** L'alumne propietari pot modificar documents només abans de revisar o quan se li demana una correcció. */
    public function correct($user, Convalidacio $peticio): bool
    {
        return $user instanceof Alumno
            && (string) $user->nia === (string) $peticio->sollicitud->alumno_id
            && in_array($peticio->estat, [
                Convalidacio::ESTAT_ENVIADA,
                Convalidacio::ESTAT_REVISAR_DOCUMENTACIO,
            ], true);
    }

    /** Direcció, Secretaria i Administració poden revisar peticions no terminals. */
    public function resolve($user, Convalidacio $peticio): bool
    {
        return $this->canManageRequests($user) && !$peticio->esTerminal();
    }

    /** Secretaria o Direcció poden aportar documents només quan s'han demanat. */
    public function addStaffContribution($user, Convalidacio $peticio): bool
    {
        return ($this->isDirection($user) || $this->isSecretary($user))
            && $peticio->estat === Convalidacio::ESTAT_REVISAR_DOCUMENTACIO;
    }

    /** Perfils operatius autoritzats poden consultar els llistats de sol·licituds i mòduls. */
    public function viewModuleRequests($user): bool
    {
        return $this->canManageRequests($user);
    }

    /** Perfils operatius autoritzats poden registrar el pas a ITACA en la intranet. */
    public function registerModuleRequests($user): bool
    {
        return $this->viewModuleRequests($user);
    }

    /** Perfils operatius autoritzats poden aplicar les regles automàtiques a les sol·licituds. */
    public function applyAutomaticConvalidations($user): bool
    {
        return $this->canManageRequests($user);
    }

    /** Determina si la identitat pertany al perfil de Direcció. */
    private function isDirection($user): bool
    {
        return $user instanceof Profesor
            && esRol((int) $user->rol, (int) config('roles.rol.direccion'));
    }

    /** Comprova si el perfil pot treballar les sol·licituds de convalidació. */
    private function canManageRequests($user): bool
    {
        return $this->isDirection($user)
            || $this->isSecretary($user)
            || ($user instanceof Profesor && esRol((int) $user->rol, (int) config('roles.rol.administrador')));
    }

    /** Comprova el factor independent del rol de Secretaria. */
    private function isSecretary($user): bool
    {
        return $user instanceof Profesor
            && esRol((int) $user->rol, (int) config('roles.rol.secretaria'));
    }
}
