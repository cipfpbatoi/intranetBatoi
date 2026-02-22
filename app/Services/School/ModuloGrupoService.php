<?php

namespace Intranet\Services\School;

use Illuminate\Support\Collection;
use Intranet\Entities\Horario;
use Intranet\Entities\Modulo_ciclo;
use Intranet\Entities\Modulo_grupo;

class ModuloGrupoService
{
    public function profesoresArray(Modulo_grupo $moduloGrupo): array
    {
        return $this->profesorIds($moduloGrupo)
            ->map(static fn ($idProfesor) => ['idProfesor' => $idProfesor])
            ->values()
            ->all();
    }

    public function profesorIds(Modulo_grupo $moduloGrupo): Collection
    {
        $moduloCiclo = $moduloGrupo->ModuloCiclo;
        if (!$moduloCiclo) {
            return collect();
        }

        return Horario::query()
            ->select('idProfesor')
            ->distinct()
            ->where('idGrupo', $moduloGrupo->idGrupo)
            ->where('modulo', $moduloCiclo->idModulo)
            ->pluck('idProfesor')
            ->filter();
    }

    public function misModulos(string $dni, ?string $modulo = null): array
    {
        $horarios = Horario::query()
            ->Profesor($dni)
            ->whereNotNull('idGrupo')
            ->when($modulo, static function ($query) use ($modulo) {
                return $query->where('modulo', $modulo);
            }, static function ($query) {
                return $query->whereNotIn('modulo', config('constants.modulosNoLectivos'));
            })
            ->with('Grupo:codigo,idCiclo')
            ->get(['modulo', 'idGrupo']);

        if ($horarios->isEmpty()) {
            return [];
        }

        $pairs = [];
        foreach ($horarios as $horario) {
            $grupo = $horario->Grupo;
            if (!$grupo || !$grupo->idCiclo) {
                continue;
            }
            $pairs[$horario->modulo . '|' . $grupo->idCiclo] = [
                'idModulo' => $horario->modulo,
                'idCiclo' => $grupo->idCiclo,
            ];
        }

        if ($pairs === []) {
            return [];
        }

        $moduloCiclos = Modulo_ciclo::query()
            ->where(function ($query) use ($pairs) {
                foreach ($pairs as $pair) {
                    $query->orWhere(function ($subQuery) use ($pair) {
                        $subQuery->where('idModulo', $pair['idModulo'])
                            ->where('idCiclo', $pair['idCiclo']);
                    });
                }
            })
            ->get(['id', 'idModulo', 'idCiclo'])
            ->keyBy(static fn ($mc) => $mc->idModulo . '|' . $mc->idCiclo);

        if ($moduloCiclos->isEmpty()) {
            return [];
        }

        $targetPairs = [];
        foreach ($horarios as $horario) {
            $grupo = $horario->Grupo;
            if (!$grupo || !$grupo->idCiclo) {
                continue;
            }

            $key = $horario->modulo . '|' . $grupo->idCiclo;
            $moduloCiclo = $moduloCiclos->get($key);
            if (!$moduloCiclo) {
                continue;
            }

            $targetPairs[$horario->idGrupo . '|' . $moduloCiclo->id] = true;
        }

        if ($targetPairs === []) {
            return [];
        }

        $grupoIds = [];
        $moduloCicloIds = [];
        foreach (array_keys($targetPairs) as $pair) {
            [$grupoId, $moduloCicloId] = explode('|', $pair);
            $grupoIds[$grupoId] = $grupoId;
            $moduloCicloIds[$moduloCicloId] = (int) $moduloCicloId;
        }

        return Modulo_grupo::query()
            ->whereIn('idGrupo', array_values($grupoIds))
            ->whereIn('idModuloCiclo', array_values($moduloCicloIds))
            ->get()
            ->filter(static fn ($mg) => isset($targetPairs[$mg->idGrupo . '|' . $mg->idModuloCiclo]))
            ->values()
            ->all();
    }
}
