<?php

namespace Intranet\Policies;

use Intranet\Entities\Documento;

/**
 * Policy d'autorització per a la gestió de documents.
 */
class DocumentoPolicy
{
    /**
     * Determina si l'usuari pot accedir als panells de llistat documental.
     *
     * @param mixed $user
     */
    public function viewAny($user): bool
    {
        return $this->hasIdentity($user);
    }

    /**
     * Determina si l'usuari pot crear documents.
     *
     * @param mixed $user
     */
    public function create($user): bool
    {
        return $this->hasIdentity($user);
    }

    /**
     * Determina si l'usuari pot veure documents.
     *
     * @param mixed $user
     */
    public function view($user, Documento $documento): bool
    {
        return $this->hasIdentity($user);
    }

    /**
     * Determina si l'usuari pot actualitzar documents.
     *
     * @param mixed $user
     */
    public function update($user, Documento $documento): bool
    {
        return $this->hasIdentity($user);
    }

    /**
     * Determina si l'usuari pot eliminar documents.
     *
     * @param mixed $user
     */
    public function delete($user, Documento $documento): bool
    {
        return $this->hasIdentity($user);
    }

    /**
     * @param mixed $user
     */
    private function hasIdentity($user): bool
    {
        return is_object($user) && isset($user->dni) && (string) $user->dni !== '';
    }
}
