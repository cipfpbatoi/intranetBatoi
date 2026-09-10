<?php

declare(strict_types=1);

namespace Tests\Unit\AssumpteParticular;

use Intranet\Application\AssumpteParticular\ContingentAssumpteParticularCalculator;
use PHPUnit\Framework\TestCase;

/**
 * Proves del repartiment proporcional del contingent diari.
 */
class ContingentAssumpteParticularCalculatorTest extends TestCase
{
    public function test_repartix_les_huit_places_proporcionalment(): void
    {
        $quotes = (new ContingentAssumpteParticularCalculator())->quotes([
            'mati' => 60,
            'vesprada' => 30,
            'ambdos' => 10,
        ]);

        $this->assertSame(8, array_sum($quotes));
        $this->assertSame(['mati' => 5, 'vesprada' => 2, 'ambdos' => 1], $quotes);
    }

    public function test_no_assigna_mes_places_que_persones_en_plantilla(): void
    {
        $quotes = (new ContingentAssumpteParticularCalculator())->quotes([
            'mati' => 2,
            'vesprada' => 1,
        ]);

        $this->assertSame(3, array_sum($quotes));
        $this->assertSame(['mati' => 2, 'vesprada' => 1], $quotes);
    }
}
