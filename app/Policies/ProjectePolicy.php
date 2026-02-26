<?php

namespace Intranet\Policies;

use Intranet\Application\Grupo\GrupoService;
use Intranet\Entities\Projecte;
use Intranet\Policies\Concerns\InteractsWithProfesorOwnership;

/**
 * Policy d'autorització per al flux de propostes de projecte.
 */
class ProjectePolicy
{
    use InteractsWithProfesorOwnership;

    /**
     * Determina si l'usuari pot crear propostes dins del seu grup de tutoria.
     *
     * @param mixed $user
     */
    public function create($user): bool
    {
        return $this->isTutorOfAnyGroup($user);
    }

    /**
     * Determina si l'usuari pot vore una proposta del seu grup de tutoria.
     *
     * @param mixed $user
     */
    public function view($user, Projecte $projecte): bool
    {
        return $this->isTutorOfGroup($user, (string) $projecte->grup);
    }

    /**
     * Determina si l'usuari pot actualitzar una proposta del seu grup.
     *
     * @param mixed $user
     */
    public function update($user, Projecte $projecte): bool
    {
        return $this->view($user, $projecte);
    }

    /**
     * Determina si l'usuari pot eliminar una proposta del seu grup.
     *
     * @param mixed $user
     */
    public function delete($user, Projecte $projecte): bool
    {
        return $this->view($user, $projecte);
    }

    /**
     * Determina si l'usuari pot validar una proposta del seu grup.
     *
     * @param mixed $user
     */
    public function check($user, Projecte $projecte): bool
    {
        return $this->view($user, $projecte);
    }

    /**
     * Determina si l'usuari pot enviar projectes del seu grup.
     *
     * @param mixed $user
     */
    public function send($user): bool
    {
        return $this->isTutorOfAnyGroup($user);
    }

    /**
     * Determina si l'usuari pot crear l'acta de valoració del seu grup.
     *
     * @param mixed $user
     */
    public function createActa($user): bool
    {
        return $this->isTutorOfAnyGroup($user);
    }

    /**
     * Determina si l'usuari pot crear l'acta de defenses del seu grup.
     *
     * @param mixed $user
     */
    public function createDefenseActa($user): bool
    {
        return $this->isTutorOfAnyGroup($user);
    }

    /**
     * @param mixed $user
     */
    private function isTutorOfAnyGroup($user): bool
    {
        if (!$this->hasProfesorIdentity($user)) {
            return false;
        }

        return app(GrupoService::class)->byTutorOrSubstitute((string) $user->dni, $user->sustituye_a ?? null) !== null;
    }

    /**
     * @param mixed $user
     */
    private function isTutorOfGroup($user, string $groupCode): bool
    {
        if (!$this->hasProfesorIdentity($user) || $groupCode === '') {
            return false;
        }

        $grupoTutor = app(GrupoService::class)->byTutorOrSubstitute((string) $user->dni, $user->sustituye_a ?? null);
        if ($grupoTutor === null) {
            return false;
        }

        return (string) $grupoTutor->codigo === $groupCode;
    }
}
