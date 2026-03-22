<?php

declare(strict_types=1);

namespace Tests\Unit\Controllers {
    use Illuminate\Auth\GenericUser;
    use Illuminate\Http\RedirectResponse;
    use Intranet\Http\Controllers\RedirectAfterAuthenticationController;
    use Intranet\Http\Requests\PasswordRequest;
    use Intranet\Sao\Support\SaoRunner;
    use Mockery;
    use RuntimeException;
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

            $runner = Mockery::mock(SaoRunner::class);
            $runner->shouldReceive('run')
                ->once()
                ->andReturn(redirect('/ok-sao'));

            $request = PasswordRequest::create('/externalAuth', 'POST', [
                'accion' => 'a2',
                'password' => 'secret',
            ]);

            $response = (new RedirectAfterAuthenticationController($runner))($request);

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

            $runner = Mockery::mock(SaoRunner::class);
            $runner->shouldReceive('run')
                ->once()
                ->andThrow(new RuntimeException('Error SAO simulat'));

            $request = PasswordRequest::create('/externalAuth', 'POST', [
                'accion' => 'a2',
                'password' => 'secret',
            ]);

            $response = (new RedirectAfterAuthenticationController($runner))($request);

            $this->assertInstanceOf(RedirectResponse::class, $response);
            $this->assertSame(url()->previous(), $response->getTargetUrl());
        }
    }
}
