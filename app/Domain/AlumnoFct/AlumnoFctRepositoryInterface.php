<?php

declare(strict_types=1);

namespace Intranet\Domain\AlumnoFct;

use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Intranet\Entities\AlumnoFct;

/**
 * Contracte d'accés a dades d'AlumnoFct.
 */
interface AlumnoFctRepositoryInterface
{
    /**
     * Recupera tots els registres d'alumnat en FCT.
     *
     * @return EloquentCollection<int, AlumnoFct>
     */
    public function all(): EloquentCollection;

    /**
     * Recupera les FCT visibles per a un tutor (incloent substitucions).
     *
     * @return EloquentCollection<int, AlumnoFct>
     */
    public function totesFcts(?string $profesor = null): EloquentCollection;

    /**
     * Cerca un registre per identificador.
     */
    public function find(int|string $id): ?AlumnoFct;

    /**
     * Cerca un registre per identificador o llança excepció.
     */
    public function findOrFail(int|string $id): AlumnoFct;

    /**
     * Recupera el primer registre associat a un id SAO.
     */
    public function firstByIdSao(int|string $idSao): ?AlumnoFct;

    /**
     * Llista tots els registres d'un alumne.
     *
     * @return EloquentCollection<int, AlumnoFct>
     */
    public function byAlumno(string $nia): EloquentCollection;

    /**
     * Llista registres d'un alumne amb annex A56 en curs.
     *
     * @return EloquentCollection<int, AlumnoFct>
     */
    public function byAlumnoWithA56(string $nia): EloquentCollection;

    /**
     * Llista registres d'un grup que són FCT (no dual).
     *
     * @return EloquentCollection<int, AlumnoFct>
     */
    public function byGrupoEsFct(string $grupo): EloquentCollection;

    /**
     * Llista registres d'un grup que són dual.
     *
     * @return EloquentCollection<int, AlumnoFct>
     */
    public function byGrupoEsDual(string $grupo): EloquentCollection;

    /**
     * Reassigna tutor responsable en bloc.
     */
    public function reassignProfesor(string $fromDni, string $toDni): int;
}
