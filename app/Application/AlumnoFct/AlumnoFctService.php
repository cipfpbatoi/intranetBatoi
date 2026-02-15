<?php

declare(strict_types=1);

namespace Intranet\Application\AlumnoFct;

use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Intranet\Domain\AlumnoFct\AlumnoFctRepositoryInterface;
use Intranet\Entities\AlumnoFct;

/**
 * Servei d'aplicació per a casos d'ús d'AlumnoFct.
 */
class AlumnoFctService
{
    public function __construct(private readonly AlumnoFctRepositoryInterface $alumnoFctRepository)
    {
    }

    /**
     * @return EloquentCollection<int, AlumnoFct>
     */
    public function all(): EloquentCollection
    {
        return $this->alumnoFctRepository->all();
    }

    /**
     * @return EloquentCollection<int, AlumnoFct>
     */
    public function totesFcts(?string $profesor = null): EloquentCollection
    {
        return $this->alumnoFctRepository->totesFcts($profesor);
    }

    public function find(int|string $id): ?AlumnoFct
    {
        return $this->alumnoFctRepository->find($id);
    }

    public function findOrFail(int|string $id): AlumnoFct
    {
        return $this->alumnoFctRepository->findOrFail($id);
    }

    public function firstByIdSao(int|string $idSao): ?AlumnoFct
    {
        return $this->alumnoFctRepository->firstByIdSao($idSao);
    }

    /**
     * @return EloquentCollection<int, AlumnoFct>
     */
    public function byAlumno(string $nia): EloquentCollection
    {
        return $this->alumnoFctRepository->byAlumno($nia);
    }

    /**
     * @return EloquentCollection<int, AlumnoFct>
     */
    public function byAlumnoWithA56(string $nia): EloquentCollection
    {
        return $this->alumnoFctRepository->byAlumnoWithA56($nia);
    }

    /**
     * @return EloquentCollection<int, AlumnoFct>
     */
    public function byGrupoEsFct(string $grupo): EloquentCollection
    {
        return $this->alumnoFctRepository->byGrupoEsFct($grupo);
    }

    /**
     * @return EloquentCollection<int, AlumnoFct>
     */
    public function byGrupoEsDual(string $grupo): EloquentCollection
    {
        return $this->alumnoFctRepository->byGrupoEsDual($grupo);
    }

    public function reassignProfesor(string $fromDni, string $toDni): int
    {
        return $this->alumnoFctRepository->reassignProfesor($fromDni, $toDni);
    }
}
