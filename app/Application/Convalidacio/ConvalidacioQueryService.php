<?php

declare(strict_types=1);

namespace Intranet\Application\Convalidacio;

use Illuminate\Support\Collection;
use Intranet\Entities\SollicitudConvalidacio;

/**
 * Servei de consultes per a la gestió de convalidacions.
 */
class ConvalidacioQueryService
{
    /**
     * Retorna totes les sol·licituds d'un alumne.
     */
    public function sollicitudsAlumne(string $alumnoId): Collection
    {
        return SollicitudConvalidacio::query()
            ->with(['convalidacions.modulo', 'convalidacions.cicleFormatiuCursat'])
            ->where('alumno_id', $alumnoId)
            ->orderBy('data_sol·licitud', 'desc')
            ->get();
    }

    /**
     * Retorna el detall d'una sol·licitud.
     */
    public function sollicitudDetail(int $sollicitudId): ?SollicitudConvalidacio
    {
        return SollicitudConvalidacio::query()
            ->with([
                'alumno',
                'convalidacions.modulo',
                'convalidacions.cicleFormatiuCursat',
            ])
            ->find($sollicitudId);
    }

    /**
     * Retorna totes les sol·licituds pendents per a Direcció.
     */
    public function sollicitudsDireccion(): Collection
    {
        return SollicitudConvalidacio::query()
            ->with([
                'alumno',
                'convalidacions.modulo',
                'convalidacions.cicleFormatiuCursat',
            ])
            ->where('estat', SollicitudConvalidacio::ESTAT_PENDENT)
            ->orderBy('data_sol·licitud', 'asc')
            ->get();
    }

    /**
     * Retorna les sol·licituds d'un alumne filtrades per estat.
     */
    public function sollicitudsAlumnePerEstat(string $alumnoId, string $estat): Collection
    {
        return SollicitudConvalidacio::query()
            ->with(['convalidacions.modulo', 'convalidacions.cicleFormatiuCursat'])
            ->where('alumno_id', $alumnoId)
            ->where('estat', $estat)
            ->orderBy('data_sol·licitud', 'desc')
            ->get();
    }

    /**
     * Retorna una convalidació amb totes les relacions.
     */
    public function convalidacioDetail(int $convalidacioId): ?\Intranet\Entities\Convalidacio
    {
        return \Intranet\Entities\Convalidacio::query()
            ->with([
                'sollicitud.alumno',
                'modulo',
                'cicleFormatiuCursat.alumno',
            ])
            ->find($convalidacioId);
    }
}
