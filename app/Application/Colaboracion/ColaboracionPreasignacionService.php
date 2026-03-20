<?php

declare(strict_types=1);

namespace Intranet\Application\Colaboracion;

use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use Intranet\Entities\Alumno;
use Intranet\Entities\Colaboracion;
use Intranet\Entities\ColaboracionPreasignacion;
use RuntimeException;

/**
 * Casos d'ús de preassignació d'alumnat sobre col·laboracions.
 */
class ColaboracionPreasignacionService
{
    /**
     * Hidrata el panell amb reserves i opcions d'alumnat disponibles per cicle.
     *
     * @param Collection<int, Colaboracion> $colaboraciones
     * @return Collection<int, Colaboracion>
     */
    public function hydrateForPanel(Collection $colaboraciones, ?string $idProfesor = null): Collection
    {
        if ($colaboraciones->isEmpty()) {
            return $colaboraciones;
        }

        $preasignacionesByColaboracion = ColaboracionPreasignacion::query()
            ->with(['Alumno.Grupo', 'Profesor'])
            ->whereIn('idColaboracion', $colaboraciones->pluck('id')->all())
            ->orderByDesc('created_at')
            ->get()
            ->groupBy('idColaboracion');

        $alumnosByCiclo = $this->availableAlumnoOptionsByCiclo(
            $colaboraciones->pluck('idCiclo')->filter()->unique()->values()->all(),
            $idProfesor
        );

        $colaboraciones->each(function (Colaboracion $colaboracion) use ($preasignacionesByColaboracion, $alumnosByCiclo): void {
            $colaboracion->preasignacionesPanel = $preasignacionesByColaboracion
                ->get($colaboracion->id, collect())
                ->values();
            $colaboracion->preasignacionAlumnoOptions = $alumnosByCiclo
                ->get((int) $colaboracion->idCiclo, collect());
        });

        return $colaboraciones;
    }

    /**
     * Crea una nova proposta o reserva per a una col·laboració.
     *
     * @throws RuntimeException
     */
    public function create(
        int|string $idColaboracion,
        string $idAlumno,
        string $idProfesor,
        string $estado = 'proposta',
        ?string $observaciones = null
    ): ColaboracionPreasignacion {
        if (!in_array($estado, ColaboracionPreasignacion::STATES, true)) {
            throw new RuntimeException('L\'estat de preassignació no és vàlid.');
        }

        $colaboracion = Colaboracion::query()->findOrFail($idColaboracion);

        $this->guardDuplicateInColaboracion((int) $colaboracion->id, $idAlumno);
        $this->guardAlumnoCycleConflict($colaboracion, $idAlumno);
        $this->guardCapacity($colaboracion, $estado);

        return ColaboracionPreasignacion::query()->create([
            'idColaboracion' => (int) $colaboracion->id,
            'idAlumno' => $idAlumno,
            'idProfesor' => $idProfesor,
            'estado' => $estado,
            'observaciones' => $observaciones,
        ]);
    }

    /**
     * Retorna totes les preassignacions d'una col·laboració.
     *
     * @return EloquentCollection<int, ColaboracionPreasignacion>
     */
    public function byColaboracion(int|string $idColaboracion): EloquentCollection
    {
        return ColaboracionPreasignacion::query()
            ->with(['Alumno', 'Profesor'])
            ->where('idColaboracion', $idColaboracion)
            ->orderByDesc('created_at')
            ->get();
    }

    /**
     * Elimina una preassignació del panell del tutor.
     *
     * @throws RuntimeException
     */
    public function descartar(int|string $id): void
    {
        $preasignacion = ColaboracionPreasignacion::query()->find($id);
        if (!$preasignacion) {
            throw new RuntimeException('La preassignació no existeix.');
        }

        $preasignacion->delete();
    }

    /**
     * Marca una reserva com a convertida després de crear la FCT real.
     *
     * @throws RuntimeException
     */
    public function marcarComConvertida(int|string $id): ColaboracionPreasignacion
    {
        $preasignacion = ColaboracionPreasignacion::query()->find($id);
        if (!$preasignacion) {
            throw new RuntimeException('La preassignació no existeix.');
        }

        $preasignacion->estado = 'convertida';
        $preasignacion->save();

        return $preasignacion->fresh();
    }

