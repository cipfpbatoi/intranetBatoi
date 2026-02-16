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
     * Recupera tots els registres d'alumnat en FCT.
     *
     * @return EloquentCollection<int, AlumnoFct>
     */
    public function all(): EloquentCollection
    {
        return $this->alumnoFctRepository->all();
    }

    /**
     * Recupera les FCT visibles per al tutor indicat.
     *
     * @return EloquentCollection<int, AlumnoFct>
     */
    public function totesFcts(?string $profesor = null): EloquentCollection
    {
        return $this->alumnoFctRepository->totesFcts($profesor);
    }

    /**
     * Cerca un registre per identificador.
     */
    public function find(int|string $id): ?AlumnoFct
    {
        return $this->alumnoFctRepository->find($id);
    }

    /**
     * Cerca un registre per identificador o llança excepció.
     */
    public function findOrFail(int|string $id): AlumnoFct
    {
        return $this->alumnoFctRepository->findOrFail($id);
    }

    /**
     * Recupera el primer registre associat a un id SAO.
     */
    public function firstByIdSao(int|string $idSao): ?AlumnoFct
    {
        return $this->alumnoFctRepository->firstByIdSao($idSao);
    }

    /**
     * Llista tots els registres d'un alumne.
     *
     * @return EloquentCollection<int, AlumnoFct>
     */
    public function byAlumno(string $nia): EloquentCollection
    {
        return $this->alumnoFctRepository->byAlumno($nia);
    }

    /**
     * Llista registres d'un alumne amb annex A56 en curs.
     *
     * @return EloquentCollection<int, AlumnoFct>
     */
    public function byAlumnoWithA56(string $nia): EloquentCollection
    {
        return $this->alumnoFctRepository->byAlumnoWithA56($nia);
    }

    /**
     * Llista registres d'un grup que són FCT.
     *
     * @return EloquentCollection<int, AlumnoFct>
     */
    public function byGrupoEsFct(string $grupo): EloquentCollection
    {
        return $this->alumnoFctRepository->byGrupoEsFct($grupo);
    }

    /**
     * Llista registres d'un grup que són dual.
     *
     * @return EloquentCollection<int, AlumnoFct>
     */
    public function byGrupoEsDual(string $grupo): EloquentCollection
    {
        return $this->alumnoFctRepository->byGrupoEsDual($grupo);
    }

    /**
     * Reassigna en bloc el tutor responsable.
     */
    public function reassignProfesor(string $fromDni, string $toDni): int
    {
        return $this->alumnoFctRepository->reassignProfesor($fromDni, $toDni);
    }

    /**
     * Recupera identificadors d'alumnes amb FCT avaluable del tutor.
     *
     * @return array<int, string>
     */
    public function avalDistinctAlumnoIdsByProfesor(?string $profesor = null): array
    {
        return $this->alumnoFctRepository->avalDistinctAlumnoIdsByProfesor($profesor);
    }

    /**
     * Recupera l'últim registre avaluable d'un alumne per tutor.
     */
    public function latestAvalByAlumnoAndProfesor(string $idAlumno, ?string $profesor = null): ?AlumnoFct
    {
        return $this->alumnoFctRepository->latestAvalByAlumnoAndProfesor($idAlumno, $profesor);
    }

    /**
     * Recupera registres avaluables no tancats en acta.
     *
     * @return EloquentCollection<int, AlumnoFct>
     */
    public function avaluablesNoAval(?string $profesor = null, mixed $grupo = null): EloquentCollection
    {
        return $this->alumnoFctRepository->avaluablesNoAval($profesor, $grupo);
    }
}
