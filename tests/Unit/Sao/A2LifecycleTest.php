<?php

declare(strict_types=1);

namespace Tests\Unit\Sao;

use Facebook\WebDriver\Remote\RemoteWebDriver;
use Illuminate\Auth\GenericUser;
use Illuminate\Http\RedirectResponse;
use Intranet\Sao\SaoDocumentsAction;
use Intranet\Services\Signature\DigitalSignatureService;
use Mockery;
use Tests\TestCase;

/**
 * Tests de caracterització del flux principal d'A2.
 */
class A2LifecycleTest extends TestCase
{
    /**
     * Verifica que el flux principal retorna redirecció quan no hi ha errors.
     */
    public function test_index_returns_back_when_download_process_finishes(): void
    {
        $this->actingAs(
            new GenericUser([
                'dni' => '12345678A',
                'fileName' => 'test-user',
                'fullName' => 'Usuari Test',
            ]),
            'profesor'
        );

        $driver = $this->mockDriverWithTimeoutChain();

        $digitalSignatureService = Mockery::mock(DigitalSignatureService::class);
        $a2 = Mockery::mock(SaoDocumentsAction::class, [$digitalSignatureService])->makePartial();
        $a2->shouldReceive('downloadFilesFromFcts')->once();

        $response = $a2->index($driver, ['A2' => 'on']);

        $this->assertInstanceOf(RedirectResponse::class, $response);
    }

    /**
     * Verifica que el flux captura errors inesperats i torna redirecció.
     */
    public function test_index_returns_back_when_download_process_throws(): void
    {
        $this->actingAs(
            new GenericUser([
                'dni' => '12345678A',
                'fileName' => 'test-user',
                'fullName' => 'Usuari Test',
            ]),
            'profesor'
        );

        $driver = $this->mockDriverWithTimeoutChain();

        $digitalSignatureService = Mockery::mock(DigitalSignatureService::class);
        $a2 = Mockery::mock(SaoDocumentsAction::class, [$digitalSignatureService])->makePartial();
        $a2->shouldReceive('downloadFilesFromFcts')
            ->once()
            ->andThrow(new \RuntimeException('Error simulat'));

        $response = $a2->index($driver, ['A2' => 'on']);

        $this->assertInstanceOf(RedirectResponse::class, $response);
    }

    /**
     * Prepara un mock de driver amb la cadena manage()->timeouts()->pageLoadTimeout().
     */
    private function mockDriverWithTimeoutChain(): RemoteWebDriver
    {
        $timeouts = Mockery::mock();
        $timeouts->shouldReceive('pageLoadTimeout')->once()->with(2);

        $manage = Mockery::mock();
        $manage->shouldReceive('timeouts')->once()->andReturn($timeouts);

        $driver = Mockery::mock(RemoteWebDriver::class);
        $driver->shouldReceive('manage')->once()->andReturn($manage);

        return $driver;
    }
}
