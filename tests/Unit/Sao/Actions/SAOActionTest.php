<?php

declare(strict_types=1);

namespace Intranet\Sao {
    /**
     * Accio legacy dummy per provar el fallback de SAOAction.
     */
    class Legacydummy
    {
        public function __construct($digitalSignatureService)
        {
        }

        public function index($driver, $requestData, $file = null)
        {
            return 'legacy-ok';
        }
    }
}

namespace Tests\Unit\Sao\Actions {
    use Facebook\WebDriver\Remote\RemoteWebDriver;
    use Intranet\Sao\Actions\SAOAction;
    use Mockery;
    use RuntimeException;
    use Tests\TestCase;

    /**
     * Tests de caracterització del dispatcher SAOAction.
     */
    class SAOActionTest extends TestCase
    {
        /**
         * Verifica que l'accio "importa" delega en SaoImportaAction::index.
         */
        public function test_index_dispatches_importa_action(): void
        {
            $driver = Mockery::mock(RemoteWebDriver::class);

            $importaAlias = Mockery::mock('alias:Intranet\Sao\SaoImportaAction');
            $importaAlias->shouldReceive('index')
                ->once()
                ->with($driver)
                ->andReturn('importa-ok');

            $result = (new SAOAction())->index($driver, ['accion' => 'importa']);

            $this->assertSame('importa-ok', $result);
        }

        /**
         * Verifica que una accio desconeguda llança excepcio.
         */
        public function test_index_throws_when_action_is_unknown(): void
        {
            $driver = Mockery::mock(RemoteWebDriver::class);

            $this->expectException(RuntimeException::class);
            $this->expectExceptionMessage("No existeix cap accio SAO registrada");

            (new SAOAction())->index($driver, ['accion' => 'inexistent']);
        }

        /**
         * Verifica que el fallback legacy executa classes no migrades.
         */
        public function test_index_executes_legacy_action_fallback(): void
        {
            $driver = Mockery::mock(RemoteWebDriver::class);

            $result = (new SAOAction())->index($driver, ['accion' => 'legacydummy']);

            $this->assertSame('legacy-ok', $result);
        }
    }
}
