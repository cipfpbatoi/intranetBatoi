<?php

declare(strict_types=1);

namespace Intranet\Application\AssumpteParticular;

use Intranet\Entities\AssumpteParticular;
use Intranet\Entities\Horario;
use Intranet\Entities\Profesor;

/**
 * Dedueix el torn docent i la distribució actual de la plantilla.
 */
class TornAssumpteParticularService
{
    /**
     * Dedueix el torn a partir de les classes lectives del professor.
     */
    public function delProfessor(string $dni): string
    {
        $torns = Horario::query()
            ->where('idProfesor', $dni)
            ->lectivos()
            ->join('horas', 'horas.codigo', '=', 'horarios.sesion_orden')
            ->whereNotNull('horas.turno')
            ->distinct()
            ->pluck('horas.turno')
            ->map(static fn ($torn): string => strtoupper(substr((string) $torn, 0, 1)))
            ->unique()
            ->values();

        $mati = $torns->contains('M');
        $vesprada = $torns->contains('V');

        if ($mati && $vesprada) {
            return AssumpteParticular::TORN_AMBDOS;
        }
        if ($mati) {
            return AssumpteParticular::TORN_MATI;
        }
        if ($vesprada) {
            return AssumpteParticular::TORN_VESPRADA;
        }

        return AssumpteParticular::TORN_SENSE_DOCENCIA;
    }

    /**
     * @return array<string, int>
     */
    public function plantillaPerTorn(): array
    {
        $plantilla = [
            AssumpteParticular::TORN_MATI => 0,
            AssumpteParticular::TORN_VESPRADA => 0,
            AssumpteParticular::TORN_AMBDOS => 0,
            AssumpteParticular::TORN_SENSE_DOCENCIA => 0,
        ];

        Profesor::query()->activo()->pluck('dni')->each(function ($dni) use (&$plantilla): void {
            $plantilla[$this->delProfessor((string) $dni)]++;
        });

        return $plantilla;
    }
}