    /**
     * Retorna opcions d'alumnat agrupades per cicle.
     *
     * @param array<int, int|string> $cicloIds
     * @param string|null $idProfesor
     * @return Collection<int, Collection<string, string>>
     */
    public function availableAlumnoOptionsByCiclo(array $cicloIds, ?string $idProfesor = null): Collection
    {
        if ($cicloIds === []) {
            return collect();
        }

        $idProfesor = $idProfesor ?: (authUser()->dni ?? null);
        $reservedAlumnoIdsByCiclo = ColaboracionPreasignacion::query()
            ->select(['idAlumno', 'idColaboracion'])
            ->whereIn('estado', ColaboracionPreasignacion::ACTIVE_STATES)
            ->whereHas('Colaboracion', function ($query) use ($cicloIds): void {
                $query->whereIn('idCiclo', $cicloIds);
            })
            ->with('Colaboracion:id,idCiclo')
            ->get()
            ->groupBy(fn (ColaboracionPreasignacion $preasignacion): int => (int) $preasignacion->Colaboracion->idCiclo)
            ->map(fn (Collection $items): Collection => $items->pluck('idAlumno')->unique()->values());

        $query = Alumno::query()
            ->with('Grupo:codigo,idCiclo')
            ->whereHas('Grupo', function ($query) use ($cicloIds): void {
                $query->whereIn('idCiclo', $cicloIds);
            });

        if ($idProfesor) {
            $query->MisAlumnos($idProfesor);
        }

        return $query
            ->get()
            ->flatMap(function (Alumno $alumno) {
                return $alumno->Grupo
                    ->pluck('idCiclo')
                    ->filter()
                    ->unique()
                    ->map(function ($idCiclo) use ($alumno) {
                        return [
                            'idCiclo' => (int) $idCiclo,
                            'nia' => (string) $alumno->nia,
                            'label' => (string) $alumno->fullName,
                        ];
                    });
            })
            ->groupBy('idCiclo')
            ->map(function (Collection $items, int $idCiclo) use ($reservedAlumnoIdsByCiclo): Collection {
                $reservedIds = $reservedAlumnoIdsByCiclo->get($idCiclo, collect());

                return $items
                    ->reject(fn (array $item): bool => $reservedIds->contains($item['nia']))
                    ->sortBy('label', SORT_NATURAL | SORT_FLAG_CASE)
                    ->mapWithKeys(fn (array $item): array => [$item['nia'] => $item['label']]);
            });
    }

    /**
     * Evita duplicats actius per al mateix alumne dins de la mateixa col·laboració.
     *
     * @throws RuntimeException
     */
    private function guardDuplicateInColaboracion(int $idColaboracion, string $idAlumno): void
    {
        $exists = ColaboracionPreasignacion::query()
            ->where('idColaboracion', $idColaboracion)
            ->where('idAlumno', $idAlumno)
            ->whereIn('estado', ColaboracionPreasignacion::ACTIVE_STATES)
            ->exists();

        if ($exists) {
            throw new RuntimeException('L\'alumne ja està preassignat en esta col·laboració.');
        }
    }

    /**
     * Evita reserves simultànies del mateix alumne en el mateix cicle.
     *
     * @throws RuntimeException
     */
    private function guardAlumnoCycleConflict(Colaboracion $colaboracion, string $idAlumno): void
    {
        $conflict = ColaboracionPreasignacion::query()
            ->where('idAlumno', $idAlumno)
            ->whereIn('estado', ColaboracionPreasignacion::ACTIVE_STATES)
            ->whereHas('Colaboracion', function ($query) use ($colaboracion): void {
                $query->where('idCiclo', $colaboracion->idCiclo)
                    ->where('id', '<>', $colaboracion->id);
            })
            ->exists();

        if ($conflict) {
            throw new RuntimeException('L\'alumne ja té una altra preassignació activa en este cicle.');
        }
    }

    /**
     * Respecta el límit de places ofert per la col·laboració.
     *
     * @throws RuntimeException
     */
    private function guardCapacity(Colaboracion $colaboracion, string $estado): void
    {
        if (!in_array($estado, ColaboracionPreasignacion::ACTIVE_STATES, true)) {
            return;
        }

        $activeCount = ColaboracionPreasignacion::query()
            ->where('idColaboracion', $colaboracion->id)
            ->whereIn('estado', ColaboracionPreasignacion::ACTIVE_STATES)
            ->count();

        $puestos = max(1, (int) $colaboracion->puestos);

        if ($activeCount >= $puestos) {
            throw new RuntimeException('La col·laboració ja no té places lliures per a noves preassignacions.');
        }
    }
}
