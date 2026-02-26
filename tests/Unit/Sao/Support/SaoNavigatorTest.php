<?php

declare(strict_types=1);

namespace Tests\Unit\Sao\Support;

use Facebook\WebDriver\Remote\RemoteWebDriver;
use Intranet\Sao\Support\SaoNavigator;
use Mockery;
use Tests\TestCase;

/**
 * Tests de navegacio comuna SAO.
 */
class SaoNavigatorTest extends TestCase
{
    /**
     * Verifica que backToMain usa la URL configurada de SAO.
     */
    public function test_back_to_main_uses_configured_url(): void
    {
        config(['sao.urls.main' => 'https://example.test/sao/main']);
        config(['sao.navigation.sleep_seconds' => 0]);

        $driver = Mockery::mock(RemoteWebDriver::class);
        $driver->shouldReceive('get')
            ->once()
            ->with('https://example.test/sao/main');

        $navigator = new SaoNavigator();
        $navigator->backToMain($driver, 0);

        $this->assertTrue(true);
    }
}

