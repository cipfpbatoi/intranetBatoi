<?php

namespace Tests\Unit\Services\Mail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\View;
use Mockery;
use Tests\TestCase;
use Intranet\Services\Mail\MyMail;
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



}
