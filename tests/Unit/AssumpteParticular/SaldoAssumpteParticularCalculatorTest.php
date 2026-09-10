<?php

declare(strict_types=1);

namespace Tests\Unit\AssumpteParticular;

use Carbon\CarbonImmutable;
use Intranet\Application\AssumpteParticular\SaldoAssumpteParticularCalculator;
use Intranet\Entities\Profesor;
use PHPUnit\Framework\TestCase;

/**
 * Proves del prorrateig del saldo per nomenament.
 */
class SaldoAssumpteParticularCalculatorTest extends TestCase
{
    public function test_concedix_tres_dies_per_tipus_si_el_nomenament_cobrix_tot_el_curs(): void
    {
        $profesor = new Profesor();
        $profesor->setRawAttributes(['fecha_ingreso' => '2020-09-01', 'fecha_baja' => null]);

        $limit = (new SaldoAssumpteParticularCalculator())->limit(
            $profesor,
            CarbonImmutable::parse('2026-09-01'),
            CarbonImmutable::parse('2027-07-31')
        );

        $this->assertSame(3.0, $limit);
    }

    public function test_conserva_decimals_en_un_nomenament_parcial(): void
    {
        $profesor = new Profesor();
        $profesor->setRawAttributes([
            'fecha_ingreso' => '2026-09-01',
            'fecha_baja' => '2027-02-14',
        ]);

        $limit = (new SaldoAssumpteParticularCalculator())->limit(
            $profesor,
            CarbonImmutable::parse('2026-09-01'),
            CarbonImmutable::parse('2027-07-31')
        );

        $this->assertGreaterThan(1.0, $limit);
        $this->assertLessThan(2.0, $limit);
        $this->assertNotSame(floor($limit), $limit);
    }
}
