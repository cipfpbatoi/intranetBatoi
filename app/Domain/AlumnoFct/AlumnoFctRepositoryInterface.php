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
     * @return EloquentCollection<int, AlumnoFct>
     */
    public function all(): EloquentCollection;

    /**
     * @return EloquentCollection<int, AlumnoFct>
     */
    public function totesFcts(?string $profesor = null): EloquentCollection;

    public function find(int|string $id): ?AlumnoFct;

    public function findOrFail(int|string $id): AlumnoFct;

    public function firstByIdSao(int|string $idSao): ?AlumnoFct;

    /**
     * @return EloquentCollection<int, AlumnoFct>
     */
    public function byAlumno(string $nia): EloquentCollection;

    /**
     * @return EloquentCollection<int, AlumnoFct>
     */
    public function byAlumnoWithA56(string $nia): EloquentCollection;

    /**
     * @return EloquentCollection<int, AlumnoFct>
     */
    public function byGrupoEsFct(string $grupo): EloquentCollection;

    /**
     * @return EloquentCollection<int, AlumnoFct>
     */
    public function byGrupoEsDual(string $grupo): EloquentCollection;

    public function reassignProfesor(string $fromDni, string $toDni): int;
}
