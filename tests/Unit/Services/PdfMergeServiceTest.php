<?php

namespace Tests\Unit\Services;

use InvalidArgumentException;
use Intranet\Services\Document\PdfMergeService;
use Tests\TestCase;

class PdfMergeServiceTest extends TestCase
{
    public function test_merge_llanca_excepcio_si_no_hi_ha_fitxers(): void
    {
        $this->expectException(InvalidArgumentException::class);

        app(PdfMergeService::class)->merge([], storage_path('tmp/test-merge.pdf'));
    }

    public function test_merge_llanca_excepcio_si_un_fitxer_no_existix(): void
    {
        $this->expectException(InvalidArgumentException::class);

        app(PdfMergeService::class)->merge(
            [storage_path('tmp/no-existix.pdf')],
            storage_path('tmp/test-merge.pdf')
        );
    }
}
