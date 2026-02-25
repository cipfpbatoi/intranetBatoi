<?php

declare(strict_types=1);

namespace Tests\Unit\Sao;

use Exception;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Illuminate\Http\RedirectResponse;
use Intranet\Sao\SaoSyncAction;
use Mockery;
use Tests\TestCase;

/**
 * Tests de caracterització del cicle de vida del driver en Sync.
 */
class SyncLifecycleTest extends TestCase
{
    /**
     * Verifica que sempre es tanca la sessió Selenium en execució normal.
     */
    public function test_execute_quits_driver_when_callback_returns_empty_collection(): void
    {
        $driver = Mockery::mock(RemoteWebDriver::class);
        $driver->shouldReceive('quit')->once();

        $callback = static fn () => new class {
            /**
             * @return \Illuminate\Support\Collection<int, mixed>
             */
            public function get()
            {
                return collect();
            }
        };

        $response = (new SaoSyncAction())->execute($driver, $callback);

        $this->assertInstanceOf(RedirectResponse::class, $response);
    }

    /**
     * Verifica que també es tanca la sessió si falla l'obtenció de dades.
     */
    public function test_execute_quits_driver_when_callback_throws_exception(): void
    {
        $driver = Mockery::mock(RemoteWebDriver::class);
        $driver->shouldReceive('quit')->once();

        $callback = static fn () => new class {
            /**
             * @return never
             * @throws \Exception
             */
            public function get()
            {
                throw new Exception('Error simulat en callback');
            }
        };

        $response = (new SaoSyncAction())->execute($driver, $callback);

        $this->assertInstanceOf(RedirectResponse::class, $response);
    }
}
