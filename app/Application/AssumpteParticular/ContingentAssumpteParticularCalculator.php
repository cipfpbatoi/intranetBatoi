<?php

declare(strict_types=1);

namespace Intranet\Application\AssumpteParticular;

/**
 * Repartix el màxim diari proporcionalment entre els torns de la plantilla.
 */
class ContingentAssumpteParticularCalculator
{
    public const MAXIM_DIARI = 8;

    /**
     * @param array<string, int> $plantillaPerTorn
     * @return array<string, int>
     */
    public function quotes(array $plantillaPerTorn): array
    {
        $plantillaPerTorn = array_map(
            static fn (int $persones): int => max(0, $persones),
            $plantillaPerTorn
        );
        $totalPlantilla = array_sum($plantillaPerTorn);
        $places = min(self::MAXIM_DIARI, $totalPlantilla);

        if ($places === 0) {
            return array_fill_keys(array_keys($plantillaPerTorn), 0);
        }

        $quotes = [];
        $restes = [];
        foreach ($plantillaPerTorn as $torn => $persones) {
            $quotaExacta = $places * $persones / $totalPlantilla;
            $quotes[$torn] = (int) floor($quotaExacta);
            $restes[$torn] = $quotaExacta - $quotes[$torn];
        }

        $pendents = $places - array_sum($quotes);
        uksort($restes, static function (string $esquerra, string $dreta) use ($restes): int {
            return $restes[$dreta] <=> $restes[$esquerra] ?: $esquerra <=> $dreta;
        });

        foreach (array_keys($restes) as $torn) {
            if ($pendents === 0) {
                break;
            }
            if ($plantillaPerTorn[$torn] > 0) {
                $quotes[$torn]++;
                $pendents--;
            }
        }

        return $quotes;
    }
}
