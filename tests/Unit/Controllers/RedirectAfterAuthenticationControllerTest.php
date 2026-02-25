<?php

declare(strict_types=1);

namespace Intranet\Sao {
    use RuntimeException;

    /**
     * Acció SAO dummy per provar flux d'èxit.
     */
    class Testdummy
    {
        public function __construct($digitalSignatureService)
        {
        }

        public function index($driver, $request)
        {
            return redirect('/ok-sao');
        }
    }

    /**
     * Acció SAO dummy per provar flux amb error.
     */
    class Testdummythrow
    {
        public function __construct($digitalSignatureService)
        {
        }

        public function index($driver, $request)
        {
            throw new RuntimeException('Error SAO simulat');
        }
    }
}

namespace Tests\Unit\Controllers {
    use Facebook\WebDriver\Remote\RemoteWebDriver;
    use Illuminate\Auth\GenericUser;
    use Illuminate\Http\RedirectResponse;
    use Intranet\Http\Controllers\RedirectAfterAuthenticationController;
    use Intranet\Http\Requests\PasswordRequest;
    use Mockery;
    use Tests\TestCase;

    /**
     * Tests de caracterització del controlador d'entrada SAO.
     */
    class RedirectAfterAuthenticationControllerTest extends TestCase
    {
        protected function tearDown(): void
        {
            Mockery::close();
            parent::tearDown();
        }

        /**
         * Verifica que en flux correcte es retorna la resposta de l'acció i es tanca el driver.
         */
        public function test_invoke_quits_driver_on_successful_action_execution(): void
        {
            $this->actingAs(new GenericUser([
                'dni' => '12345678A',
            ]), 'profesor');

            $driver = Mockery::mock(RemoteWebDriver::class);
            $driver->shouldReceive('quit')->once();

            $seleniumAlias = Mockery::mock('alias:Intranet\Services\Automation\SeleniumService');
            $seleniumAlias->shouldReceive('loginSAO')->once()->andReturn($driver);

            $request = PasswordRequest::create('/externalAuth', 'POST', [
                'accion' => 'testdummy',
                'password' => 'secret',
            ]);

            $response = app(RedirectAfterAuthenticationController::class)($request);

            $this->assertInstanceOf(RedirectResponse::class, $response);
            $this->assertSame(url('/ok-sao'), $response->getTargetUrl());
        }

        /**
         * Verifica que en error d'acció també es tanca el driver i es torna enrere.
         */
        public function test_invoke_quits_driver_when_action_throws_exception(): void
        {
            $this->actingAs(new GenericUser([
                'dni' => '12345678A',
            ]), 'profesor');

            $driver = Mockery::mock(RemoteWebDriver::class);
            $driver->shouldReceive('quit')->once();

            $seleniumAlias = Mockery::mock('alias:Intranet\Services\Automation\SeleniumService');
            $seleniumAlias->shouldReceive('loginSAO')->once()->andReturn($driver);

            $request = PasswordRequest::create('/externalAuth', 'POST', [
                'accion' => 'testdummythrow',
                'password' => 'secret',
            ]);

            $response = app(RedirectAfterAuthenticationController::class)($request);

            $this->assertInstanceOf(RedirectResponse::class, $response);
            $this->assertSame(url()->previous(), $response->getTargetUrl());
        }
    }
}
