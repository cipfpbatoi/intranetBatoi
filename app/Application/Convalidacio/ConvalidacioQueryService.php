<?php

declare(strict_types=1);

namespace Intranet\Application\Convalidacio;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Intranet\Entities\Alumno;
use Intranet\Entities\Convalidacio;
use Intranet\Entities\Modulo;
use Intranet\Entities\SollicitudConvalidacio;

/** Consultes de lectura del flux de convalidacions. */
class ConvalidacioQueryService
{
    public function __construct(private readonly ResultatsAcademicsXmlService $resultatsAcademics)
    {
    }

    /** Retorna les sol·licituds d'un alumne amb totes les peticions. */
    public function sollicitudsAlumne(string $nia): Collection
    {
        return SollicitudConvalidacio::query()
            ->with(['convalidacions.moduloDestino', 'convalidacions.documents'])
            ->where('alumno_id', $nia)
            ->latest('submitted_at')
            ->get();
    }

    /** Retorna el detall complet d'una sol·licitud. */
    public function sollicitudDetail(int $id): ?SollicitudConvalidacio
    {
        return SollicitudConvalidacio::query()
            ->with(['alumno', 'convalidacions.moduloDestino', 'convalidacions.revisor', 'convalidacions.documents'])
            ->find($id);
    }

    /** Retorna les sol·licituds visibles per Direcció amb filtres per petició. */
    public function sollicitudsDireccion(?string $estat = null, ?string $origen = null): Collection
    {
        return SollicitudConvalidacio::query()
            ->with(['alumno', 'convalidacions.moduloDestino', 'convalidacions.documents'])
            ->when($estat, fn ($query) => $query->whereHas('convalidacions', fn ($q) => $q->where('estat', $estat)))
            ->when($origen, fn ($query) => $query->whereHas('convalidacions', fn ($q) => $q->where('origen', $origen)))
            ->latest('submitted_at')
            ->get();
    }

    /** Retorna totes les capçaleres sense filtrar-les pels estats de les peticions. */
    public function totesSollicitudsDireccion(): Collection
    {
        return SollicitudConvalidacio::query()
            ->with(['alumno', 'convalidacions.moduloDestino'])
            ->latest('submitted_at')
            ->get();
    }

    /** Retorna una fila per petició, opcionalment filtrada pel seu estat. */
    public function peticionsDireccion(array $filters = []): Collection
    {
        return Convalidacio::query()
            ->with(['sollicitud.alumno', 'moduloDestino'])
            ->when($filters['estat'] ?? null, fn ($query, $estat) => $query->where('estat', $estat))
            ->latest('created_at')
            ->get();
    }

    /** Ordena una col·lecció carregada per al nom visible i desempata per NIA i identificador. */
    public function ordenarPerNomAlumne(Collection $peticions): Collection
    {
        return $peticions->sort(static function (Convalidacio $a, Convalidacio $b): int {
            $nomA = mb_strtolower((string) ($a->sollicitud?->alumno?->fullName ?? $a->sollicitud?->alumno_id));
            $nomB = mb_strtolower((string) ($b->sollicitud?->alumno?->fullName ?? $b->sollicitud?->alumno_id));

            return [$nomA, (string) $a->sollicitud?->alumno_id, (int) $a->id]
                <=> [$nomB, (string) $b->sollicitud?->alumno_id, (int) $b->id];
        })->values();
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

    /** Retorna els mòduls aprovats que consten en els XML privats. */
    public function modulsAprovats(Alumno $alumno): array
    {
        return $this->resultatsAcademics->aprovats((string) $alumno->nia);
    }
}
