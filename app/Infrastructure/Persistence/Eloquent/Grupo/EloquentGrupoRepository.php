<?php

declare(strict_types=1);

namespace Intranet\Infrastructure\Persistence\Eloquent\Grupo;

use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Intranet\Domain\Grupo\GrupoRepositoryInterface;
use Intranet\Entities\Grupo;

class EloquentGrupoRepository implements GrupoRepositoryInterface
{
    public function create(array $attributes): Grupo
    {
        /** @var Grupo $grupo */
        $grupo = Grupo::create($attributes);
        if (isset($attributes['codigo'])) {
            $loaded = Grupo::find((string) $attributes['codigo']);
            if ($loaded instanceof Grupo) {
                return $loaded;
            }
        }
        return $grupo;
    }

    public function find(string $codigo): ?Grupo
    {
        return Grupo::find($codigo);
    }

    public function all(): EloquentCollection
    {
        return Grupo::all();
    }

    public function qTutor(string $dni): EloquentCollection
    {
        return Grupo::query()->QTutor($dni)->get();
    }

    public function firstByTutor(string $dni): ?Grupo
    {
        return Grupo::query()->QTutor($dni)->first();
    }

    public function largestByTutor(string $dni): ?Grupo
    {
        return Grupo::query()->QTutor($dni)->largestByAlumnes()->first();
    }

    public function byCurso(int $curso): EloquentCollection
    {
        return Grupo::query()->Curso($curso)->get();
    }

    public function byDepartamento(int $departamento): EloquentCollection
    {
        return Grupo::query()->Departamento($departamento)->get();
    }

    public function tutoresDniList(): array
    {
        return Grupo::query()
            ->whereNotNull('tutor')
            ->where('tutor', '<>', '')
            ->pluck('tutor')
            ->map(static fn ($dni): string => trim((string) $dni))
            ->filter(static fn (string $dni): bool => $dni !== '' && $dni !== 'BAJA' && $dni !== 'SIN TUTOR')
            ->values()
            ->all();
    }

    public function reassignTutor(string $fromDni, string $toDni): int
    {
        return Grupo::query()
            ->where('tutor', $fromDni)
            ->update(['tutor' => $toDni]);
    }

    public function misGrupos(): EloquentCollection
    {
        return Grupo::query()->MisGrupos()->get();
    }

    public function misGruposByProfesor(string $dni): EloquentCollection
    {
        return Grupo::query()
            ->MisGrupos((object) ['dni' => $dni])
            ->get();
    }

    public function withActaPendiente(): EloquentCollection
    {
        return Grupo::query()
            ->where('acta_pendiente', '>', 0)
            ->get();
    }

    public function byTutorOrSubstitute(string $dni, ?string $sustituyeA): ?Grupo
    {
        return Grupo::query()
            ->where('tutor', $dni)
            ->orWhere(static function ($query) use ($sustituyeA): void {
                if ($sustituyeA !== null && trim($sustituyeA) !== '') {
                    $query->where('tutor', trim($sustituyeA));
                }
            })
            ->first();
    }

    public function withStudents(): EloquentCollection
    {
        return Grupo::query()
            ->whereHas('alumnos')
            ->get();
    }

    public function firstByTutorDual(string $dni): ?Grupo
    {
        return Grupo::query()
            ->where('tutorDual', $dni)
            ->first();
    }

    public function byCodes(array $codigos): EloquentCollection
    {
        return Grupo::query()
            ->whereIn('codigo', $codigos)
            ->get();
    }

    public function allWithTutorAndCiclo(): EloquentCollection
    {
        return Grupo::query()
            ->with(['Ciclo', 'Tutor', 'Tutor.Sustituye'])
            ->get();
    }

    public function misGruposWithCiclo(): EloquentCollection
    {
        return Grupo::query()
            ->with('Ciclo')
            ->MisGrupos()
            ->get();
    }
}
