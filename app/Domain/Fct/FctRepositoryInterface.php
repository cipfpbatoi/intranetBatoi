<?php

declare(strict_types=1);

namespace Intranet\Domain\Fct;

use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Intranet\Entities\Colaborador;
use Intranet\Entities\Fct;

/**
 * Contracte de persistència per al domini FCT.
 */
interface FctRepositoryInterface
{
    public function find(int|string $id): ?Fct;

    public function findOrFail(int|string $id): Fct;

    public function firstByColaboracionAsociacionInstructor(
        int|string $idColaboracion,
        int|string $asociacion,
        int|string $idInstructor
    ): ?Fct;

    /**
     * @return EloquentCollection<int, Fct>
     */
    public function panelListingByProfesor(string $dni): EloquentCollection;

    public function save(Fct $fct): Fct;

    public function create(array $attributes): Fct;

    public function attachAlumno(
        int|string $idFct,
        string $idAlumno,
        array $pivotAttributes
    ): void;

    public function detachAlumno(int|string $idFct, string $idAlumno): void;

    public function saveColaborador(int|string $idFct, Colaborador $colaborador): void;

    public function deleteColaborador(int|string $idFct, string $idInstructor): int;

    public function updateColaboradorHoras(int|string $idFct, string $idInstructor, int|string $horas): int;

    public function setCotutor(int|string $idFct, ?string $cotutor): void;

    public function empresaIdByFct(int|string $idFct): ?int;
}

