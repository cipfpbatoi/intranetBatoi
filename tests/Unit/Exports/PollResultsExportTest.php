<?php

declare(strict_types=1);

namespace Tests\Unit\Exports;

use Intranet\Exports\PollResultsExport;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Tests\TestCase;

/**
 * Proves unitàries de l'exportació de resultats de polls.
 */
class PollResultsExportTest extends TestCase
{
    public function test_la_fulla_grups_formata_les_mitjanes_amb_dos_decimals(): void
    {
        $export = new PollResultsExport(
            (object) ['title' => 'Enquesta FE'],
            ['grup' => []],
            collect([
                (object) ['id' => 1],
                (object) ['id' => 2],
            ]),
            collect(),
            ['grup' => []],
            [],
            [],
            []
        );

        $sheets = $export->sheets();

        $this->assertSame(
            [
                'D' => NumberFormat::FORMAT_NUMBER_00,
                'E' => NumberFormat::FORMAT_NUMBER_00,
            ],
            $sheets[2]->columnFormats()
        );
    }
}
