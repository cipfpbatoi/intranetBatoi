<?php

declare(strict_types=1);

namespace Intranet\Application\AlumnoFct;

use DB;
use Illuminate\Support\Collection;
use Intranet\Entities\Documento;

class AlumnoFctAvalService
{
    public function __construct(private readonly AlumnoFctService $alumnoFctService)
    {
    }

    public function latestByProfesor(string $dni): Collection
    {
        $nombres = $this->alumnoFctService->avalDistinctAlumnoIdsByProfesor($dni);
        $todas = collect();
        foreach ($nombres as $nombre) {
            $fct = $this->alumnoFctService->latestAvalByAlumnoAndProfesor((string) $nombre, $dni);
            if ($fct !== null) {
                $todas->push($fct);
            }
        }

        return $todas;
    }

    public function apte(int|string $id): void
    {
        $fct = $this->alumnoFctService->findOrFail($id);
        $fct->calificacion = 1;
        $fct->save();
    }

    public function noApte(int|string $id, bool $projectRequired): void
    {
        $fct = $this->alumnoFctService->findOrFail($id);
        $fct->calificacion = 0;
        $fct->calProyecto = $projectRequired ? 0 : null;
        $fct->save();
    }

    public function noAval(int|string $id): void
    {
        $fct = $this->alumnoFctService->findOrFail($id);
        $fct->calificacion = null;
        $fct->calProyecto = null;
        $fct->actas = 0;
        $fct->save();
    }

    public function noProyecto(int|string $id): void
    {
        $fct = $this->alumnoFctService->findOrFail($id);
        $fct->calProyecto = 0;
        $fct->save();
    }

    public function nullProyecto(int|string $id): void
    {
        DB::transaction(function () use ($id): void {
            $fct = $this->alumnoFctService->findOrFail($id);
            $fct->calProyecto = null;
            $fct->save();

            $doc = Documento::where('tipoDocumento', 'Proyecto')
                ->where('curso', Curso())
                ->whereNull('idDocumento')
                ->where('propietario', $fct->fullName)
                ->first();
            if ($doc) {
                $doc->deleteDoc();
            }
        });
    }

    public function nuevoProyecto(int|string $id): void
    {
        $fct = $this->alumnoFctService->findOrFail($id);
        $fct->calProyecto = null;
        $fct->actas = 1;
        $fct->save();
    }

    public function toggleInsercion(int|string $id): void
    {
        $fct = $this->alumnoFctService->findOrFail($id);
        $fct->insercion = $fct->insercion ? 0 : 1;
        $fct->save();
    }

    public function requestActaForTutor(string $dni, Collection $grupos): array
    {
        $pendents = [];
        $demanades = [];
        $senseAlumnes = [];

        foreach ($grupos as $grupo) {
            if ($grupo->acta_pendiente) {
                $pendents[] = $grupo->nombre;
                continue;
            }

            if ($this->markStudentsAsActaPending($dni, (bool) $grupo->proyecto, $grupo)) {
                $grupo->acta_pendiente = 1;
                $grupo->save();
                avisa(
                    config('avisos.jefeEstudios2'),
                    "Acta pendent grup $grupo->nombre",
                    config('contacto.host.web') . "/direccion/$grupo->codigo/acta"
                );
                $demanades[] = $grupo->nombre;
            } else {
                $senseAlumnes[] = $grupo->nombre;
            }
        }

        return [
            'pendents' => $pendents,
            'demanades' => $demanades,
            'senseAlumnes' => $senseAlumnes,
        ];
    }

    public function estadistiques(Collection $grupos): array
    {
        $ciclos = [];
        foreach ($grupos as $grupo) {
            $ciclo = $grupo->idCiclo;
            $ciclos[$ciclo]['matriculados'] = isset($ciclos[$ciclo]['matriculados'])
                ? $ciclos[$ciclo]['matriculados'] + $grupo->matriculados
                : $grupo->matriculados;
            $ciclos[$ciclo]['resfct'] = isset($ciclos[$ciclo]['resfct'])
                ? $ciclos[$ciclo]['resfct'] + $grupo->AprobFct
                : $grupo->AprobFct;
            $ciclos[$ciclo]['exentos'] = isset($ciclos[$ciclo]['exentos'])
                ? $ciclos[$ciclo]['exentos'] + $grupo->exentos
                : $grupo->exentos;
            $ciclos[$ciclo]['respro'] = isset($ciclos[$ciclo]['respro'])
                ? $ciclos[$ciclo]['respro'] + $grupo->AprobPro
                : $grupo->AprobPro;
            $ciclos[$ciclo]['avalpro'] = isset($ciclos[$ciclo]['avalpro'])
                ? $ciclos[$ciclo]['avalpro'] + $grupo->AvalPro
                : $grupo->AvalPro;
            $ciclos[$ciclo]['resempresa'] = isset($ciclos[$ciclo]['resempresa'])
                ? $ciclos[$ciclo]['resempresa'] + $grupo->colocados
                : $grupo->colocados;
            $ciclos[$ciclo]['avalfct'] = isset($ciclos[$ciclo]['avalfct'])
                ? $ciclos[$ciclo]['avalfct'] + $grupo->avalFct
                : $grupo->avalFct;
        }

        return $ciclos;
    }

    private function markStudentsAsActaPending(string $dni, bool $projectNeeded, mixed $grupo = null): bool
    {
        $found = false;
        foreach ($this->alumnoFctService->avaluablesNoAval($dni, $grupo) as $fct) {
            if ($projectNeeded) {
                if (isset($fct->calProyecto)) {
                    $fct->actas = 3;
                    $fct->save();
                    $found = true;
                }
            } elseif (isset($fct->calificacion)) {
                $fct->actas = 3;
                $fct->save();
                $found = true;
            }
        }

        return $found;
    }
}
