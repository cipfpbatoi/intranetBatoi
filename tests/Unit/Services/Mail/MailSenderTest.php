<?php

namespace Tests\Unit\Services\Mail;

use Illuminate\Support\Facades\Auth;
use Intranet\Exceptions\IntranetException;
use Intranet\Services\Mail\MailSender;
use Intranet\Services\Mail\MyMail;
use Mockery;
use Symfony\Component\Mailer\Exception\TransportException;
use Tests\TestCase;

/**
 * Proves unitàries del servei d'enviament de correu.
 */
class MailSenderTest extends TestCase
{
    /**
     * Comprova que es bloquegen missatges massa grans abans d'arribar a SMTP.
     *
     * @return void
     */
    public function testSendThrowsDomainExceptionWhenEstimatedMessageExceedsLimit(): void
    {
        $this->mockAuthenticatedUser();

        $element = (object) [
            'mail' => 'recipient@example.com',
            'contact' => 'John Doe',
            'id' => 1,
        ];

        $sender = new MailSender(maxMessageBytes: 1024, encodedSizeFactor: 1.0, mimeOverheadBytes: 0);
        $content = str_repeat('<p>Contingut massa gran per al límit de prova.</p>', 80);

        $mail = new MyMail(
            [$element],
            $content,
            ['subject' => 'Prova límit'],
            null,
            null,
            null,
            $sender
        );

        $this->expectException(IntranetException::class);
        $this->expectExceptionMessage('El correu supera el límit estimat de mida');

        $mail->send();
    }

    /**
     * Comprova que els errors 552 del transport es tradueixen a excepció funcional.
     *
     * @return void
     */
    public function testMapTransportExceptionWrapsMaxSizeErrors(): void
    {
        $sender = new MailSender();
        $transportException = new TransportException(
            '552-5.3.4 Your message exceeded Google\'s message size limits. MaxSizeError'
        );

        $mapped = $this->callProtectedMethod($sender, 'mapTransportException', [$transportException]);

        $this->assertInstanceOf(IntranetException::class, $mapped);
        $this->assertSame(422, $mapped->getStatus());
        $this->assertFalse($mapped->shouldNotify());
        $this->assertStringContainsString('massa gran', $mapped->getUserMessage());
    }

    /**
     * Simula un usuari autenticat compatible amb el helper `authUser()`.
     *
     * @return void
     */
    private function mockAuthenticatedUser(): void
    {
        $mockUser = Mockery::mock(\Illuminate\Foundation\Auth\User::class);
        $mockUser->shouldReceive('getAttribute')->with('email')->andReturn('test@example.com');
        $mockUser->shouldReceive('getAttribute')->with('FullName')->andReturn('Fake User');

        Auth::partialMock();
        Auth::shouldReceive('guard')->andReturnSelf();
        Auth::shouldReceive('user')->andReturn($mockUser);
    }
}
