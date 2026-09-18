<?php

declare(strict_types=1);

namespace Intranet\Application\Convalidacio;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Intranet\Entities\Alumno;
use Intranet\Entities\Ciclo;
use Intranet\Entities\Convalidacio;
use Intranet\Entities\Modulo;
use Intranet\Entities\SollicitudConvalidacio;

/** Consultes de lectura del flux de convalidacions. */
class ConvalidacioQueryService
{
    /** Retorna les sol·licituds d'un alumne amb totes les peticions. */
    public function sollicitudsAlumne(string $nia): Collection
    {
        return SollicitudConvalidacio::query()
            ->with(['convalidacions.moduloDestino', 'convalidacions.cicloOrigen'])
            ->where('alumno_id', $nia)
            ->latest('submitted_at')
            ->get();
    }

    /** Retorna el detall complet d'una sol·licitud. */
    public function sollicitudDetail(int $id): ?SollicitudConvalidacio
    {
        return SollicitudConvalidacio::query()
            ->with(['alumno', 'convalidacions.moduloDestino', 'convalidacions.cicloOrigen', 'convalidacions.revisor'])
            ->find($id);
    }

    /** Retorna les sol·licituds visibles per Direcció amb filtres per petició. */
    public function sollicitudsDireccion(?string $estat = null, ?string $origen = null): Collection
    {
        return SollicitudConvalidacio::query()
            ->with(['alumno', 'convalidacions.moduloDestino', 'convalidacions.cicloOrigen'])
            ->when($estat, fn ($query) => $query->whereHas('convalidacions', fn ($q) => $q->where('estat', $estat)))
            ->when($origen, fn ($query) => $query->whereHas('convalidacions', fn ($q) => $q->where('origen', $origen)))
            ->latest('submitted_at')
            ->get();
    }

    /** Retorna els mòduls de la matrícula vigent de l'alumne. */
    public function modulsActuals(Alumno $alumno): Collection
    {
        $grups = $alumno->Grupo()->pluck('grupos.codigo');

        $ids = DB::table('modulo_grupos')
            ->join('modulo_ciclos', 'modulo_ciclos.id', '=', 'modulo_grupos.idModuloCiclo')
            ->whereIn('modulo_grupos.idGrupo', $grups)
            ->pluck('modulo_ciclos.idModulo');

        $bloquejats = Convalidacio::query()
            ->whereHas('sollicitud', fn ($query) => $query->where('alumno_id', $alumno->nia))
            ->where('estat', '!=', Convalidacio::ESTAT_DENEGADA)
            ->pluck('modulo_destino_id');

        return Modulo::query()
            ->whereIn('codigo', $ids)
            ->whereNotIn('codigo', $bloquejats)
            ->orderBy('vliteral')
            ->get();
    }

    /** Retorna els cicles que consten en l'historial acadèmic de l'alumne. */
    public function ciclesPrevis(Alumno $alumno): Collection
    {
        $ids = $alumno->AlumnoResultado()
            ->join('modulo_grupos', 'modulo_grupos.id', '=', 'alumno_resultados.idModuloGrupo')
            ->join('modulo_ciclos', 'modulo_ciclos.id', '=', 'modulo_grupos.idModuloCiclo')
            ->whereNotNull('modulo_ciclos.idCiclo')
            ->distinct()
            ->pluck('modulo_ciclos.idCiclo');

        return Ciclo::query()->whereIn('id', $ids)->orderBy('vliteral')->get();
    }
}
