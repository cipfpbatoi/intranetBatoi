<?php

declare(strict_types=1);

namespace Tests\Unit\Sao\Support;

use Intranet\Sao\Support\SaoDownloadManager;
use Tests\TestCase;

/**
 * Tests per a utilitats de fitxers temporals SAO.
 */
class SaoDownloadManagerTest extends TestCase
{
    /**
     * Verifica prioritat de directori temporal.
     */
    public function test_temp_directory_uses_share_directory_when_defined(): void
    {
        config(['variables.shareDirectory' => '/tmp/share-sao/']);

        $manager = new SaoDownloadManager();

        $this->assertSame('/tmp/share-sao/', $manager->tempDirectory());
    }

    /**
     * Verifica fallback a configuracio de sao quan no hi ha shareDirectory.
     */
    public function test_temp_directory_uses_sao_config_when_share_directory_is_null(): void
    {
        config(['variables.shareDirectory' => null]);
        config(['sao.download.directory' => '/tmp/sao-config/']);

        $manager = new SaoDownloadManager();

        $this->assertSame('/tmp/sao-config/', $manager->tempDirectory());
    }

    /**
     * Verifica que waitForFile llança timeout quan no apareix fitxer.
     */
    public function test_wait_for_file_throws_timeout_exception(): void
    {
        $manager = new SaoDownloadManager();

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Timeout waiting for file');

        $manager->waitForFile('/tmp/non-existing-sao-file.pdf', -1);
    }

    /**
     * Verifica que unlinkIfExists no falla si el fitxer no existeix.
     */
    public function test_unlink_if_exists_is_safe_when_file_is_missing(): void
    {
        $manager = new SaoDownloadManager();

        $manager->unlinkIfExists('/tmp/non-existing-sao-file.pdf');

        $this->assertTrue(true);
    }
}

