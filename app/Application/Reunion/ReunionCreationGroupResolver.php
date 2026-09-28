<?php

declare(strict_types=1);

namespace Intranet\Application\Reunion;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Validation\ValidationException;
use Intranet\Application\Grupo\GrupoService;
use Intranet\Entities\Grupo;
use Intranet\Entities\Profesor;

/**
 * Resol els grups docents disponibles en crear una reunió.
 */
class ReunionCreationGroupResolver
{
    public function __construct(private readonly GrupoService $grupoService)
    {
    }

    /**
     * Retorna els grups que el professor pot seleccionar en el formulari.
     *
     * El grup actual es conserva en edició perquè una acta antiga autoritzada
     * no perda el seu valor encara que haja canviat la tutoria.
     *
     * @return EloquentCollection<int, Grupo>
     */
    public function availableFor(Profesor $creator, ?string $currentGroupCode = null): EloquentCollection
    {
        $groups = $this->isDirectionOrAdmin($creator)
            ? $this->grupoService->all()
            : $this->grupoService->qTutor((string) $creator->dni);

        if ($currentGroupCode !== null && !$groups->contains('codigo', $currentGroupCode)) {
            $currentGroup = $this->grupoService->find($currentGroupCode);
            if ($currentGroup !== null) {
                $groups->push($currentGroup);
            }
        }

        return $groups->unique('codigo')->values();
    }

    /**
     * Resol i autoritza el grup enviat per a una reunió de grup docent.
     *
     * @throws AuthorizationException
     * @throws ValidationException
     */
    public function resolve(Profesor $creator, ?string $requestedGroupCode): string
    {
        if ($requestedGroupCode === null) {
            $defaultGroup = $this->grupoService->largestByTutor((string) $creator->dni);
            if ($defaultGroup === null) {
                throw ValidationException::withMessages([
                    'idGrupo' => 'Cal seleccionar un grup docent autoritzat.',
                ]);
            }

            return (string) $defaultGroup->codigo;
        }

        if ($this->isDirectionOrAdmin($creator)) {
            $group = $this->grupoService->find($requestedGroupCode);
            if ($group === null) {
                throw ValidationException::withMessages([
                    'idGrupo' => 'El grup docent seleccionat no existeix.',
                ]);
            }

            return (string) $group->codigo;
        }

        $allowed = $this->grupoService->qTutor((string) $creator->dni)
            ->firstWhere('codigo', $requestedGroupCode);
        if ($allowed === null) {
            throw new AuthorizationException(
                'Només pots crear actes per als grups que tutoritzes.'
            );
        }

        return (string) $allowed->codigo;
    }

    /**
     * Indica si el professor té accés institucional a tots els grups.
     */
    private function isDirectionOrAdmin(Profesor $creator): bool
    {
        $role = (int) $creator->rol;
        $directionRole = (int) config('roles.rol.direccion');
        $adminRole = (int) config('roles.rol.administrador');

        return $role > 0 && (
            ($directionRole > 0 && $role % $directionRole === 0)
            || ($adminRole > 0 && $role % $adminRole === 0)
        );
    }
}
