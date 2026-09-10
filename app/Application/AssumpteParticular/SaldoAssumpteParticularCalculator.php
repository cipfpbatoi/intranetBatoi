<?php

declare(strict_types=1);

namespace Intranet\Application\AssumpteParticular;

use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Intranet\Entities\Profesor;

/**
 * Calcula el límit anual prorratejat segons el nomenament del professor.
 */
class SaldoAssumpteParticularCalculator
{
    public const DIES_PER_TIPUS = 3.0;

    /**
     * Retorna el saldo màxim d'un tipus per al curs indicat.
     */
    public function limit(Profesor $profesor, CarbonInterface $iniciCurs, CarbonInterface $fiCurs): float
    {
        $iniciNomenament = $this->rawDate($profesor, 'fecha_ingreso') ?? CarbonImmutable::instance($iniciCurs);
        $fiNomenament = $this->rawDate($profesor, 'fecha_baja') ?? CarbonImmutable::instance($fiCurs);

        $inici = $iniciNomenament->greaterThan($iniciCurs)
            ? $iniciNomenament
            : CarbonImmutable::instance($iniciCurs);
        $fi = $fiNomenament->lessThan($fiCurs)
            ? $fiNomenament
            : CarbonImmutable::instance($fiCurs);

        if ($inici->greaterThan($fi)) {
            return 0.0;
        }

        $diesCurs = $iniciCurs->diffInDays($fiCurs) + 1;
        $diesNomenament = $inici->diffInDays($fi) + 1;

        return round(self::DIES_PER_TIPUS * ($diesNomenament / $diesCurs), 4);
    }

    /**
     * Llig una data original sense aplicar els accessors llegats del model.
     */
    private function rawDate(Profesor $profesor, string $camp): ?CarbonImmutable
    {
        $valor = $profesor->getAttributes()[$camp] ?? null;

        return $valor ? CarbonImmutable::parse((string) $valor)->startOfDay() : null;
    }
}
