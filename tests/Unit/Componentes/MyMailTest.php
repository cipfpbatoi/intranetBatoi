<?php

namespace Tests\Unit\Componentes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\View;
use Mockery;
use Tests\TestCase;
use Intranet\Componentes\MyMail;
use Intranet\Mail\DocumentRequest;

class MyMailTest extends TestCase
{
    public function testSendMail()
    {
        Mail::fake();


        // 🔹 Mockejar usuari autenticat
        $mockUser = \Mockery::mock(\Illuminate\Foundation\Auth\User::class);
        $mockUser->shouldReceive('getAttribute')->with('email')->andReturn('test@example.com');
        $mockUser->shouldReceive('getAttribute')->with('FullName')->andReturn('Fake User');

        Auth::partialMock();
        Auth::shouldReceive('guard')->andReturnSelf();
        Auth::shouldReceive('user')->andReturn($mockUser);

        // 🔹 Simular un element amb email
        $element = (object) [
            'mail' => 'recipient@example.com',
            'contact' => 'John Doe',
            'id' => 1
        ];

          // 🔹 Crear instància de MyMail
        $mail = new MyMail([$element], 'email.test');

         // 🔹 Executar el mètode
        $mail->send();

         // 🔹 Comprovar que el correu s'ha enviat correctament
        Mail::assertSent(DocumentRequest::class, function ($mail) use ($element) {
            return $mail->hasTo($element->mail);
        });
    }

    public function testRender()
    {
        // 🔹 Mockejar l'usuari autenticat
        $mockUser = Mockery::mock(\Illuminate\Foundation\Auth\User::class);
        $mockUser->shouldReceive('getAttribute')->with('email')->andReturn('test@example.com');
        $mockUser->shouldReceive('getAttribute')->with('FullName')->andReturn('Fake User');

        Auth::partialMock();
        Auth::shouldReceive('guard')->andReturnSelf();
        Auth::shouldReceive('user')->andReturn($mockUser);

        // 🔹 Simula un element amb email
        $element = (object) [
            'mail' => 'recipient@example.com',
            'contact' => 'John Doe',
            'id' => 1
        ];

        // 🔹 Crear instància de MyMail
        $mail = new MyMail([$element], 'email.test');

        // 🔹 Obtenir les dades que es passaran a la vista
        $route = '/test-route';
        $expectedData = [
            'to' => $mail->getFormattedReceivers(),
            'from' => 'test@example.com',
            'subject' => null,
            'contenido' => 'email.test',
            'route' => $route,
            'fromPerson' => 'Fake User',
            'toPeople' => null,
            'class' => get_class($element), // ✅ Ara comparem correctament la classe
            'register' => null,
            'editable' => true,
            'template' => null,
            'action' => 'myMail.send'
        ];

        // 🔹 Mock de la vista
        $mockView = Mockery::mock();
        $mockView->shouldReceive('render')->once()->andReturn('Rendered View');
        $mockView->shouldReceive('__toString')->andReturn('Rendered View');

        // 🔹 Mock de View::make() assegurant que `render()` es crida
        View::shouldReceive('make')
            ->once()
            ->withArgs(function ($view, $data, $mergeData) use ($expectedData) {
                Log::info("📤 Mock de View::make() cridat", ['view' => $view, 'data' => $data]);
                return $view === 'email.view' && $data == $expectedData && $mergeData === [];
            })
            ->andReturn($mockView);

        // 🔹 Executar render()
        $output = $mail->render($route);

        // 🔹 Comprovar que el mètode retorna el resultat esperat
        $this->assertEquals('Rendered View', $output);
    }

}
