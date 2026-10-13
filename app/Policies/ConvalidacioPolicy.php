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
        return $this->isDirection($user)
            || ($user instanceof Alumno && (string) $user->nia === (string) $sollicitud->alumno_id);
    }

    /** L'alumne pot consultar o descarregar una petició pròpia. */
    public function viewPeticio($user, Convalidacio $peticio): bool
    {
        return $this->isDirection($user)
            || ($user instanceof Alumno && (string) $user->nia === (string) $peticio->sollicitud->alumno_id);
    }

    /** Només l'alumne propietari pot corregir documentació requerida. */
    public function correct($user, Convalidacio $peticio): bool
    {
        return $user instanceof Alumno
            && (string) $user->nia === (string) $peticio->sollicitud->alumno_id
            && $peticio->esOrigenExtern()
            && $peticio->estat === Convalidacio::ESTAT_REVISAR_DOCUMENTACIO;
    }

    /** Només Direcció pot revisar una petició no terminal. */
    public function resolve($user, Convalidacio $peticio): bool
    {
        return $this->isDirection($user) && !$peticio->esTerminal();
    }

    /** Direcció i la persona configurada com a secretària poden consultar el llistat de mòduls. */
    public function viewModuleRequests($user): bool
    {
        return $this->isDirection($user) || $this->isSecretary($user);
    }

    /** Direcció i la persona configurada com a secretària poden registrar el pas a ITACA. */
    public function registerModuleRequests($user): bool
    {
        return $this->viewModuleRequests($user);
    }

    /** Determina si la identitat pertany al perfil de Direcció. */
    private function isDirection($user): bool
    {
        return $user instanceof Profesor
            && esRol((int) $user->rol, (int) config('roles.rol.direccion'));
    }

    /** Comprova el DNI configurat per al càrrec de secretaria. */
    private function isSecretary($user): bool
    {
        $secretaryDni = trim((string) config('avisos.secretario', ''));

        return $user instanceof Profesor
            && $secretaryDni !== ''
            && (string) $user->dni === $secretaryDni;
    }
}
